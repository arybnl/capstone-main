<?php
// Start the session
session_start();

// Include the database configuration file
require_once '../config.php'; // Adjust path as necessary

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header('Location: ../index.php'); // Adjust the path to your login page
    exit();
}

// Initialize variables for form data and messages
$email = $first_name = $last_name = $user_type = "";
$phone_number = $membership_expiry = "";
$email_err = $user_type_err = ""; // Removed password errors
$general_err = $success_message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate Email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        // Prepare a select statement to check if email already exists
        $sql = "SELECT user_id FROM Users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                $general_err = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Validate User Type
    if (empty(trim($_POST["user_type"]))) {
        $user_type_err = "Please select a user type.";
    } else {
        $user_type = trim($_POST["user_type"]);
        if (!in_array($user_type, ['member', 'trainer', 'admin'])) {
            $user_type_err = "Invalid user type selected.";
        }
    }

    // Collect other form data
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $phone_number = trim($_POST["phone_number"]);
    $membership_expiry = trim($_POST["membership_expiry"]); // Only relevant for members

    // --- Auto-generate password ---
    $default_password = "Triple3123"; // Set the default password
    $param_password = password_hash($default_password, PASSWORD_DEFAULT); // Hash it for storage
    // --- End Auto-generate password ---

    // Check input errors before inserting into database (Updated condition)
    if (empty($email_err) && empty($user_type_err) && empty($general_err)) {

        // Generate a UUID for the new user_id
        $new_user_id = generate_uuid(); // Using the function from config.php

        // Prepare an insert statement for Users table
        $sql_user = "INSERT INTO Users (user_id, email, password_hash, first_name, last_name, user_type) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt_user = $conn->prepare($sql_user)) {
            $stmt_user->bind_param("ssssss", $param_user_id, $param_email, $param_password, $param_first_name, $param_last_name, $param_user_type);

            // Set parameters
            $param_user_id = $new_user_id;
            $param_email = $email;
            // $param_password is already hashed above as $default_password
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_user_type = $user_type;

            if ($stmt_user->execute()) {
                // User record created successfully in Users table

                // Now handle insertion into specific role table (Members, Trainers, Admins)
                $role_id = generate_uuid(); // Generate a UUID for the specific role table's primary key
                $insert_role_success = true; // Flag to track if role insertion was successful

                switch ($user_type) {
                    case 'member':
                        // Generate QR Code Data (UUID) for members
                        $qr_code_data_uuid = generate_uuid(); // Use the function from config.php
                        $sql_member = "INSERT INTO Members (member_id, user_id, phone_number, membership_expiry, qr_code_data) VALUES (?, ?, ?, ?, ?)";
                        if ($stmt_member = $conn->prepare($sql_member)) {
                            $stmt_member->bind_param("sssss", $role_id, $new_user_id, $phone_number, $membership_expiry, $qr_code_data_uuid);
                            if (!$stmt_member->execute()) {
                                $general_err = "Error inserting into Members table: " . $stmt_member->error;
                                $insert_role_success = false;
                            }
                            $stmt_member->close();
                        } else {
                            $general_err = "Error preparing Members insert statement: " . $conn->error;
                            $insert_role_success = false;
                        }
                        break;
                    case 'trainer':
                        $sql_trainer = "INSERT INTO Trainers (trainer_id, user_id, contact_number) VALUES (?, ?, ?)"; // Simplified for example, add specialization/bio if available in form
                        if ($stmt_trainer = $conn->prepare($sql_trainer)) {
                            $stmt_trainer->bind_param("sss", $role_id, $new_user_id, $phone_number); // Using phone_number as contact for now
                            if (!$stmt_trainer->execute()) {
                                $general_err = "Error inserting into Trainers table: " . $stmt_trainer->error;
                                $insert_role_success = false;
                            }
                            $stmt_trainer->close();
                        } else {
                            $general_err = "Error preparing Trainers insert statement: " . $conn->error;
                            $insert_role_success = false;
                        }
                        break;
                    case 'admin':
                        $sql_admin = "INSERT INTO Admins (admin_id, user_id) VALUES (?, ?)";
                        if ($stmt_admin = $conn->prepare($sql_admin)) {
                            $stmt_admin->bind_param("ss", $role_id, $new_user_id);
                            if (!$stmt_admin->execute()) {
                                $general_err = "Error inserting into Admins table: " . $stmt_admin->error;
                                $insert_role_success = false;
                            }
                            $stmt_admin->close();
                        } else {
                            $general_err = "Error preparing Admins insert statement: " . $conn->error;
                            $insert_role_success = false;
                        }
                        break;
                }

                if ($insert_role_success) {
                    $success_message = "User account created successfully! Default Password: " . $default_password . " User ID: " . $new_user_id;
                    // Clear form fields after successful submission
                    $email = $first_name = $last_name = $user_type = "";
                    $phone_number = $membership_expiry = "";
                } else {
                    // If role insertion failed, consider rolling back the Users table insertion
                    // For simplicity, we are not doing a full transaction here.
                    // In a production environment, use database transactions.
                    $general_err = "User created, but failed to assign to role: " . $general_err;
                }

            } else {
                $general_err = "Error inserting into Users table: " . $stmt_user->error;
            }
            $stmt_user->close();
        } else {
            $general_err = "Error preparing Users insert statement: " . $conn->error;
        }
    }
}

