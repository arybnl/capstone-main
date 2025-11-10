<?php
// Start the session
session_start();

// Include the database configuration file
require_once '../config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header('Location: ../index.php'); // Adjust the path to your login page
    exit();
}

// --- Fetch Dashboard Data ---
$totalMembers = 0;
$totalActiveMembers = 0;
$totalTrainers = 0;

// Get Total Members
$sqlTotalMembers = "SELECT COUNT(*) AS total_members FROM Users WHERE user_type = 'member'";
if ($result = $conn->query($sqlTotalMembers)) {
    $row = $result->fetch_assoc();
    $totalMembers = $row['total_members'];
    $result->free();
}

// Get Total Active Members
$sqlTotalActiveMembers = "SELECT COUNT(*) AS total_active_members FROM Users WHERE user_type = 'member' AND status = 'active'";
if ($result = $conn->query($sqlTotalActiveMembers)) {
    $row = $result->fetch_assoc();
    $totalActiveMembers = $row['total_active_members'];
    $result->free();
}

// Get Total Trainers
$sqlTotalTrainers = "SELECT COUNT(*) AS total_trainers FROM Users WHERE user_type = 'trainer'";
if ($result = $conn->query($sqlTotalTrainers)) {
    $row = $result->fetch_assoc();
    $totalTrainers = $row['total_trainers'];
    $result->free();
}

// You can fetch recent activities and gym logins dynamically here if you have corresponding tables.
// For now, they remain static as in your HTML, or I can suggest new table structures for them.

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/t3-logo.png"> <!-- Adjusted path -->
    <link rel="stylesheet" href="SideBar.css">
    <style>
        body {
            background-color: #1A1A1A;
            color: #F6F9F7;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .content-wrapper {
            margin-left: 270px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed ~ .content-wrapper {
            margin-left: 100px;
        }

        @media (max-width: 1024px) {
            .content-wrapper {
                margin-left: 0 !important;
            }
        }

        .dashboard-card {
            background-color: #313131;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .dashboard-card h3 {
            color: #9E0A0A;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .dashboard-card .value {
            font-size: 36px;
            font-weight: bold;
        }

        .chart-card {
            background-color: #313131;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            height: 400px; /* Fixed height for charts */
            display: flex;
            flex-direction: column;
        }
        
        .chart-card-title {
            color: #9E0A0A;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-card-title a {
            color: #F6F9F7;
            font-size: 14px;
            text-decoration: none;
        }

        .chart-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .activities-card, .logins-card {
            background-color: #313131;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .activities-card h3, .logins-card h3 {
            color: #9E0A0A;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activities-card h3 a, .logins-card h3 a {
            color: #F6F9F7;
            font-size: 14px;
            text-decoration: none;
        }

        .activity-item, .login-item {
            border-bottom: 1px solid #404040;
            padding: 10px 0;
            font-size: 14px;
        }

        .activity-item:last-child, .login-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <!-- Toggle button -->
    <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Admin_sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <h1 class="mb-4">Dashboard</h1>

            <!-- Top Row Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h3>Total Members</h3>
                        <div class="value"><?php echo $totalMembers; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h3>Total Active Members</h3>
                        <div class="value"><?php echo $totalActiveMembers; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h3>Total Trainers</h3>
                        <div class="value"><?php echo $totalTrainers; ?></div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities and Monthly Active Chart -->
            <div class="row">
                <div class="col-md-7">
                    <div class="activities-card" style="height: 450px; overflow-y: auto;">
                        <h3>Recent Activities <a href="Admin_LogTrack.php">view all</a></h3>
                        <div class="activity-list">
                            <!-- Static data for now. Can be made dynamic with a 'Activities' table. -->
                            <div class="activity-item">
                                <small class="text-muted">June 20, 2025 | 11:32 AM</small><br>
                                John Dela Cruz completed Leg Day.
                            </div>
                            <div class="activity-item">
                                <small class="text-muted">June 20, 2025 | 10:27 AM</small><br>
                                Louisa Ramirez finished all workouts in the current plan.
                            </div>
                            <div class="activity-item">
                                <small class="text-muted">June 19, 2025 | 08:44 PM</small><br>
                                Janine Zamora uploaded a new health assessment.
                            </div>
                            <div class="activity-item">
                                <small class="text-muted">June 19, 2025 | 07:01 PM</small><br>
                                Kendrick Isaiah Garcia has been assigned to you.
                            </div>
                            <div class="activity-item">
                                <small class="text-muted">June 19, 2025 | 06:30 PM</small><br>
                                Tom Rae Santos completed 100% of this week's workout plan.
                            </div>
                            <div class="activity-item">
                                <small class="text-muted">June 19, 2025 | 06:30 PM</small><br>
                                Tom Rae Santos completed HIIT Workout - Day 7.
                            </div>
                            <div class="activity-item">
                                <small class="text-muted">June 19, 2025 | 01:06 PM</small><br>
                                Clai Dela Cruz completed Upper Body Strength Training.
                            </div>
                             <div class="activity-item">
                                <small class="text-muted">June 19, 2025 | 12:11 PM</small><br>
                                John Lloyd Cruz marked Rest Day Recovery as completed.
                            </div>
                             <div class="activity-item">
                                <small class="text-muted">June 19, 2025 | 08:03 AM</small><br>
                                John Lloyd Cruz completed Strength Training workout.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="chart-card">
                        <div class="chart-card-title">Monthly Active</div>
                        <div class="chart-container">
                             <img src="https://quickchart.io/chart?c={type:'line',data:{labels:['Dec','Jan','Feb','Mar','Apr','May','Jun'],datasets:[{label:'Monthly Active',data:[25,40,30,55,100,120,70],fill:false,borderColor:'rgb(255, 99, 132)'}]}}" alt="Monthly Active Chart" style="max-width: 100%; height: auto;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onsite Gym Logins Chart -->
            <div class="row">
                <div class="col-12">
                     <div class="logins-card" style="height: 350px; overflow-y: auto;">
                        <h3>Onsite Gym Logins <a href="Admin_LogTrack.php">view all</a></h3>
                        <div class="login-list">
                            <!-- Static data for now. Can be made dynamic with a 'GymLogins' table. -->
                            <div class="login-item">
                                <small class="text-muted">June 20, 2025 | 12:01 PM</small><br>
                                - Lorenzo Francis Manalo
                            </div>
                            <div class="login-item">
                                <small class="text-muted">June 20, 2025 | 11:54 AM</small><br>
                                - Angelica Rose Quirino
                            </div>
                            <div class="login-item">
                                <small class="text-muted">June 20, 2025 | 11:52 AM</small><br>
                                - Carmela Bautista
                            </div>
                            <div class="login-item">
                                <small class="text-muted">June 20, 2025 | 10:55 AM</small><br>
                                - John Dela Cruz
                            </div>
                            <div class="login-item">
                                <small class="text-muted">June 20, 2025 | 09:47 PM</small><br>
                                - Clai Dela Cruz
                            </div>
                            <div class="login-item">
                                <small class="text-muted">June 20, 2025 | 09:00 AM</small><br>
                                - Louisa Ramirez
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Sidebar.js"></script>
</body>
</html>
