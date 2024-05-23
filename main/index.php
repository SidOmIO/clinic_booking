<?php
  session_start();
  if(!isset($_SESSION['login']) || (!isset($_SESSION['type']))) {
        header("location: ../../index.php");
  }
  include_once("../config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="../assets/css/main/index.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('sidebar.php');?>    

    <div class="main-content">
        <header>
            <h1>Welcome <?=$_SESSION['login']?></h1>
        </header>
        
        <section>
            <!-- Main content of the page -->
            <h2>Title of the Main Content Section</h2>
            <p>This is the main content of the page. You can add your content here.</p>
        </section>
    </div>
</body>
</html>
