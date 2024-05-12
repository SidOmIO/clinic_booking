<?php
  session_start();
  if(!isset($_SESSION['login']) || (!isset($_SESSION['type']))) {
    header("location: ../../index.php");
  }
  include_once("../../config.php");
  include_once("../../mailer.php");
  
  $message = include_once("../../forms/message.php");
  
  if(isset($_GET['id']) && isset($_GET['email'])){
    $email = $_GET['email'];
    $id = $_GET['id'];

    //prepare statement
    $stmt = $mysqli->prepare("DELETE FROM appointment WHERE id = ? and email = ?");
    $stmt->bind_param("ss", $id, $email);
    
    $log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('delete_appointment', ?, NOW())");

    // Bind parameters
    $log->bind_param("s", $_SESSION['login']);
    
    // Execute the statement
    if ($stmt->execute() && $log->execute()) {
        $stmt->close();
        $log->close();
        $mysqli->close();
        sendMail($email, $message['cancel_appointment_title'], $message['cancel_appointment_body']);
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