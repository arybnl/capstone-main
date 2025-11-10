<?php
// Client_sidebar.php
// This file assumes it's included within a PHP context where session_start() has been called.
// It generates the HTML for the client sidebar.
?>
<div class="sidebar">
    <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>
    <nav class="nav flex-column">
        <!-- IMAGE LOGO -->
        <a class="nav-link" id="logo" href="dashboard.php">
            <span class="icon">
                <img src="img/t3-logo.png" alt="Logo" width="90" height="90">
            </span>
        </a>
        <!-- DASHBOARD -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" id="dashboard" href="dashboard.php">
            <span class="icon">
                <i class="bi bi-columns-gap"></i>
            </span>
            <span class="text">Dashboard</span>
        </a>
        <!-- TRACKER -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Tracker.php') ? 'active' : ''; ?>" id="tracker" href="Tracker.php">
            <span class="icon">
                <i class="bi bi-activity"></i>
            </span>
            <span class="text">Tracker</span>
        </a>
        <!-- ATTENDANCE -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'TrackerAttendance.php') ? 'active' : ''; ?>" id="attendance" href="TrackerAttendance.php">
            <span class="icon">
                <i class="bi bi-person-check"></i>
            </span>
            <span class="text">Attendance</span>
        </a>
        <!-- HEALTH HISTORY -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'HealthHistory.php') ? 'active' : ''; ?>" id="health" href="HealthHistory.php">
            <span class="icon">
                <i class="bi bi-file-earmark-medical"></i>
            </span>
            <span class="text">Health History</span>
        </a>
        <!-- RECOMMENDED VIDEOS -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'RecommendedVid.php') ? 'active' : ''; ?>" id="reco" href="RecommendedVid.php">
            <span class="icon">
                <i class="bi bi-rewind-btn-fill"></i>
            </span>
            <span class="text">Recommended Videos</span>
        </a>
        <!-- DIET PLAN -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'DietPlan.php') ? 'active' : ''; ?>" id="planner" href="DietPlan.php">
            <span class="icon">
                <i class="bi bi-leaf-fill"></i>
            </span>
            <span class="text">Diet Plan</span>
        </a>
        <!-- MESSAGES -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Messenger.php') ? 'active' : ''; ?>" id="client-messages" href="Messenger.php">
            <span class="icon">
                <i class="bi bi-chat-left-text"></i>
            </span>
            <span class="text">Messages</span>
        </a>
        <!-- ARCHIVE -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Archive.php') ? 'active' : ''; ?>" id="archive" href="Archive.php">
            <span class="icon">
                <i class="bi bi-archive"></i>
            </span>
            <span class="text">Archive</span>
        </a>
        <!-- PROFILE -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Profile.php') ? 'active' : ''; ?>" id="profile" href="Profile.php">
            <span class="icon">
                <i class="bi bi-person-circle"></i>
            </span>
            <span class="text">Profile</span>
        </a>
        <!-- LOGOUT -->
        <a class="nav-link" id="logout" href="../Logout.php">
            <span class="icon">
                <i class="bi bi-box-arrow-left"></i>
            </span>
            <span class="text">Logout</span>
        </a>
    </nav>
</div>
