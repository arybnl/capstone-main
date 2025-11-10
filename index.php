<?php
// Start a PHP session
session_start();

// Include database configuration, email configuration, and rate limiter
require_once 'config.php';
require_once 'email_config.php';
require_once 'rate_limiter.php';

// Function to generate secure token for password reset
function generateSecureToken() {
    return bin2hex(random_bytes(32));
}

// Function to verify token validity from database
function verifyResetToken($conn, $token) {
    $sql = "SELECT email, expires_at FROM password_reset_otps 
            WHERE reset_token = ? AND used = 0 AND expires_at > NOW()";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $stmt->close();
            return [
                'valid' => true,
                'email' => $row['email']
            ];
        }
        $stmt->close();
    }
    
    return ['valid' => false];
}

// Initialize rate limiter
$rateLimiter = new PasswordResetRateLimiter($conn);

// Initialize variables for messages
$login_error = '';
$register_error = '';
$register_success = '';
$forgot_password_message = '';
$set_new_password_message = '';
$email_sent_for_otp = false;
$otp_cooldown_seconds = 0;
$show_resend_timer = false;

// --- Handle User Logout ---
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header('Location: index.php');
    exit();
}

// --- Check if user is already logged in ---
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_type']) {
        case 'member':
            header('Location: Clients/dashboard.php');
            exit();
        case 'trainer':
            header('Location: Trainers/Trainer_Dashboard.php');
            exit();
        case 'admin':
            header('Location: Admin/Admin_Dashboard.php');
            exit();
    }
}

// --- Handle Login Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $login_error = 'Please enter both email and password.';
    } else {
        $sql = "SELECT user_id, email, password_hash, user_type FROM Users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $param_email);
            $param_email = $email;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($user_id, $db_email, $password_hash, $user_type);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $password_hash)) {
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['email'] = $db_email;
                            $_SESSION['user_type'] = $user_type;

                            switch ($user_type) {
                                case 'member':
                                    header('Location: Clients/dashboard.php');
                                    exit();
                                case 'trainer':
                                    header('Location: Trainers/Trainer_Dashboard.php');
                                    exit();
                                case 'admin':
                                    header('Location: Admin/Admin_Dashboard.php');
                                    exit();
                                default:
                                    $login_error = 'Unknown user type. Please contact support.';
                                    break;
                            }
                        } else {
                            $login_error = 'The password you entered was not valid.';
                        }
                    }
                } else {
                    $login_error = 'No account found with that email address.';
                }
            } else {
                $login_error = 'Oops! Something went wrong. Please try again later.';
            }
            $stmt->close();
        }
    }
}

// --- Handle Register Form Submission with Strong Password Policy ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_submit'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $register_error = 'Please fill in all fields.';
    } elseif ($password !== $confirm_password) {
        $register_error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $register_error = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $register_error = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $register_error = 'Password must contain at least one lowercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $register_error = 'Password must contain at least one number.';
    } else {
        $sql_check = "SELECT user_id FROM Users WHERE email = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param('s', $param_email);
            $param_email = $email;
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $register_error = 'This email is already registered.';
            }
            $stmt_check->close();
        }

        if (empty($register_error)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $user_id = generate_uuid();
            $first_name = explode(' ', $full_name)[0];
            $last_name = implode(' ', array_slice(explode(' ', $full_name), 1));
            $user_type = 'member';

            $sql_user = "INSERT INTO Users (user_id, email, password_hash, first_name, last_name, user_type) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt_user = $conn->prepare($sql_user)) {
                $stmt_user->bind_param('ssssss', $user_id, $email, $hashed_password, $first_name, $last_name, $user_type);
                if ($stmt_user->execute()) {
                    $member_id = generate_uuid();
                    $sql_member = "INSERT INTO Members (member_id, user_id) VALUES (?, ?)";
                    if ($stmt_member = $conn->prepare($sql_member)) {
                        $stmt_member->bind_param('ss', $member_id, $user_id);
                        $stmt_member->execute();
                        $stmt_member->close();
                    }
                    $register_success = 'Account created successfully! You can now log in.';
                    $_POST = array();
                } else {
                    $register_error = 'Something went wrong with user registration: ' . $stmt_user->error;
                }
                $stmt_user->close();
            }
        }
    }
}