// Close connection (will be re-opened for HTML structure if needed)
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/t3-logo.png">
    <link rel="stylesheet" href="SideBar.css">
    <style>
        body {
            background-color: #1A1A1A;
            color: #F6F9F7;
        }
        .wrapper {
            display: flex;
            min-height: 100vh;
        }
        .content-wrapper {
            flex-grow: 1;
            padding: 20px;
            margin-left: 270px; /* Adjust based on sidebar width */
            transition: margin-left 0.3s ease;
        }
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: 100px; /* Adjust for collapsed sidebar */
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.6);
            border: 2px solid #F43F3F;
        }
        .card-header {
            background-color: #F43F3F; /* Red header */
            color: white;
            font-weight: bold;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .form-group label {
            font-weight: 500;
        }
        .btn-custom {
            background-color: #F43F3F;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #c82333;
            color: white;
        }
        .form-select {
            height: calc(1.5em + .75rem + 2px); /* Bootstrap default for selects */
        }
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 350px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'admin_sidebar.php'; ?>

        <div class="content-wrapper">
            <div class="container-fluid">
                <h1 class="mb-4">Create New User Account</h1>

                <!-- Messages Display -->
                <div class="alert-container">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($general_err)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $general_err; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <div class="card-header">
                        User Details
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $first_name; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo $last_name; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
                                <div class="invalid-feedback"><?php echo $email_err; ?></div>
                            </div>

                            <!-- PASSWORD FIELDS REMOVED FROM HERE -->

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="user_type" class="form-label">User Type</label>
                                    <select name="user_type" id="user_type" class="form-select <?php echo (!empty($user_type_err)) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">Select Type</option>
                                        <option value="member" <?php echo ($user_type == 'member') ? 'selected' : ''; ?>>Member</option>
                                        <option value="trainer" <?php echo ($user_type == 'trainer') ? 'selected' : ''; ?>>Trainer</option>
                                        <option value="admin" <?php echo ($user_type == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <div class="invalid-feedback"><?php echo $user_type_err; ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control" value="<?php echo $phone_number; ?>">
                                    <small class="form-text text-muted">(Optional for Trainers/Admins)</small>
                                </div>
                            </div>

                            <div class="mb-3" id="membership_expiry_group" style="display: <?php echo ($user_type == 'member') ? 'block' : 'none'; ?>;">
                                <label for="membership_expiry" class="form-label">Membership Expiry Date</label>
                                <input type="date" name="membership_expiry" id="membership_expiry" class="form-control" value="<?php echo $membership_expiry; ?>">
                                <small class="form-text text-muted">Required for Members.</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom">Create User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide Membership Expiry field based on user type
            const userTypeSelect = document.getElementById('user_type');
            const membershipExpiryGroup = document.getElementById('membership_expiry_group');

            userTypeSelect.addEventListener('change', function() {
                if (this.value === 'member') {
                    membershipExpiryGroup.style.display = 'block';
                    membershipExpiryGroup.querySelector('input').setAttribute('required', 'required');
                } else {
                    membershipExpiryGroup.style.display = 'none';
                    membershipExpiryGroup.querySelector('input').removeAttribute('required');
                    membershipExpiryGroup.querySelector('input').value = ''; // Clear value if not a member
                }
            });

            // Admin sidebar toggle (assuming Sidebar.js handles the class toggling)
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.querySelector('.sidebar');
            const contentWrapper = document.querySelector('.content-wrapper');

            if (menuToggle && sidebar && contentWrapper) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    // Adjust content-wrapper margin based on sidebar state
                    if (sidebar.classList.contains('collapsed')) {
                        contentWrapper.style.marginLeft = '100px';
                    } else {
                        contentWrapper.style.marginLeft = '270px';
                    }
                });
            }

            // Message Alert Fade Out
            const alertContainers = document.querySelectorAll('.alert-container .alert');
            alertContainers.forEach(alert => {
                setTimeout(() => {
                    alert.classList.add('fade', 'show'); // Add fade-out classes
                    setTimeout(() => {
                        alert.remove(); // Remove after animation
                    }, 500); // Should match CSS transition/animation duration (e.g., 0.5s)
                }, 4500); // Start fade-out after 4.5 seconds (total 5s with 0.5s animation)
            });
        });
    </script>
</body>
</html>
