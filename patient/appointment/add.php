<?php
  session_start();
  if(!isset($_SESSION['login'])) {
    header("location: ../../index.php");
  }
  include_once("../../config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Page</title>
    <link rel="stylesheet" href="../../assets/css/doctor/index.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('../sidebar.php');?>    

    <div class="main-content">
        <header>
            <h1>Book an Appointment</h1>
        </header>
        
        <section>
            <div class="container">
                <form action="process_form.php" method="POST">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date">
                    </div>
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" class="form-control" id="time" name="time">
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark</label>
                        <textarea class="form-control" id="remark" name="remark"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
