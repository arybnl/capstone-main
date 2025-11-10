<?php
// [Profile.php] - UPDATED VERSION (for DB Image Storage)
session_start();
require_once '../config.php'; // Adjust path as necessary

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: ../index.php'); // Redirect to login page
    exit();
}

// Ensure the logged-in user is a member
if ($_SESSION['user_type'] !== 'member') {
    switch ($_SESSION['user_type']) {
        case 'trainer':
            header('Location: ../Trainers/Trainer_Dashboard.php');
            break;
        case 'admin':
            header('Location: ../Admin/Admin_Dashboard.php');
            break;
        default:
            header('Location: ../index.php'); // Fallback
            break;
    }
    exit();
}

$user_id = $_SESSION['user_id'];
$user_data = [];
$member_data = [];

// Fetch user data (including profile_picture, profile_image_data, profile_image_mime)
$sql_user = "SELECT first_name, last_name, email, status, profile_picture, profile_image_data, profile_image_mime FROM Users WHERE user_id = ?";
if ($stmt_user = $conn->prepare($sql_user)) {
    $stmt_user->bind_param("s", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    if ($result_user->num_rows == 1) {
        $user_data = $result_user->fetch_assoc();
    }
    $stmt_user->close();
} else {
    $_SESSION['error_message'] = "ERROR: Could not prepare user query: " . $conn->error;
}

// Fetch member data
$sql_member = "SELECT phone_number, membership_expiry, qr_code_data FROM Members WHERE user_id = ?";
if ($stmt_member = $conn->prepare($sql_member)) {
    $stmt_member->bind_param("s", $user_id);
    $stmt_member->execute();
    $result_member = $stmt_member->get_result();
    if ($result_member->num_rows == 1) {
        $member_data = $result_member->fetch_assoc();
    }
    $stmt_member->close();
} else {
    $_SESSION['error_message'] = "ERROR: Could not prepare member query: " . $conn->error;
}

// Default values if data isn't found
$first_name = htmlspecialchars($user_data['first_name'] ?? '');
$last_name = htmlspecialchars($user_data['last_name'] ?? '');
$full_name = trim($first_name . ' ' . $last_name);
$email = htmlspecialchars($user_data['email'] ?? '');
$status = htmlspecialchars($user_data['status'] ?? 'N/A');
$phone_number = htmlspecialchars($member_data['phone_number'] ?? '');
$membership_expiry = htmlspecialchars($member_data['membership_expiry'] ?? 'N/A');
$qr_code_data = htmlspecialchars($member_data['qr_code_data'] ?? '');


// --- Profile Picture Logic (Now links to serve_profile_image.php) ---
// The actual profile picture displayed will come from serve_profile_image.php
// which fetches the binary data from the DB or serves default_profile.png
$profile_picture_src = 'serve_profile_image.php'; // This script will dynamically serve the image

// Original $profile_picture_filename is still stored in DB and in file system for your records,
// but for display on the profile page itself, we use the DB-served image.
// If you still wanted to fallback to the file system version if DB fails for some reason,
// you'd add more complex logic here, but for simplicity, we directly point to the DB server script.
// If profile_image_data is NULL in DB, serve_profile_image.php will automatically serve default_profile.png

// Generate QR Code URL
$qr_code_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_code_data);

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="icon" type="image/png" href="Img/t3-logo.png"> <!-- Adjust path for favicon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="RecoVid.css">
    <link rel="stylesheet" href="Profile.css">
    <link rel="stylesheet" href="SideBar.css">
    <style>
        /* Custom styles for Profile page (can be moved to Profile.css if preferred) */
        .settings-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            min-height: 80vh; /* Adjust as needed */
            background-color: #fff;
        }

        .settings-header {
            background-color: #dc3545; /* Red header */
            color: white;
            padding: 20px;
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
        }

        .vertical-divider {
            border-right: 1px solid #eee;
        }

        @media (max-width: 575.98px) {
            .vertical-divider {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
        }

        .sidebar-option {
            padding: 15px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-weight: 500;
            color: #555;
        }

        .sidebar-option:hover {
            background-color: #f8f9fa;
        }

        .sidebar-option.active {
            background-color: #dc3545; /* Red for active */
            color: white;
            border-radius: 5px;
        }

        .content-section {
            padding: 20px;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #dc3545;
        }

        .edit-profile-btn {
            color: #dc3545;
            cursor: pointer;
            font-weight: 500;
            text-decoration: underline;
        }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            padding: .5rem .75rem;
            margin-top: .25rem;
            color: #495057;
        }

        .btn-custom {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #c82333;
            color: white;
        }

        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
            cursor: not-allowed;
        }

        .qr-container {
            text-align: center;
        }

        /* Messages */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 350px;
        }

        .alert {
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .alert.fade.show {
            animation: fadeOut 5s forwards; /* Fade out after 5 seconds */
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }

        .content-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-container {
            flex-grow: 1;
            padding-top: 20px;
            padding-bottom: 20px;
        }
    </style>
</head>
<body class="body">
    <!-- Toggle button -->
    <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Client_sidebar.php'; ?>

    <!-- Main Container for PROFILE -->
    <div class="content-wrapper">
        <div class="main-container">
            <div class="container-fluid px-3">
                <!-- Messages Display -->
                <div class="alert-container">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="settings-container mx-auto">
                    <!-- Header -->
                    <div class="settings-header">PROFILE</div>

                    <!-- Content -->
                    <div class="row g-0 flex-grow-1 h-100">
                        <!-- Sidebar ng Setting -->
                        <div class="col-12 col-sm-4 col-md-3 p-3 vertical-divider">
                            <div class="sidebar-option active" onclick="showContent(event, 'account')">Account</div>
                            <div class="sidebar-option" onclick="showContent(event, 'password')">Password</div>
                            <div class="sidebar-option" onclick="showContent(event, 'qr')">QR Code</div>
                        </div>

                        <!-- Main Content -->
                        <div class="col-12 col-sm-8 col-md-9 p-3 overflow-auto">
                            <!-- Account Section -->
                            <div id="account" class="content-section">
                                <form id="profileUploadForm" action="Profile_pictureUpload.php" method="POST" enctype="multipart/form-data">
                                    <div class="text-center">
                                        <!-- Image source now points to the script that serves from DB -->
                                        <img id="profileImage" src="<?php echo $profile_picture_src; ?>" class="profile-pic mb-2" alt="Profile">
                                        <input type="file" id="profileInput" name="profile_picture" class="d-none" accept="image/*" onchange="previewProfilePic(event)">
                                        <div>
                                            <small>
                                                <span class="edit-profile-btn" onclick="document.getElementById('profileInput').click()">Edit Profile Picture</span>
                                            </small>
                                        </div>
                                        <h5 class="fw-bold text-secondary mt-2"><?php echo $full_name; ?></h5>
                                        <small>Member ID: <?php echo htmlspecialchars($user_id); ?></small>
                                    </div>
                                </form>

                                <hr class="my-4">

                                <!-- Form for personal information updates -->
                                <h5 class="fw-bold mb-3">Personal Information</h5>
                                <form id="personalInfoForm" action="update_profile.php" method="POST">
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="firstName">First Name:</label>
                                            <input type="text" class="form-control" id="firstName" name="first_name" value="<?php echo $first_name; ?>" readonly>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="lastName">Last Name:</label>
                                            <input type="text" class="form-control" id="lastName" name="last_name" value="<?php echo $last_name; ?>" readonly>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" readonly>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="phoneNumber">Cellphone No.:</label>
                                            <input type="text" class="form-control" id="phoneNumber" name="phone_number" value="<?php echo $phone_number; ?>" readonly>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label>Status:</label>
                                            <div class="info-box"><?php echo ucfirst($status); ?></div>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label>Membership Expiry:</label>
                                            <div class="text-danger fw-bold">
                                                <?php
                                                if ($membership_expiry !== 'N/A') {
                                                    $expiry_date = new DateTime($membership_expiry);
                                                    echo $expiry_date->format('F j, Y');
                                                } else {
                                                    echo $membership_expiry;
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <button type="button" class="btn btn-outline-danger" id="editPersonalInfoBtn">Edit Personal Info</button>
                                        <button type="submit" class="btn btn-custom d-none" id="savePersonalInfoBtn">Save Changes</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Password Section -->
                            <div id="password" class="content-section d-none">
                                <h5 class="fw-bold mb-3">Change Password</h5>
                                <form action="update_password.php" method="POST" id="passwordChangeForm">
                                    <div class="mb-3">
                                        <label for="currentPassword">Current Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword">New Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="newPassword" name="new_password" required pattern=".{8,}" title="Password must be at least 8 characters long">
                                        <small id="newPasswordHelp" class="form-text text-muted">Minimum 8 characters.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                        <div id="passwordMatchError" class="invalid-feedback">
                                            Passwords do not match.
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-custom">Save Password</button>
                                </form>
                            </div>

                            <!-- QR Code Section -->
                            <div id="qr" class="content-section d-none qr-container">
                                <h5 class="fw-bold mb-3">Download Your QR</h5>
                                <?php if ($qr_code_data !== 'N/A' && !empty($qr_code_data)): ?>
                                    <img src="<?php echo $qr_code_api_url; ?>" alt="QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                                    <p>This QR code contains your unique membership data.</p>
                                    <?php
                                    // Construct the username for the filename
                                    // You might want to sanitize this further if names can contain problematic characters for filenames
                                    $username_for_qr_filename = '';
                                    if (!empty($first_name) && !empty($last_name)) {
                                        $username_for_qr_filename = urlencode(str_replace(' ', '_', $first_name . '_' . $last_name));
                                    } elseif (!empty($first_name)) {
                                        $username_for_qr_filename = urlencode(str_replace(' ', '_', $first_name));
                                    } elseif (!empty($last_name)) {
                                        $username_for_qr_filename = urlencode(str_replace(' ', '_', $last_name));
                                    } else {
                                        // Fallback if no name parts are available
                                        $username_for_qr_filename = 'Member';
                                    }

                                    $qr_download_filename = $username_for_qr_filename . '_Protrack_QRcode.png';
                                    ?>
                                    <a href="<?php echo $qr_code_api_url; ?>" id="downloadQrCodeBtn" class="btn btn-custom mt-3" title="Download your QR Code">Download QR Code</a>
                                <?php else: ?>
                                    <p class="text-muted">QR Code not available. Please contact support if you believe this is an error.</p>
                                    <img src="Img/qr_code_image.png" alt="Default QR Placeholder" class="img-fluid mb-3" style="max-width: 200px;">
                                    <small class="d-block text-muted">A default image is shown. Your actual QR will appear here once generated.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Sidebar.js"></script>
    <script src="Profile.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ... (Your existing Profile.js content if it's inline, e.g., for showing/hiding profile pic edit) ...

            const downloadQrCodeBtn = document.getElementById('downloadQrCodeBtn');

            if (downloadQrCodeBtn) {
                downloadQrCodeBtn.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default link behavior (opening in a new tab)

                    const qrCodeImageUrl = this.href; // Get the image URL from the button's href
                    const suggestedFileName = "<?php echo htmlspecialchars($qr_download_filename); ?>"; // Get the PHP-generated filename

                    // Fetch the image data
                    fetch(qrCodeImageUrl)
                        .then(response => response.blob()) // Get the response as a Blob (binary data)
                        .then(blob => {
                            // Create a temporary URL for the blob
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = suggestedFileName; // Set the download filename
                            document.body.appendChild(a);
                            a.click(); // Programmatically click the hidden link to trigger download
                            window.URL.revokeObjectURL(url); // Clean up the temporary URL
                            a.remove(); // Remove the temporary link element
                        })
                        .catch(error => {
                            console.error('Error downloading QR code:', error);
                            alert('Failed to download QR code. Please try again.');
                            // Fallback: If fetch fails, try opening in new tab as before (less ideal, but better than nothing)
                            window.open(qrCodeImageUrl, '_blank');
                        });
                });
            }
        });
    </script>
</body>
</html>
