<?php
  session_start();
  if(!isset($_SESSION['login'])) {
    header("location: ../index.php");
  }
  include_once("../config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Page</title>
    <link rel="stylesheet" href="../assets/css/doctor/index.css">
</head>
<body>
    <div class="sidebar">
        <!-- Sidebar content -->
        <ul>
            <li><a href="#">Menu Item 1</a></li>
            <li><a href="#">Menu Item 2</a></li>
            <li><a href="#">Menu Item 3</a></li>
            <li><a href="../logout.php">Log out</a></li>
            <!-- Add more sidebar links as needed -->
        </ul>
    </div>
    
    <div class="main-content">
        <header>
            <h1>Welcome to the Patient Page</h1>
        </header>
        
        <section>
            <!-- Main content of the page -->
            <h2>Title of the Main Content Section</h2>
            <p>This is the main content of the page. You can add your content here.</p>
        </section>
    </div>
</body>
</html>
