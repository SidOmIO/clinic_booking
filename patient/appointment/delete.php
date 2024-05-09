<?php
  session_start();
  if(!isset($_SESSION['login'])) {
    header("location: ../../index.php");
  }
  include_once("../../config.php");
  include_once("../../mailer.php");
  
  $message = include_once("../../forms/message.php");
  
  if(isset($_GET['id'])){
      $email = $_SESSION['login'];
      $id = $_GET['id'];
  
          //prepare statement
          $stmt = $mysqli->prepare("DELETE FROM appointment WHERE id = ? and email = ?");
          $log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('delete_appointment', ?, NOW())");

          // Bind parameters
          $stmt->bind_param("ss", $id, $email);
          $log->bind_param("s", $email);
          
          // Execute the statement
          if ($stmt->execute() && $log->execute()) {
              $stmt->close();
              $log->close();
              $mysqli->close();
              sendMail($email,$name, $message['cancel_appointment_title'], $message['cancel_appointment_body']);
              echo "<script>alert('Your appointment have been cancelled!')</script>";
              echo "<script>window.location.href = 'view.php';</script>";
              exit; 
          } else {
              echo "Error: " . $stmt->error;
          }
  } else {
    echo "<script>window.location.href = 'view.php';</script>";
  }
  ?>