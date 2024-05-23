<?php
$appointment = "/appointment/view.php";
$admin_add = "/admin_add.php";
$consultation = "/consultation/view.php";
$logout = "/logout.php";
?>
<link rel="stylesheet" href="<?=APP_BASE_PATH?>/assets/css/sidebar.css" />
<link
    href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css"
    rel="stylesheet"
/>
<nav>
    <div class="sidebar">
    <div class="logo">
        <span class="logo-name">Clinic System</span>
    </div>

    <div class="sidebar-content">
        <ul class="lists">
        <li class="list">
            <a href="<?php echo APP_BASE_PATH; ?>" class="nav-link">
            <i class="bx bx-home-alt icon"></i>
            <span class="link">Main Page</span>
            </a>
        </li>
        <?php
        if($_SESSION['type'] == "admin"){ ?>
            <li class="list">
            <a href="<?php echo APP_MAIN_PATH.$admin_add; ?>" class="nav-link">
                <i class="bx bx-bar-chart-alt-2 icon"></i>
                <span class="link">Add New User</span>
            </a>
            </li>
        <?php } ?>
        <?php
        if($_SESSION['type'] != "doctor"){ ?>
            <li class="list">
            <a href="<?php echo APP_MAIN_PATH.$appointment; ?>" class="nav-link">
                <i class="bx bx-bell icon"></i>
                <span class="link">Appointment</span>
            </a>
            </li>
        <?php } ?>
        <li class="list">
            <a href="<?php echo APP_MAIN_PATH.$consultation; ?>" class="nav-link">
            <i class="bx bx-message-rounded icon"></i>
            <span class="link">Consultation</span>
            </a>
        </li>
        <div class="bottom-cotent">
        <li class="list">
            <a href="<?=APP_BASE_PATH.$logout?>" class="nav-link">
            <i class="bx bx-log-out icon"></i>
            <span class="link">Logout</span>
            </a>
        </li>
        </div>
    </div>
    </div>
</nav>