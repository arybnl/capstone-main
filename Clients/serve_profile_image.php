<?php
// [serve_profile_image.php]
session_start();
require_once '../config.php'; // Adjust path as necessary

// Ensure the user is logged in to view their profile picture
if (!isset($_SESSION['user_id'])) {
    // Optionally redirect to a default image or show an error
    header('Content-Type: image/png');
    readfile('Img/profile_uploads/default_profile.png'); // Path to your default image
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve binary image data and MIME type from the database
$sql = "SELECT profile_image_data, profile_image_mime FROM Users WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->bind_result($imageData, $imageMime); // Bind results
    $stmt->fetch();
    $stmt->close();
}
$conn->close();

// Check if image data was retrieved
if ($imageData && $imageMime) {
    // Set the appropriate content type header
    header("Content-Type: " . $imageMime);
    // Output the binary image data
    echo $imageData;
} else {
    // If no image is found in the database, serve the default image
    header('Content-Type: image/png'); // Assuming default is PNG
    readfile('Img/profile_uploads/default_profile.png'); // Path to your default image
}
exit();
?>
