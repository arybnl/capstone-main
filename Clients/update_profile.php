<?php
// [update_profile.php]
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to update your profile.";
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $_SESSION['error_message'] = "First name, last name, and email cannot be empty.";
        header('Location: Profile.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
        header('Location: Profile.php');
        exit();
    }

    // You might want more robust phone number validation (regex etc.)

    // Check if email already exists for another user
    $sql_check_email = "SELECT user_id FROM Users WHERE email = ? AND user_id != ?";
    if ($stmt_check_email = $conn->prepare($sql_check_email)) {
        $stmt_check_email->bind_param("ss", $email, $user_id);
        $stmt_check_email->execute();
        $stmt_check_email->store_result();
        if ($stmt_check_email->num_rows > 0) {
            $_SESSION['error_message'] = "This email is already registered to another account.";
            $stmt_check_email->close();
            header('Location: Profile.php');
            exit();
        }
        $stmt_check_email->close();
    } else {
        $_SESSION['error_message'] = "Database error checking email: " . $conn->error;
        error_log("DB error checking email for user " . $user_id . ": " . $conn->error);
        header('Location: Profile.php');
        exit();
    }

    // Update Users table
    $sql_update_user = "UPDATE Users SET first_name = ?, last_name = ?, email = ? WHERE user_id = ?";
    if ($stmt_user = $conn->prepare($sql_update_user)) {
        $stmt_user->bind_param("ssss", $first_name, $last_name, $email, $user_id);
        if (!$stmt_user->execute()) {
            $_SESSION['error_message'] = "Error updating user data: " . $stmt_user->error;
            error_log("Error updating Users table for user " . $user_id . ": " . $stmt_user->error);
            $stmt_user->close();
            header('Location: Profile.php');
            exit();
        }
        $stmt_user->close();
    } else {
        $_SESSION['error_message'] = "Database statement preparation failed (Users): " . $conn->error;
        error_log("DB statement prep failed (Users) for user " . $user_id . ": " . $conn->error);
        header('Location: Profile.php');
        exit();
    }

    // Update Members table (assuming phone_number is there)
    $sql_update_member = "UPDATE Members SET phone_number = ? WHERE user_id = ?";
    if ($stmt_member = $conn->prepare($sql_update_member)) {
        $stmt_member->bind_param("ss", $phone_number, $user_id);
        if (!$stmt_member->execute()) {
            $_SESSION['error_message'] = "Error updating member data: " . $stmt_member->error;
            error_log("Error updating Members table for user " . $user_id . ": " . $stmt_member->error);
            $stmt_member->close();
            header('Location: Profile.php');
            exit();
        }
        $stmt_member->close();
    } else {
        $_SESSION['error_message'] = "Database statement preparation failed (Members): " . $conn->error;
        error_log("DB statement prep failed (Members) for user " . $user_id . ": " . $conn->error);
        header('Location: Profile.php');
        exit();
    }

    $_SESSION['success_message'] = "Profile updated successfully!";
    header('Location: Profile.php');
    exit();

} else {
    $_SESSION['error_message'] = "Invalid request method.";
    header('Location: Profile.php');
    exit();
}

$conn->close();
?>
