<?php
// Start a PHP session
session_start();

// Include database configuration (assuming config.php is one level up relative to Logout.php)
require_once 'config.php'; // Adjusted path if config.php is in the root

// Determine the correct dashboard for the "No" button
$dashboard_url = 'index.php'; // Default to home if not logged in or type unknown

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_type']) {
        case 'member':
            $dashboard_url = 'Clients/dashboard.php';
            break;
        case 'trainer':
            $dashboard_url = 'Trainers/Trainer_Dashboard.php';
            break;
        case 'admin':
            $dashboard_url = 'Admin/Admin_Dashboard.php'; 
            break;
        default:
            // If user_type is unknown, redirect to index or a generic dashboard
            $dashboard_url = 'index.php';
            break;
    }
} else {
    // If somehow a non-logged-in user lands here, redirect them to the home page or login page
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - ProTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/t3-logo.png"> <!-- Adjusted path for favicon -->
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            background: url('img/logoutbg.jpg'); /* Adjusted path for background image */
            background-size: cover;
            background-position: center; /* Added for better image centering */
        }
        .logout-container {
            background-color: #494949;
            border: 1px solid #fff;
            border-radius: 15px;
            max-width: 400px;
            width: 90%;
            margin: auto;
            padding: 2rem 1.5rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
        }
        .protrack-text {
            color: #9E0A0A;
        }
        .btn-yes {
            background-color: #4D711B;
            color: #fff;
            border: none;
        }

        .btn-yes:hover {
            background-color: #6dbe02;
            color: #fff;
        }

        .btn-no {
            background-color: #9E0A0A;
            color: #fff;
            border: none;
        }

        .btn-no:hover {
            background-color: #af0101;
            color: #fff;
        }
        .btn-yes:hover, .btn-no:hover {
            opacity: 0.85;
        }
        @media (max-width: 576px) {
            .logout-container {
                padding: 1.5rem 0.75rem;
                max-width: 95vw;
            }
            .btn-group {
                flex-direction: column !important;
                gap: 0.75rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="logout-container text-center">
            <h2 class="fw-bold text-white mb-2">THANK YOU</h2>
            <h3 class="fw-bold mb-3 text-white">
                FOR USING <span class="protrack-text">ProTrack!</span>
            </h3>
            <div class="mb-3">
                <h5 class="text-white fw-normal">Are you sure you want to Logout?</h5>
            </div>
            <div class="d-flex justify-content-between btn-group gap-3">
                <!-- 'Yes' button: Triggers logout process in index.php -->
                <a type="button" class="btn btn-yes px-4 fw-bold" style="border-radius: 5px;" href="index.php?logout=true">Yes</a>
                <!-- 'No' button: Returns to the user's specific dashboard -->
                <a type="button" class="btn btn-no px-4 fw-bold" style="border-radius: 5px;" href="<?php echo $dashboard_url; ?>">No</a>
            </div>
        </div>
    </div>
     <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>