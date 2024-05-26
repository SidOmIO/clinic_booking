<?php
  session_start();
  if(!isset($_SESSION['login']) || (!isset($_SESSION['type']))) {
    header("location: ../../index.php");
  }
  include_once("../../config.php");
  include_once("../../mailer.php");
  
  $message = include_once("../../forms/message.php");
  
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $email = mysqli_real_escape_string($mysqli,$_POST['email']);
        $id = mysqli_real_escape_string($mysqli,$_POST['id']);
        $title = mysqli_real_escape_string($mysqli,$_POST['title']);
        $date = mysqli_real_escape_string($mysqli,$_POST['date']);
        $time = mysqli_real_escape_string($mysqli,$_POST['time']);
        $remark = mysqli_real_escape_string($mysqli,$_POST['remark']);
  
        //prepare statement
        $stmt = $mysqli->prepare("UPDATE appointment SET title = ?, date = ?, time = ?, remark = ? WHERE id = ? AND email = ?");
        $stmt->bind_param("ssssss", $title, $date, $time, $remark, $id, $email);

        $log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('update_appointment', ?, NOW())");

        // Bind parameters
        $log->bind_param("s", $_SESSION['login']);
        
        // Execute the statement
        if ($stmt->execute() && $log->execute()) {
            $stmt->close();
            $log->close();
            $mysqli->close();
            sendMail($email, $message['update_appointment_title'], $message['update_appointment_body']);
            echo "<script>alert('Appointment updated successfully!')</script>";
            echo "<script>window.location.href = 'view.php';</script>";
            exit; 
        } else {
            echo "Error: " . $stmt->error;
        }
  } else{
        if(isset($_GET['id']) && isset($_GET['email'])){
            $email = $_GET['email'];
            $id = $_GET['id'];

            $stmt = $mysqli->prepare("SELECT * FROM appointment WHERE id = ? AND email = ?");
            $stmt->bind_param("ss", $id, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $stmt->close();
                $mysqli->close();
            } elseif ($result->num_rows == 0) {
                echo "<script>window.location.href = 'view.php';</script>";
            }
        } else {
            echo "<script>window.location.href = 'view.php';</script>";
        }
  }
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Page</title>
    <link rel="stylesheet" href="../../assets/css/main/index.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('../sidebar.php');?>    

    <div class="main-content">
        <header>
            <h1>Edit Appointment</h1>
        </header>
        
        <section>
            <div class="container">
                <br>
                <form action="" method="POST">
                    <input type="hidden" id="id" name="id" value="<?=$id?>">
                    <input type="hidden" id="email" name="email" value="<?=$email?>">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?=$row['title']?>" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?=$row['date']?>" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" class="form-control" id="time" name="time" value="<?=$row['time']?>" required>
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" required><?=$row['remark']?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </section>
    </div>
    <script>
        // Get the current date in the format YYYY-MM-DD
        var currentDate = new Date().toISOString().split('T')[0];
        document.getElementById("date").setAttribute("min", currentDate);
    </script>
</body>
</html>
