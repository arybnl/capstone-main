<?php
// [Profile_pictureUpload.php] - UPDATED VERSION for DB Storage
session_start();
require_once '../config.php'; // Adjust path as necessary

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to upload a profile picture.";
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$uploadDirectory = 'Img/profile_uploads/'; // Relative to this PHP file

// Ensure the upload directory exists and is writable
if (!file_exists($uploadDirectory)) {
    if (!mkdir($uploadDirectory, 0775, true)) {
        $_SESSION['error_message'] = "Failed to create upload directory.";
        error_log("Failed to create upload directory: " . $uploadDirectory);
        header('Location: Profile.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];

    // Basic file validation
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error_message'] = "File upload error: " . $file['error'];
        header('Location: Profile.php');
        exit();
    }

    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['error_message'] = "Invalid file type. Only JPEG, PNG, GIF are allowed.";
        header('Location: Profile.php');
        exit();
    }

    if ($file['size'] > $maxFileSize) {
        $_SESSION['error_message'] = "File size exceeds 5MB limit.";
        header('Location: Profile.php');
        exit();
    }

    // Generate a unique filename for the file system
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = hash('md5', uniqid(rand(), true)) . '.' . $fileExtension;
    $destination = $uploadDirectory . $newFileName;

    // Read binary data of the image for database storage
    $image_binary_data = file_get_contents($file['tmp_name']);
    $image_mime_type = $file['type']; // Store MIME type to serve correctly later

    // Move the uploaded file to the designated directory
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // File uploaded successfully to file system, now update the database

        $old_profile_picture_filename = '';
        // Get the current profile picture filename from the database
        $sql_get_old = "SELECT profile_picture FROM Users WHERE user_id = ?";
        if ($stmt_get_old = $conn->prepare($sql_get_old)) {
            $stmt_get_old->bind_param("s", $user_id);
            $stmt_get_old->execute();
            $result_get_old = $stmt_get_old->get_result();
            if ($result_get_old->num_rows == 1) {
                $old_profile_picture_filename = $result_get_old->fetch_assoc()['profile_picture'];
            }
            $stmt_get_old->close();
        }

        // Update Users table with new filename AND binary data
        $sql_update = "UPDATE Users SET profile_picture = ?, profile_image_data = ?, profile_image_mime = ? WHERE user_id = ?";
        if ($stmt_update = $conn->prepare($sql_update)) {
            // 'sbs' -> string for filename, binary for image data, string for mime type
            $stmt_update->bind_param("sbss", $newFileName, $null, $image_mime_type, $user_id);
            // This line is crucial for BLOB binding:
            // $null is a placeholder; real binding happens with send_long_data
            $stmt_update->send_long_data(1, $image_binary_data); // 1 is the index of profile_image_data (0-indexed)

            if ($stmt_update->execute()) {
                $_SESSION['success_message'] = "Profile picture updated successfully!";

                // Delete the old profile picture from the file system if it's not the default
                if (!empty($old_profile_picture_filename) && $old_profile_picture_filename !== 'default_profile.png') {
                    $oldFilePath = $uploadDirectory . $old_profile_picture_filename;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Delete the file
                    }
                }
            } else {
                $_SESSION['error_message'] = "Database update failed: " . $stmt_update->error;
                error_log("Profile picture DB update failed for user " . $user_id . ": " . $stmt_update->error);
                unlink($destination); // Delete the newly uploaded file to prevent orphans if DB update fails
            }
            $stmt_update->close();
        } else {
            $_SESSION['error_message'] = "Database statement preparation failed: " . $conn->error;
            error_log("Profile picture update statement prep failed: " . $conn->error);
            unlink($destination); // Delete uploaded file if DB prep fails
        }
    } else {
        $_SESSION['error_message'] = "Failed to move uploaded file.";
        error_log("Failed to move uploaded file for user " . $user_id);
    }
} else {
    $_SESSION['error_message'] = "No file uploaded or invalid request method.";
}

$conn->close();
header('Location: Profile.php'); // Redirect back to profile page
exit();
?>
