<?php
  session_start();
  if(!isset($_SESSION['login']) && !isset($_SESSION['type'])) {
    header("location: ../../index.php");
    }
  include_once("../../config.php");
  include_once("../../mailer.php");
  
  $message = include_once("../../forms/message.php");
  
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
      $doctor_email = $_SESSION['login'];
      $patient_email = mysqli_real_escape_string($mysqli,$_POST['patient_email']);
      $appointment_id = mysqli_real_escape_string($mysqli,$_POST['appointment_id']);
      $remark = mysqli_real_escape_string($mysqli,$_POST['remark']);
  
          //prepare statement
          $stmt = $mysqli->prepare("INSERT INTO consultation(appointment_id, patient_email, doctor_email, remark) VALUES (?, ?, ?, ?)");
          $log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('add_consultation', ?, NOW())");

          // Bind parameters
          $stmt->bind_param("ssss", $appointment_id, $patient_email, $doctor_email, $remark);
          $log->bind_param("s", $email);
          
          // Execute the statement
          if ($stmt->execute() && $log->execute()) {
              $stmt->close();
              $log->close();
              $mysqli->close();
              sendMail($email, $message['add_appointment_title'], $message['add_appointment_body']);
              echo "<script>alert('Remarks added successfully!')</script>";
              echo "<script>window.location.href = 'view.php';</script>";
              exit; 
          } else {
              echo "Error: " . $stmt->error;
          }
  }
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
                <form action="" method="POST">
                <input type="hidden" id="patient_email" name="patient_email" value="<?=$_GET['email']?>">
                    <div class="form-group">
                        <label for="title">Appointment ID</label>
                        <input type="text" class="form-control" id="appointment_id" name="appointment_id" value="<?=$_GET['id']?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" required></textarea>
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
