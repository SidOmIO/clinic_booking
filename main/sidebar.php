<?php
$appointment = "/appointment/view.php";
$admin_add = "/admin_add.php";
$consultation = "/consultation/view.php";
$logout = "/logout.php";

// Sidebar content
?>
<div class="sidebar">
        <!-- Sidebar content -->
        <ul>
            <li><a href="<?php echo APP_BASE_PATH; ?>">Home Page</a></li>
            <?php
            if($_SESSION['type'] == "admin"){ ?>
            <li><a href="<?php echo APP_MAIN_PATH.$admin_add; ?>">Add New User</a></li>
            <?php } ?>
            <?php
            if($_SESSION['type'] != "doctor"){ ?>
            <li><a href="<?php echo APP_MAIN_PATH.$appointment; ?>">Appointment</a></li>
            <?php } ?>
            <li><a href="<?php echo APP_MAIN_PATH.$consultation; ?>">Consultation</a></li>
            <li><a href="<?=APP_BASE_PATH.$logout?>">Log out</a></li>
            <!-- Add more sidebar links as needed -->
        </ul>
    </div>