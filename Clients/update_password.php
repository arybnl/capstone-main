<?php
// [update_password.php]
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to change your password.";
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "All password fields are required.";
        header('Location: Profile.php');
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "New password and confirm password do not match.";
        header('Location: Profile.php');
        exit();
    }

    // Password strength policy (example: at least 8 characters)
    if (strlen($new_password) < 8) {
        $_SESSION['error_message'] = "New password must be at least 8 characters long.";
        header('Location: Profile.php');
        exit();
    }
    // Add more complex password rules here if needed (e.g., requires uppercase, number, special char)

    // Fetch current hashed password from database
    $sql_get_hash = "SELECT password_hash FROM Users WHERE user_id = ?";
    if ($stmt_get_hash = $conn->prepare($sql_get_hash)) {
        $stmt_get_hash->bind_param("s", $user_id);
        $stmt_get_hash->execute();
        $result_get_hash = $stmt_get_hash->get_result();
        if ($result_get_hash->num_rows === 1) {
            $row = $result_get_hash->fetch_assoc();
            $stored_hash = $row['password_hash'];

            // Verify current password
            if (password_verify($current_password, $stored_hash)) {
                // Hash the new password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in database
                $sql_update_password = "UPDATE Users SET password_hash = ? WHERE user_id = ?";
                if ($stmt_update_password = $conn->prepare($sql_update_password)) {
                    $stmt_update_password->bind_param("ss", $new_hashed_password, $user_id);
                    if ($stmt_update_password->execute()) {
                        $_SESSION['success_message'] = "Password updated successfully!";
                    } else {
                        $_SESSION['error_message'] = "Database error updating password: " . $stmt_update_password->error;
                        error_log("DB error updating password for user " . $user_id . ": " . $stmt_update_password->error);
                    }
                    $stmt_update_password->close();
                } else {
                    $_SESSION['error_message'] = "Database statement preparation failed (password update): " . $conn->error;
                    error_log("DB statement prep failed (password update) for user " . $user_id . ": " . $conn->error);
                }
            } else {
                $_SESSION['error_message'] = "Current password is incorrect.";
            }
        } else {
            $_SESSION['error_message'] = "User not found or password not set.";
            error_log("User not found or password not set for user ID: " . $user_id);
        }
        $stmt_get_hash->close();
    } else {
        $_SESSION['error_message'] = "Database error fetching password hash: " . $conn->error;
        error_log("DB error fetching password hash for user " . $user_id . ": " . $conn->error);
    }
    header('Location: Profile.php');
    exit();

} else {
    $_SESSION['error_message'] = "Invalid request method.";
    header('Location: Profile.php');
    exit();
}

$conn->close();
?>
