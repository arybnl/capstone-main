<?php
// Email Configuration File - PHPMailer Version
// This file contains email settings for sending OTP codes

// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'arianebanila2@gmail.com');
define('SMTP_PASSWORD', 'gemqplcrakgjrask'); // App Password
define('SMTP_FROM_EMAIL', 'arianebanila2@gmail.com');
define('SMTP_FROM_NAME', 'Triple 3 Fitness Gym');

// OTP Configuration
define('OTP_EXPIRY_MINUTES', 10); // OTP expires in 10 minutes
define('OTP_LENGTH', 6); // 6-digit OTP

/**
 * Generate a random OTP code
 */
function generateOTP($length = OTP_LENGTH) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= random_int(0, 9);
    }
    return $otp;
}

/**
 * Send OTP email - Uses PHPMailer if available, falls back to mail()
 */
function sendOTPEmail($to_email, $otp, $user_name = '') {
    // Use absolute path based on this file's location
    $autoload_path = __DIR__ . '/vendor/autoload.php';
    
    // Check if PHPMailer is installed
    if (file_exists($autoload_path)) {
        return sendOTPEmailWithPHPMailer($to_email, $otp, $user_name);
    } else {
        // Fallback to PHP mail() function
        error_log("PHPMailer not found at: $autoload_path. Using mail() function. Install PHPMailer for better results: composer require phpmailer/phpmailer");
        return sendOTPWithMailFunction($to_email, $otp, $user_name);
    }
}

/**
 * Send OTP using PHPMailer (Recommended)
 */
function sendOTPEmailWithPHPMailer($to_email, $otp, $user_name = '') {
    require_once __DIR__ . '/vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Enable verbose debug output (comment out in production)
        // $mail->SMTPDebug = 2;
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Disable SSL verification for localhost (remove in production)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to_email);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Password Reset OTP - Triple 3 Fitness Gym';
        $mail->Body    = getOTPEmailHTML($otp, $user_name);
        $mail->AltBody = getOTPEmailPlainText($otp, $user_name);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Fallback: Send OTP using PHP mail() function
 */
function sendOTPWithMailFunction($to_email, $otp, $user_name = '') {
    $subject = 'Password Reset OTP - Triple 3 Fitness Gym';
    $message = getOTPEmailHTML($otp, $user_name);
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    
    return mail($to_email, $subject, $message, $headers);
}

/**
 * Get HTML email template for OTP
 */
