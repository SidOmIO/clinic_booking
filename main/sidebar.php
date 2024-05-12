<?php
$appointment = "/appointment/view.php";
$logout = "/logout.php";

// Sidebar content
?>
<div class="sidebar">
        <!-- Sidebar content -->
        <ul>
            <li><a href="<?php echo APP_BASE_PATH; ?>">Home Page</a></li>
            <li><a href="<?php echo APP_MAIN_PATH.$appointment; ?>">Appointment</a></li>
            <li><a href="#">Menu Item 2</a></li>
            <li><a href="#">Menu Item 3</a></li>
            <li><a href="<?=APP_BASE_PATH.$logout?>">Log out</a></li>
            <!-- Add more sidebar links as needed -->
        </ul>
    </div>