// --- Handle Forgot Password (Send OTP via Email) with RATE LIMITING ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot_password_submit'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $forgot_password_message = 'Please enter your email address.';
    } else {
        // ✅ CHECK RATE LIMIT BEFORE PROCESSING
        $rateCheck = $rateLimiter->checkRateLimit($email);
        
        if (!$rateCheck['allowed']) {
            $forgot_password_message = $rateCheck['message'];
        } else {
            // ✅ ALWAYS show same message - prevents email enumeration
            $generic_message = 'If that email address is registered, an OTP code has been sent. Please check your email.';
            
            $sql = "SELECT user_id, first_name FROM Users WHERE email = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('s', $param_email);
                $param_email = $email;
                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows == 1) {
                        // Email exists - send OTP
                        $stmt->bind_result($user_id, $first_name);
                        $stmt->fetch();
                        
                        $otp = generateOTP();
                        $expires_at = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));
                        
                        // Delete old OTPs
                        $delete_old = "DELETE FROM password_reset_otps WHERE email = ?";
                        if ($stmt_delete = $conn->prepare($delete_old)) {
                            $stmt_delete->bind_param('s', $email);
                            $stmt_delete->execute();
                            $stmt_delete->close();
                        }
                        
                        // Insert new OTP
                        $sql_otp = "INSERT INTO password_reset_otps (email, otp_code, expires_at) VALUES (?, ?, ?)";
                        if ($stmt_otp = $conn->prepare($sql_otp)) {
                            $stmt_otp->bind_param('sss', $email, $otp, $expires_at);
                            if ($stmt_otp->execute()) {
                                $email_sent = sendOTPEmail($email, $otp, $first_name);
                                
                                if ($email_sent) {
                                    $_SESSION['reset_email'] = $email;
                                    $email_sent_for_otp = true;
                                    $otp_cooldown_seconds = 60;
                                    $show_resend_timer = true;
                                } else {
                                    error_log("Failed to send OTP to: $email");
                                }
                            }
                            $stmt_otp->close();
                        }
                    } else {
                        // Email doesn't exist - log it but show same message
                        error_log("Password reset attempt for non-existent email: $email");
                    }
                    
                    // Show generic message regardless
                    $forgot_password_message = $generic_message;
                } else {
                    $forgot_password_message = 'Oops! Something went wrong. Please try again later.';
                }
                $stmt->close();
            }
        }
    }
}

// --- Handle Resend OTP with COOLDOWN TIMER and RATE LIMITING ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resend_otp_submit'])) {
    $email = trim($_POST['email_for_otp']);
    
    if (!empty($email)) {
        // Check if there's a recent OTP sent (within last 60 seconds)
        $sql_check_recent = "SELECT created_at FROM password_reset_otps 
                            WHERE email = ? 
                            ORDER BY created_at DESC LIMIT 1";
        
        if ($stmt_check = $conn->prepare($sql_check_recent)) {
            $stmt_check->bind_param('s', $email);
            $stmt_check->execute();
            $stmt_check->bind_result($last_otp_time);
            $stmt_check->fetch();
            $stmt_check->close();
            
            if ($last_otp_time) {
                $time_since_last = time() - strtotime($last_otp_time);
                $cooldown_period = 60; // 60 seconds cooldown
                
                if ($time_since_last < $cooldown_period) {
                    // Still in cooldown period
                    $otp_cooldown_seconds = $cooldown_period - $time_since_last;
                    $forgot_password_message = 'Please wait ' . $otp_cooldown_seconds . ' seconds before requesting a new OTP.';
                    $show_resend_timer = true;
                    $email_sent_for_otp = true;
                } else {
                    // Cooldown expired, check rate limit
                    $rateCheck = $rateLimiter->checkRateLimit($email);
                    
                    if (!$rateCheck['allowed']) {
                        $forgot_password_message = $rateCheck['message'];
                        $email_sent_for_otp = false;
                    } else {
                        // Proceed with resending OTP
                        $sql = "SELECT user_id, first_name FROM Users WHERE email = ?";
                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param('s', $email);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                                if ($stmt->num_rows == 1) {
                                    $stmt->bind_result($user_id, $first_name);
                                    $stmt->fetch();
                                    
                                    $otp = generateOTP();
                                    $expires_at = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));
                                    
                                    $delete_old = "DELETE FROM password_reset_otps WHERE email = ?";
                                    if ($stmt_delete = $conn->prepare($delete_old)) {
                                        $stmt_delete->bind_param('s', $email);
                                        $stmt_delete->execute();
                                        $stmt_delete->close();
                                    }
                                    
                                    $sql_otp = "INSERT INTO password_reset_otps (email, otp_code, expires_at) VALUES (?, ?, ?)";
                                    if ($stmt_otp = $conn->prepare($sql_otp)) {
                                        $stmt_otp->bind_param('sss', $email, $otp, $expires_at);
                                        if ($stmt_otp->execute()) {
                                            if (sendOTPEmail($email, $otp, $first_name)) {
                                                $forgot_password_message = 'A new OTP code has been sent to your email.';
                                                $email_sent_for_otp = true;
                                                $otp_cooldown_seconds = 60;
                                                $show_resend_timer = true;
                                            } else {
                                                $forgot_password_message = 'Failed to resend OTP. Please try again.';
                                            }
                                        }
                                        $stmt_otp->close();
                                    }
                                }
                            }
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }
}

