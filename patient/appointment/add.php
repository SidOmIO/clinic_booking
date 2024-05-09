<?php
  session_start();
  if(!isset($_SESSION['login'])) {
    header("location: ../../index.php");
  }
  include_once("../../config.php");
  include_once("../../mailer.php");
  
  $message = include_once("../../forms/message.php");
  
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
      $email = $_SESSION['login'];
      $title = mysqli_real_escape_string($mysqli,$_POST['title']);
      $date = mysqli_real_escape_string($mysqli,$_POST['date']);
      $time = mysqli_real_escape_string($mysqli,$_POST['time']);
      $remark = mysqli_real_escape_string($mysqli,$_POST['remark']);
  
          //prepare statement
          $stmt = $mysqli->prepare("INSERT INTO appointment(email, title, date, time, remark) VALUES (?, ?, ?, ?, ?)");
          $log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('add_appointment', ?, NOW())");

          // Bind parameters
          $stmt->bind_param("sssss", $email, $title, $date, $time, $remark);
          $log->bind_param("s", $email);
          
          // Execute the statement
          if ($stmt->execute() && $log->execute()) {
              $stmt->close();
              $log->close();
              $mysqli->close();
              sendMail($email,$name, $message['add_appointment_title'], $message['add_appointment_body']);
              echo "<script>alert('Appointment created successfully!')</script>";
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
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
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
