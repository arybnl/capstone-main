<?php
// Admin_sidebar.php
// This file assumes it's included within a PHP context where session_start() has been called.
// It generates the HTML for the admin sidebar.
?>
<div class="sidebar">
    <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>
    <nav class="nav flex-column">
        <!-- IMAGE LOGO -->
        <a class="nav-link" id="logo" href="Admin_Dashboard.php"> <!-- Link logo to dashboard -->
            <span class="icon">
                <img src="../img/t3-logo.png" alt="Logo" width="90" height="90">
            </span>
        </a>
        <!-- DASHBOARD -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Admin_Dashboard.php') ? 'active' : ''; ?>" id="dashboard" href="Admin_Dashboard.php">
            <span class="icon">
                <i class="bi bi-columns-gap"></i>
            </span>
            <span class="text">Dashboard</span>
        </a>
        <!-- USERS / with SUBMENU -->
        <a class="nav-link" href="#submenu" id="users" data-bs-toggle="collapse" aria-expanded="false" aria-controls="submenu">
            <span class="icon">
                <i class="bi bi-people"></i>
            </span>
            <span class="description">Users
                <i class="bi bi-caret-down-fill dropdown"></i>
            </span>
        </a>
        <!-- USER'S SUBMENU -->
        <div class="sub-menu collapse <?php echo (basename($_SERVER['PHP_SELF']) == 'Admin_Members.php' || basename($_SERVER['PHP_SELF']) == 'Admin_Trainers.php' || basename($_SERVER['PHP_SELF']) == 'Create_User.php') ? 'show' : ''; ?>" id="submenu">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Admin_Members.php') ? 'active' : ''; ?>" href="Admin_Members.php">
                <span class="text">Members</span>
            </a>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Admin_Trainers.php') ? 'active' : ''; ?>" href="Admin_Trainers.php">
                <span class="text">Trainers</span>
            </a>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Create_User.php') ? 'active' : ''; ?>" href="Create_User.php">
                <span class="text">Create User</span> <!-- NEW LINK -->
            </a>
        </div>
        <!-- LOG AND TRACK -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Admin_LogTrack.php') ? 'active' : ''; ?>" id="logtrack" href="Admin_LogTrack.php">
            <span class="icon">
                <i class="bi bi-ui-radios"></i>
            </span>
            <span class="text">Log & Track</span>
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