// --- Handle Verify OTP with Enhanced Security and Token Generation ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp_submit'])) {
    $otp_code = trim($_POST['otp_code']);
    $email = trim($_POST['email_for_otp']);
    
    // Input validation
    if (empty($otp_code)) {
        $forgot_password_message = 'Please enter the OTP code.';
    } else if (empty($email)) {
        $forgot_password_message = 'Email address is required.';
    } else if (strlen($otp_code) != 6 || !is_numeric($otp_code)) {
        $forgot_password_message = 'Please enter a valid 6-digit OTP code.';
    } else {
        // Initialize attempt tracking
        if (!isset($_SESSION['otp_attempts'])) {
            $_SESSION['otp_attempts'] = 0;
            $_SESSION['otp_attempt_time'] = time();
        }
        
        // Check for too many failed attempts
        if ($_SESSION['otp_attempts'] >= 5) {
            $time_since_last = time() - $_SESSION['otp_attempt_time'];
            if ($time_since_last < 300) {
                $wait_time = ceil((300 - $time_since_last) / 60);
                $forgot_password_message = "Too many failed attempts. Please wait {$wait_time} minute(s) before trying again.";
            } else {
                $_SESSION['otp_attempts'] = 0;
            }
        }
        
        if (empty($forgot_password_message)) {
            $sql = "SELECT id, expires_at, used, created_at 
                    FROM password_reset_otps 
                    WHERE email = ? AND otp_code = ? 
                    ORDER BY created_at DESC 
                    LIMIT 1";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('ss', $email, $otp_code);
                
                if ($stmt->execute()) {
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 1) {
                        $stmt->bind_result($otp_id, $expires_at, $used, $created_at);
                        $stmt->fetch();
                        
                        $current_time = time();
                        $expiry_time = strtotime($expires_at);
                        $creation_time = strtotime($created_at);
                        
                        if ($used == 1) {
                            $forgot_password_message = 'This OTP has already been used. Please request a new one.';
                            $_SESSION['otp_attempts']++;
                        } 
                        else if ($expiry_time < $current_time) {
                            $forgot_password_message = 'OTP has expired. Please request a new one.';
                            $_SESSION['otp_attempts']++;
                            
                            $delete_sql = "DELETE FROM password_reset_otps WHERE id = ?";
                            if ($delete_stmt = $conn->prepare($delete_sql)) {
                                $delete_stmt->bind_param('i', $otp_id);
                                $delete_stmt->execute();
                                $delete_stmt->close();
                            }
                        }
                        else if (($current_time - $creation_time) > 3600) {
                            $forgot_password_message = 'OTP has expired. Please request a new one.';
                            $_SESSION['otp_attempts']++;
                        }
                        else {
                            if (isset($_SESSION['reset_email']) && $_SESSION['reset_email'] !== $email) {
                                $forgot_password_message = 'Email mismatch. Please restart the password reset process.';
                                $_SESSION['otp_attempts']++;
                            } else {
                                // ✅ SUCCESS - Generate secure token
                                $reset_token = generateSecureToken();
                                $token_expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                                
                                // Update database with token
                                $update_sql = "UPDATE password_reset_otps 
                                              SET reset_token = ?, token_expires_at = ? 
                                              WHERE id = ?";
                                if ($update_stmt = $conn->prepare($update_sql)) {
                                    $update_stmt->bind_param('ssi', $reset_token, $token_expires, $otp_id);
                                    $update_stmt->execute();
                                    $update_stmt->close();
                                }
                                
                                $forgot_password_message = 'OTP verified successfully! Please set your new password.';
                                
                                // Set secure session variables
                                $_SESSION['reset_email'] = $email;
                                $_SESSION['reset_token'] = $reset_token;
                                $_SESSION['otp_verified'] = true;
                                $_SESSION['otp_id'] = $otp_id;
                                $_SESSION['otp_verified_at'] = time();
                                
                                // Reset attempt counter
                                unset($_SESSION['otp_attempts']);
                                unset($_SESSION['otp_attempt_time']);
                                
                                error_log("OTP verified successfully for email: $email");
                            }
                        }
                    } else {
                        $forgot_password_message = 'Invalid OTP code. Please check and try again.';
                        $_SESSION['otp_attempts']++;
                        $_SESSION['otp_attempt_time'] = time();
                        
                        error_log("Failed OTP verification attempt for email: $email");
                    }
                } else {
                    $forgot_password_message = 'Error verifying OTP. Please try again.';
                    error_log("Database error during OTP verification: " . $stmt->error);
                }
                
                $stmt->close();
            } else {
                $forgot_password_message = 'System error. Please try again later.';
                error_log("Failed to prepare OTP verification statement: " . $conn->error);
            }
        }
    }
}