function getOTPEmailHTML($otp, $user_name = '') {
    $greeting = $user_name ? "Hello $user_name," : "Hello,";
    $expiry = OTP_EXPIRY_MINUTES;
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; background: white; }
            .header { background-color: #ff6600; color: white; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 40px 30px; }
            .otp-box { background: #f9f9f9; border: 2px dashed #ff6600; border-radius: 8px; 
                       padding: 20px; margin: 30px 0; text-align: center; }
            .otp-code { font-size: 36px; font-weight: bold; color: #ff6600; 
                        letter-spacing: 8px; margin: 10px 0; font-family: monospace; }
            .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; font-size: 12px; color: #666; }
            ul { margin: 10px 0 0 0; padding-left: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🏋️ Triple 3 Fitness Gym</h1>
                <p>Password Reset Request</p>
            </div>
            <div class='content'>
                <p>$greeting</p>
                <p>We received a request to reset your password. Use the OTP code below to complete the password reset process:</p>
                
                <div class='otp-box'>
                    <p style='margin: 0; font-size: 14px; color: #666;'>Your OTP Code:</p>
                    <div class='otp-code'>$otp</div>
                    <p style='margin: 0; font-size: 12px; color: #666;'>Valid for $expiry minutes</p>
                </div>
                
                <div class='warning'>
                    <strong>⚠️ Security Notice:</strong>
                    <ul>
                        <li>Never share this code with anyone</li>
                        <li>This code expires in $expiry minutes</li>
                        <li>If you didn't request this, please ignore this email</li>
                    </ul>
                </div>
                
                <p>If you did not request a password reset, please ignore this email or contact us if you have concerns.</p>
                
                <p>Best regards,<br>
                <strong>Triple 3 Fitness & Martial Arts Gym Team</strong></p>
            </div>
            <div class='footer'>
                <p>Triple 3 Fitness and Martial Arts Gym<br>
                3rd Floor A3R3 Fitness and Martial Arts Gym, Amethyst street<br>
                Balibago Complex, Brgy. Balibago Sta. Rosa Laguna<br>
                📧 triple3gym@yahoo.com | 📱 0991 572 9696</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Get plain text email for OTP
 */
function getOTPEmailPlainText($otp, $user_name = '') {
    $greeting = $user_name ? "Hello $user_name," : "Hello,";
    $expiry = OTP_EXPIRY_MINUTES;
    
    return "
$greeting

We received a request to reset your password for Triple 3 Fitness Gym.

Your OTP Code: $otp

This code is valid for $expiry minutes.

SECURITY NOTICE:
- Never share this code with anyone
- If you didn't request this, please ignore this email

Best regards,
Triple 3 Fitness & Martial Arts Gym Team

---
Triple 3 Fitness and Martial Arts Gym
3rd Floor A3R3 Fitness and Martial Arts Gym
Amethyst street, Balibago Complex
Brgy. Balibago Sta. Rosa Laguna
Email: triple3gym@yahoo.com
Phone: 0991 572 9696
    ";
}

/**
 * Send password change notification email
 */
function sendPasswordChangeNotification($to_email, $user_name = '', $details = []) {
    $autoload_path = __DIR__ . '/vendor/autoload.php';
    
    if (file_exists($autoload_path)) {
        return sendPasswordChangeWithPHPMailer($to_email, $user_name, $details);
    } else {
        error_log("PHPMailer not found. Using mail() for password change notification.");
        return sendPasswordChangeWithMailFunction($to_email, $user_name, $details);
    }
}

/**
 * Send password change notification using PHPMailer
 */
function sendPasswordChangeWithPHPMailer($to_email, $user_name = '', $details = []) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to_email);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Password Changed Successfully - Triple 3 Fitness Gym';
        $mail->Body    = getPasswordChangeEmailHTML($user_name, $details);
        $mail->AltBody = getPasswordChangeEmailPlainText($user_name, $details);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error (Password Change): {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Fallback: Send password change notification using mail()
 */
function sendPasswordChangeWithMailFunction($to_email, $user_name = '', $details = []) {
    $subject = 'Password Changed Successfully - Triple 3 Fitness Gym';
    $message = getPasswordChangeEmailHTML($user_name, $details);
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    
    return mail($to_email, $subject, $message, $headers);
}

/**
 * Get HTML email template for password change notification
 */
function getPasswordChangeEmailHTML($user_name = '', $details = []) {
    $greeting = $user_name ? "Hello $user_name," : "Hello,";
    
    $ip_address = $details['ip_address'] ?? 'Unknown';
    $timestamp = $details['timestamp'] ?? date('F j, Y \a\t g:i A');
    $location = $details['location'] ?? 'Unknown Location';
    $device = $details['device'] ?? 'Unknown Device';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .header { background-color: #28a745; color: white; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 40px 30px; }
            .success-icon { text-align: center; font-size: 60px; color: #28a745; margin: 20px 0; }
            .info-box { background: #f8f9fa; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px; }
            .info-box h3 { margin-top: 0; color: #28a745; font-size: 16px; }
            .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
            .detail-row:last-child { border-bottom: none; }
            .detail-label { font-weight: bold; color: #666; }
            .detail-value { color: #333; }
            .warning-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
            .warning-box strong { color: #856404; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e9ecef; }
            ul { margin: 10px 0; padding-left: 20px; }
            ul li { margin: 5px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🏋️ Triple 3 Fitness Gym</h1>
                <p>Security Notification</p>
            </div>
            
            <div class='content'>
                <div class='success-icon'>✓</div>
                
                <h2 style='text-align: center; color: #28a745;'>Password Changed Successfully</h2>
                
                <p>$greeting</p>
                
                <p>This email confirms that your password for your Triple 3 Fitness Gym account has been successfully changed.</p>
                
                <div class='info-box'>
                    <h3>Change Details:</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Date & Time:</span>
                        <span class='detail-value'>$timestamp</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>IP Address:</span>
                        <span class='detail-value'>$ip_address</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Location:</span>
                        <span class='detail-value'>$location</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Device:</span>
                        <span class='detail-value'>$device</span>
                    </div>
                </div>
                
                <div class='warning-box'>
                    <strong>⚠️ Didn't make this change?</strong><br>
                    If you did not request this password change, your account may be compromised. Please take immediate action:
                    <ul>
                        <li>Contact us immediately at <strong>triple3gym@yahoo.com</strong> or call <strong>0991 572 9696</strong></li>
                        <li>Reset your password again using the forgot password feature</li>
                        <li>Review your account activity for any unauthorized access</li>
                    </ul>
                </div>
                
                <p><strong>Security Tips:</strong></p>
                <ul>
                    <li>Use a strong, unique password for your account</li>
                    <li>Never share your password with anyone</li>
                    <li>Be cautious of phishing emails</li>
                </ul>
                
                <p>If you made this change, no further action is required. Your account is secure.</p>
                
                <p>Best regards,<br>
                <strong>Triple 3 Fitness & Martial Arts Gym Team</strong></p>
            </div>
            
            <div class='footer'>
                <p><strong>Triple 3 Fitness and Martial Arts Gym</strong><br>
                3rd Floor A3R3 Fitness and Martial Arts Gym, Amethyst street<br>
                Balibago Complex, Brgy. Balibago Sta. Rosa Laguna<br>
                📧 triple3gym@yahoo.com | 📱 0991 572 9696</p>
                
                <p style='margin-top: 15px; color: #999; font-size: 11px;'>
                    This is an automated security notification. Please do not reply to this email.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Get plain text email for password change notification
 */
function getPasswordChangeEmailPlainText($user_name = '', $details = []) {
    $greeting = $user_name ? "Hello $user_name," : "Hello,";
    
    $ip_address = $details['ip_address'] ?? 'Unknown';
    $timestamp = $details['timestamp'] ?? date('F j, Y \a\t g:i A');
    $location = $details['location'] ?? 'Unknown Location';
    $device = $details['device'] ?? 'Unknown Device';
    
    return "
$greeting

PASSWORD CHANGED SUCCESSFULLY

This email confirms that your password for your Triple 3 Fitness Gym account has been successfully changed.

CHANGE DETAILS:
- Date & Time: $timestamp
- IP Address: $ip_address
- Location: $location
- Device: $device

⚠️ DIDN'T MAKE THIS CHANGE?

If you did not request this password change, your account may be compromised. 

IMMEDIATE ACTIONS:
1. Contact us: triple3gym@yahoo.com or 0991 572 9696
2. Reset your password again
3. Review account activity

SECURITY TIPS:
- Use a strong, unique password
- Never share your password
- Be cautious of phishing emails

Best regards,
Triple 3 Fitness & Martial Arts Gym Team
    ";
}

/**
 * Get client IP address
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    return trim($ip);
}

/**
 * Get device information
 */
function getDeviceInfo() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    if (preg_match('/mobile|android|iphone|ipad/i', $user_agent)) {
        $device_type = 'Mobile Device';
    } elseif (preg_match('/tablet/i', $user_agent)) {
        $device_type = 'Tablet';
    } else {
        $device_type = 'Desktop Computer';
    }
    
    if (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Edge';
    } else {
        $browser = 'Unknown Browser';
    }
    
    return "$device_type - $browser";
}
?>