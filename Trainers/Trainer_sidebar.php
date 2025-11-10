<?php
// Trainer_sidebar.php
// This file assumes it's included within a PHP context where session_start() has been called.
// It generates the HTML for the trainer sidebar.
?>
<div class="sidebar">
    <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>
    <nav class="nav flex-column">
        <!-- IMAGE LOGO -->
        <a class="nav-link" id="logo" href="Trainer_Dashboard.php"> <!-- Link logo to dashboard -->
            <span class="icon">
                <img src="../img/t3-logo.png" alt="Logo" width="90" height="90">
            </span>
        </a>
        <!-- DASHBOARD -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Trainer_Dashboard.php') ? 'active' : ''; ?>" id="dashboard" href="Trainer_Dashboard.php">
            <span class="icon">
                <i class="bi bi-columns-gap"></i>
            </span>
            <span class="text">Dashboard</span>
        </a>
        <!-- CLIENTS -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'ClientsPage.php') ? 'active' : ''; ?>" id="clients" href="ClientsPage.php">
            <span class="icon">
                <i class="bi bi-people"></i>
            </span>
            <span class="text">Clients</span>
        </a>
        <!-- MESSAGES -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Trainer_Messenger.php') ? 'active' : ''; ?>" id="messages" href="Trainer_Messenger.php">
            <span class="icon">
                <i class="bi bi-chat-left-text"></i>
            </span>
            <span class="text">Messages</span>
        </a>
        <!-- PROFILE -->
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Trainer_Profile.php') ? 'active' : ''; ?>" id="profile" href="Trainer_Profile.php">
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