// --- Handle Set New Password with Token Verification ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['set_new_password_submit'])) {
    $new_password = trim($_POST['new_password']);
    $confirm_new_password = trim($_POST['confirm_new_password']);
    $reset_token = $_SESSION['reset_token'] ?? '';
    $reset_email = $_SESSION['reset_email'] ?? '';
    $otp_id = $_SESSION['otp_id'] ?? 0;
    $otp_verified_at = $_SESSION['otp_verified_at'] ?? 0;

    // ✅ CRITICAL: Verify token exists
    if (empty($reset_token) || empty($reset_email) || !isset($_SESSION['otp_verified'])) {
        $set_new_password_message = 'Invalid reset attempt. Please restart the forgot password process.';
        
        // Clear invalid session
        unset($_SESSION['reset_email'], $_SESSION['reset_token'], 
              $_SESSION['otp_verified'], $_SESSION['otp_id'], 
              $_SESSION['otp_verified_at']);
    }
    // Verify session hasn't expired (15 minutes)
    else if ((time() - $otp_verified_at) > 900) {
        $set_new_password_message = 'Password reset session expired. Please request a new OTP.';
        
        unset($_SESSION['reset_email'], $_SESSION['reset_token'], 
              $_SESSION['otp_verified'], $_SESSION['otp_id'], 
              $_SESSION['otp_verified_at']);
    }
    // Validate password
    else if (empty($new_password) || empty($confirm_new_password)) {
        $set_new_password_message = 'Please enter and confirm your new password.';
    } 
    else if ($new_password !== $confirm_new_password) {
        $set_new_password_message = 'New passwords do not match.';
    } 
    else if (strlen($new_password) < 8) {
        $set_new_password_message = 'New password must be at least 8 characters long.';
    } 
    else if (!preg_match('/[A-Z]/', $new_password)) {
        $set_new_password_message = 'Password must contain at least one uppercase letter.';
    } 
    else if (!preg_match('/[a-z]/', $new_password)) {
        $set_new_password_message = 'Password must contain at least one lowercase letter.';
    } 
    else if (!preg_match('/[0-9]/', $new_password)) {
        $set_new_password_message = 'Password must contain at least one number.';
    }
    else {
        // ✅ Verify token in database
        $verify_sql = "SELECT used, expires_at, token_expires_at 
                      FROM password_reset_otps 
                      WHERE id = ? AND reset_token = ? AND email = ?";
        
        if ($verify_stmt = $conn->prepare($verify_sql)) {
            $verify_stmt->bind_param('iss', $otp_id, $reset_token, $reset_email);
            $verify_stmt->execute();
            $verify_stmt->bind_result($otp_used, $otp_expires, $token_expires);
            $verify_stmt->fetch();
            $verify_stmt->close();
            
            // Check token validity
            if (empty($token_expires)) {
                $set_new_password_message = 'Invalid reset token. Please request a new OTP.';
            }
            else if ($otp_used == 1) {
                $set_new_password_message = 'This reset link has already been used. Please request a new one.';
            } 
            else if (strtotime($otp_expires) < time() || strtotime($token_expires) < time()) {
                $set_new_password_message = 'Password reset link has expired. Please request a new one.';
            }
            else {
                // ✅ All checks passed - Update password
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE Users SET password_hash = ?, updated_at = NOW() WHERE email = ?";
                
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param('ss', $hashed_new_password, $reset_email);
                    
                    if ($stmt->execute()) {
                        // Mark OTP as used
                        $sql_update_otp = "UPDATE password_reset_otps 
                                          SET used = 1, reset_token = NULL 
                                          WHERE id = ?";
                        if ($stmt_update = $conn->prepare($sql_update_otp)) {
                            $stmt_update->bind_param('i', $otp_id);
                            $stmt_update->execute();
                            $stmt_update->close();
                        }
                        
                        // Clear rate limit
                        $rateLimiter->clearRateLimit($reset_email);

                        // ✅ Get user details for notification email
                        $user_sql = "SELECT first_name FROM Users WHERE email = ?";
                        $user_name = '';

                        if ($user_stmt = $conn->prepare($user_sql)) {
                            $user_stmt->bind_param('s', $reset_email);
                            $user_stmt->execute();
                            $user_stmt->bind_result($first_name);
                            if ($user_stmt->fetch()) {
                                $user_name = $first_name;
                            }
                            $user_stmt->close();
                        }

                        // ✅ SEND PASSWORD CHANGE NOTIFICATION EMAIL
                        $notification_details = [
                            'ip_address' => getClientIP(),
                            'timestamp' => date('F j, Y \a\t g:i A'),
                            'location' => 'Santa Rosa, Laguna, Philippines',
                            'device' => getDeviceInfo()
                        ];

                        $notification_sent = sendPasswordChangeNotification(
                            $reset_email, 
                            $user_name, 
                            $notification_details
                        );

                        if (!$notification_sent) {
                            error_log("Failed to send password change notification to: $reset_email");
                        }
                        
                        // Clear ALL session variables
                        unset($_SESSION['reset_email']);
                        unset($_SESSION['reset_token']);
                        unset($_SESSION['otp_verified']);
                        unset($_SESSION['otp_id']);
                        unset($_SESSION['otp_verified_at']);
                        unset($_SESSION['otp_attempts']);
                        unset($_SESSION['otp_attempt_time']);
                        
                        $set_new_password_message = 'Your password has been reset successfully! A confirmation email has been sent. You can now log in.';
                        
                        error_log("Password reset successful for email: $reset_email");
                    } else {
                        $set_new_password_message = 'Oops! Something went wrong updating your password.';
                        error_log("Password update failed: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    $set_new_password_message = 'System error. Please try again later.';
                    error_log("Failed to prepare password update statement: " . $conn->error);
                }
            }
        } else {
            $set_new_password_message = 'System error. Please try again later.';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Triple 3 Fitness & Martial Arts Gym</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="icon" type="image/png" href="img/t3-logo.png">
<style>
/* Add these styles for the OTP timer */
.resend-timer {
    display: inline-block;
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 5px;
    padding: 10px 15px;
    margin: 10px 0;
    font-weight: bold;
    color: #856404;
}

.resend-timer .countdown {
    font-size: 18px;
    color: #ff6600;
    font-weight: bold;
}

.resend-btn-disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
    pointer-events: none;
}

.resend-btn-enabled {
    opacity: 1;
    cursor: pointer;
}

.password-requirements {
    background: #e7f3ff;
    border-left: 4px solid #2196F3;
    padding: 10px 15px;
    margin: 10px 0;
    font-size: 13px;
    color: #0d47a1;
}

.password-requirements ul {
    margin: 5px 0;
    padding-left: 20px;
}

.password-requirements li {
    margin: 3px 0;
}
</style>
</head>

<body>
    <header class="navbar">
        <div class="container">
        <div class="logo">
        <img src="img/logo.png" alt="Triple 3 Logo">
        </div>
        <nav>
        <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About Us</a></li>
        <li><a href="#services">Our Services</a></li>
        <li><a href="#contact">Contact Us</a></li>
        </ul>
        </nav>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <button class="login-btn" id="openLogin">Login</button>
        <?php else: ?>
            <button class="login-btn" onclick="window.location.href='index.php?logout=true'">Logout</button>
        <?php endif; ?>
        </div>
    </header>

<section class="hero" id="home">
    <div class="container">
        <div class="hero-content">
            <h1>Get Fit. Get Fierce.</h1>
            <h2>Get Started Today with Us!</h2>
            <p>From beginners to pros—we've got your back. Our resources and expert guidance support you at every stage, helping you enhance your skills and boost your confidence. Join us to unlock your potential!</p>
            <a href="#contact" class="btn">Inquire Now >></a>
        </div>
        <div class="hero-image">
            <img src="img/hero.png" alt="Man working out with dumbbells">
        </div>
    </div>
</section>

<section class="about-section" id="about">
    <div class="container">
        <div class="about-image">
            <img src="img/training.png" alt="People working out">
        </div>
        <div class="about-content">
            <h3>About Us</h3>
            <h2>YOUR FITNESS GOALS, OUR MISSION.</h2>
            <p>Welcome to Triple 3 Fitness and Martial Arts Gym - where dedication drives results. Whether you're just starting out or pushing toward new goals, we've got the tools, trainers, and community to support your journey. Start today and become your strongest self.</p>
            <div class="stats">
                <div class="stat-item">
                    <span>5+</span>
                    <p>Years of Excellence</p>
                </div>
                <div class="stat-item">
                    <span>80+</span>
                    <p>Active Members</p>
                </div>
            </div>
            <div class="gym-location">
                <h4>Gym Location:</h4>
                <p>3rd Floor A3R3 Fitness and Martial Arts Gym, Amethyst street, Balibago Complex, Brgy. Balibago Sta. Rosa Laguna, Santa Rosa, Philippines</p>
            </div>
        </div>
    </div>
</section>

<section class="services-section" id="services">
    <div class="container">
        <h2>Our Services</h2>
        <div class="service-grid">
            <div class="service-card">
                <i class="fas fa-dumbbell icon"></i>
                <h3>Body Building Program</h3>
                <p>Strength training involves exercising with your own bodyweight or using devices like dumbbells or machines to build muscle, burn fat, and gain strength. It challenges your body to grow, adapt, and become more fit.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-heartbeat icon"></i>
                <h3>High-Intensity Interval Training (HIIT) Program</h3>
                <p>Consists of short bursts of intense work that typically last between 15 seconds to 4 minutes. These are followed by a quick recovery period and then right back to the tough work.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-boxing-glove icon"></i>
                <h3>Boxing Class</h3>
                <p>Focus on learning fundamental techniques, including proper stance, footwork, and basic punches like jabs, crosses, hooks, and uppercuts.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-hand-rock icon"></i>
                <h3>Muay Thai Class</h3>
                <p>Each session lasts from 1 to 2 hours and incorporates multiple rounds of shadow boxing, heavy bagwork, padwork, strength training and conditioning exercises.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-fist-raised icon"></i>
                <h3>Mixed Martial Arts (MMA) Class</h3>
                <p>Combines techniques from various martial arts like boxing, Brazilian Jiu-Jitsu, Muay Thai, and wrestling, to develop a comprehensive fighting skill set.</p>
            </div>
        </div>
    </div>
</section>

<section class="contact-section" id="contact">
    <div class="container">
        <div class="contact-info">
            <h2>Contact Us</h2>
            <h3>Let's talk!</h3>
            <p>Got questions, comments, or just want to say hi? Send us a message and we'll get back to you shortly. We'd love to hear from you!</p>
            <p><i class="fas fa-map-marker-alt icon"></i> Triple 3 Fitness and Martial Arts Gym</p>
            <p><i class="fas fa-envelope icon"></i> triple3gym@yahoo.com</p> <!-- need to change -->
            <p><i class="fas fa-phone icon"></i> 0991 572 9696</p> <!-- need to change -->
        </div>
        <div class="contact-form-container">
            <div class="contact-form">
                <h3>Get in touch</h3>
                <form action="#" method="POST">
                    <input type="text" name="contact_name" placeholder="Your Name" required>
                    <input type="email" name="contact_email" placeholder="Your Email" required>
                    <select name="contact_service" required>
                        <option value="">Services You Need</option>
                        <option value="Body Building Program">Body Building Program</option>
                        <option value="HIIT Program">HIIT Program</option>
                        <option value="Boxing Class">Boxing Class</option>
                        <option value="Muay Thai Class">Muay Thai Class</option>
                        <option value="MMA Class">MMA Class</option>
                    </select>
                    <textarea name="contact_message" placeholder="Please tell us about what you need..."></textarea>
                    <button type="submit" name="contact_submit" class="btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-left">
            <img src="img/logo.png" alt="Triple 3 Logo">
            <p>3rd Floor A3R3 Fitness and Martial Arts Gym, Amethyst street, Balibago Complex, Brgy. Balibago Sta. Rosa Laguna, Santa Rosa, Philippines</p>
        </div>
        <div class="footer-right">
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#services">Our Services</a></li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>
            <div class="footer-contact">
                <p><i class="fas fa-envelope icon"></i> triple3gym@yahoo.com</p>
                <p><i class="fas fa-phone icon"></i> 0991 572 9696</p>
            </div>
        </div>
    </div>
</footer>

<!-- Login/Signup Modals -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>

        <!-- Login Popup -->
        <div class="login-popup <?php if (!empty($login_error) || (!empty($register_success) && empty($register_error) && empty($forgot_password_message) && empty($set_new_password_message))) echo 'active'; elseif (empty($register_error) && empty($forgot_password_message) && empty($set_new_password_message) && !isset($_SESSION['otp_verified'])) echo 'active'; ?>" id="loginPopup">
            <h2>Login</h2>
            <?php if (!empty($login_error)) echo '<p style="color: red;">' . $login_error . '</p>'; ?>
            <?php if (!empty($register_success)) echo '<p style="color: green;">' . $register_success . '</p>'; ?>
            <?php if (!empty($set_new_password_message) && strpos($set_new_password_message, 'successfully') !== false) echo '<p style="color: green;">' . $set_new_password_message . '</p>'; ?>
            <form action="index.php" method="POST">
                <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login_submit" class="btn login-btn-popup">Login</button>
            </form>
            <p><a href="#" id="showCreateAccount">Create Account</a> | <a href="#" id="showForgotPassword">Forgot Password?</a></p>
        </div>

        <!-- Create Account Popup -->
        <div class="create-account-popup <?php if (!empty($register_error)) echo 'active'; ?>" id="createAccountPopup">
            <h2>Create Account</h2>
            <?php if (!empty($register_error)) echo '<p style="color: red;">' . $register_error . '</p>'; ?>
            
            <div class="password-requirements">
                <strong>🔒 Password Requirements:</strong>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>One uppercase letter (A-Z)</li>
                    <li>One lowercase letter (a-z)</li>
                    <li>One number (0-9)</li>
                </ul>
            </div>
            
            <form action="index.php" method="POST">
                <input type="text" name="full_name" placeholder="Full Name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" name="register_submit" class="btn">Create Account</button>
            </form>
            <p><a href="#" id="backToLoginFromCreate">Back to Login</a></p>
        </div>

        <!-- Forgot Password Popup -->
        <div class="forgot-password-popup <?php if (!empty($forgot_password_message) && !$email_sent_for_otp && !isset($_SESSION['otp_verified'])) echo 'active'; ?>" id="forgotPasswordPopup">
            <h2>Forgot Password</h2>
            <p>Enter your registered email address and we'll send you an OTP to reset your password.</p>
            <?php if (!empty($forgot_password_message) && !$email_sent_for_otp) echo '<p style="color: red;">' . $forgot_password_message . '</p>'; ?>
            <form action="index.php" method="POST">
                <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                <button type="submit" name="forgot_password_submit" class="btn" id="sendOtpBtn">Send OTP</button>
            </form>
            <p><a href="#" id="backToLoginFromForgot">Back to Login</a></p>
        </div>

        <!-- Verify Code Popup with Timer -->
        <div class="verify-code-popup <?php if ($email_sent_for_otp || (!empty($_SESSION['reset_email']) && !isset($_SESSION['otp_verified']))) echo 'active'; ?>" id="verifyCodePopup">
            <h2>Verify Code</h2>
            <p>Enter the 6-digit OTP code sent to your registered email.</p>

            <?php if (isset($_SESSION['otp_attempts']) && $_SESSION['otp_attempts'] > 0): ?>
    <p style="color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; font-size: 13px;">
        ⚠️ <strong>Warning:</strong> <?php echo (5 - $_SESSION['otp_attempts']); ?> attempt(s) remaining before temporary lockout.
    </p>
<?php endif; ?>
            
            <?php if ($email_sent_for_otp && strpos($forgot_password_message, 'sent') !== false): ?>
                <p style="color: green;"><?php echo $forgot_password_message; ?></p>
            <?php endif; ?>
            
            <?php if (!$email_sent_for_otp && !empty($forgot_password_message) && strpos($forgot_password_message, 'verified') === false && strpos($forgot_password_message, 'sent') === false): ?>
                <p style="color: red;"><?php echo $forgot_password_message; ?></p>
            <?php endif; ?>
            
            <?php if ($show_resend_timer && $otp_cooldown_seconds > 0): ?>
                <div class="resend-timer" id="resendTimerDisplay">
                    ⏱️ You can resend OTP in <span class="countdown" id="countdownTimer"><?php echo $otp_cooldown_seconds; ?></span> seconds
                </div>
            <?php endif; ?>
            
            <form action="index.php" method="POST" id="verifyOtpForm">
                <input type="text" name="otp_code" placeholder="Enter 6-digit code" required maxlength="6" pattern="[0-9]{6}">
                <input type="hidden" name="email_for_otp" value="<?php echo htmlspecialchars($_SESSION['reset_email'] ?? $_POST['email'] ?? ''); ?>">
                <button type="submit" name="verify_otp_submit" class="btn" id="verifyOtpBtn">Verify OTP</button>
            </form>
            
            <form action="index.php" method="POST" id="resendOtpForm" style="display: inline;">
                <input type="hidden" name="email_for_otp" value="<?php echo htmlspecialchars($_SESSION['reset_email'] ?? $_POST['email'] ?? ''); ?>">
                <p>
                    <button 
                        type="submit" 
                        name="resend_otp_submit" 
                        id="resendOtpBtn"
                        class="<?php echo ($show_resend_timer && $otp_cooldown_seconds > 0) ? 'resend-btn-disabled' : 'resend-btn-enabled'; ?>"
                        style="background:none; border:none; color:#ff6600; cursor:pointer; text-decoration:underline;"
                        <?php echo ($show_resend_timer && $otp_cooldown_seconds > 0) ? 'disabled' : ''; ?>
                    >
                        Resend OTP
                    </button> 
                    | <a href="#" id="backToLoginFromVerify">Back to Login</a>
                </p>
            </form>
        </div>

        <!-- Set New Password Popup -->
        <div class="set-new-password-popup <?php if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']) echo 'active'; ?>" id="setNewPasswordPopup">
            <h2>Set New Password</h2>
            <?php if (!empty($forgot_password_message) && strpos($forgot_password_message, 'verified') !== false) echo '<p style="color: green;">' . $forgot_password_message . '</p>'; ?>
            <?php if (!empty($set_new_password_message) && strpos($set_new_password_message, 'successfully') === false) echo '<p style="color: red;">' . $set_new_password_message . '</p>'; ?>
            
            <div class="password-requirements">
                <strong>🔒 Password Requirements:</strong>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>One uppercase letter (A-Z)</li>
                    <li>One lowercase letter (a-z)</li>
                    <li>One number (0-9)</li>
                </ul>
            </div>
            
            <form action="index.php" method="POST">
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_new_password" placeholder="Confirm New Password" required>
                <button type="submit" name="set_new_password_submit" class="btn">Change Password</button>
            </form>
            <p><a href="#" id="backToLoginFromSetNew">Back to Login</a></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginModal = document.getElementById('loginModal');
        const openLoginBtn = document.getElementById('openLogin');
        const closeBtn = document.querySelector('.close-btn');

        const loginPopup = document.getElementById('loginPopup');
        const createAccountPopup = document.getElementById('createAccountPopup');
        const forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
        const verifyCodePopup = document.getElementById('verifyCodePopup');
        const setNewPasswordPopup = document.getElementById('setNewPasswordPopup');

        const showCreateAccountBtn = document.getElementById('showCreateAccount');
        const showForgotPasswordBtn = document.getElementById('showForgotPassword');
        const backToLoginFromCreateBtn = document.getElementById('backToLoginFromCreate');
        const backToLoginFromForgotBtn = document.getElementById('backToLoginFromForgot');
        const backToLoginFromVerifyBtn = document.getElementById('backToLoginFromVerify');
        const backToLoginFromSetNewBtn = document.getElementById('backToLoginFromSetNew');

        // Function to show a specific popup and hide others
        function showPopup(popupToShow) {
            [loginPopup, createAccountPopup, forgotPasswordPopup, verifyCodePopup, setNewPasswordPopup].forEach(popup => {
                popup.classList.remove('active');
            });
            popupToShow.classList.add('active');
            loginModal.style.display = 'flex';
        }

        // Open Login Modal - only if not already open by PHP for errors
        <?php if (empty($login_error) && empty($register_error) && empty($register_success) && empty($forgot_password_message) && empty($set_new_password_message) && !isset($_SESSION['otp_verified'])): ?>
            if (openLoginBtn) {
                openLoginBtn.addEventListener('click', function() {
                    loginModal.style.display = 'flex';
                    showPopup(loginPopup);
                });
            }
        <?php endif; ?>

        // Automatically open modal and show correct popup if there's a PHP message
        <?php if (!empty($login_error) || !empty($register_error) || !empty($register_success) || !empty($forgot_password_message) || !empty($set_new_password_message) || (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified'])): ?>
            loginModal.style.display = 'flex';
            <?php if (!empty($register_error)): ?>
                showPopup(createAccountPopup);
            <?php elseif (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']): ?>
                showPopup(setNewPasswordPopup);
            <?php elseif (!empty($forgot_password_message) && $email_sent_for_otp): ?>
                showPopup(verifyCodePopup);
            <?php elseif (!empty($forgot_password_message) && !$email_sent_for_otp && !isset($_SESSION['otp_verified'])): ?>
                showPopup(forgotPasswordPopup);
            <?php else: ?>
                showPopup(loginPopup);
            <?php endif; ?>
        <?php endif; ?>

        // Close Modal
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                loginModal.style.display = 'none';
            });
        }

        // Close modal when clicking outside of it
        window.addEventListener('click', function(event) {
            if (event.target === loginModal) {
                loginModal.style.display = 'none';
            }
        });

        // Navigation between popups
        if (showCreateAccountBtn) {
            showCreateAccountBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showPopup(createAccountPopup);
            });
        }

        if (backToLoginFromCreateBtn) {
            backToLoginFromCreateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showPopup(loginPopup);
            });
        }

        if (showForgotPasswordBtn) {
            showForgotPasswordBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showPopup(forgotPasswordPopup);
            });
        }

        if (backToLoginFromForgotBtn) {
            backToLoginFromForgotBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showPopup(loginPopup);
            });
        }

        if (backToLoginFromVerifyBtn) {
            backToLoginFromVerifyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showPopup(loginPopup);
            });
        }

        if (backToLoginFromSetNewBtn) {
            backToLoginFromSetNewBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showPopup(loginPopup);
            });
        }

        // ⏱️ COUNTDOWN TIMER FOR OTP RESEND
        <?php if ($show_resend_timer && $otp_cooldown_seconds > 0): ?>
        let countdown = <?php echo $otp_cooldown_seconds; ?>;
        const countdownElement = document.getElementById('countdownTimer');
        const resendBtn = document.getElementById('resendOtpBtn');
        const timerDisplay = document.getElementById('resendTimerDisplay');
        
        const timer = setInterval(function() {
            countdown--;
            
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(timer);
                
                // Hide timer display
                if (timerDisplay) {
                    timerDisplay.style.display = 'none';
                }
                
                // Enable resend button
                if (resendBtn) {
                    resendBtn.disabled = false;
                    resendBtn.classList.remove('resend-btn-disabled');
                    resendBtn.classList.add('resend-btn-enabled');
                }
            }
        }, 1000);
        <?php endif; ?>

        // Optional: Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href === '#' || href.startsWith('#show') || href.startsWith('#backTo')) {
                    return; // Don't scroll for modal navigation links
                }
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
</body>
</html>