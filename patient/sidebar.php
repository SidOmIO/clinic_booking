<?php
// Get the current directory path
$current_path = dirname($_SERVER['PHP_SELF']);

$appointment_string = "appointment/view.php";

// Split the strings into arrays using "/"
$parts1 = explode("/", $current_path);
$parts2 = explode("/", $appointment_string);

// Find the index of the matching word in $parts1
$matching_index = array_search($parts2[0], $parts1);

if ($matching_index !== false) {
    $result = implode("/", array_slice($parts1, 0, $matching_index)).'/'.$appointment_string;
} else {
    $result = $appointment_string;
}

// Sidebar content
?>
<div class="sidebar">
        <!-- Sidebar content -->
        <ul>
            <li><a href="<?php echo $result; ?>">Home Page</a></li>
            <li><a href="<?php echo $result; ?>">Appointment</a></li>
            <li><a href="#">Menu Item 2</a></li>
            <li><a href="#">Menu Item 3</a></li>
            <li><a href="../logout.php">Log out</a></li>
            <!-- Add more sidebar links as needed -->
        </ul>
    </div>