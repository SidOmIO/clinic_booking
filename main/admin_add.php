<?php
  session_start();
  if(!isset($_SESSION['login']) || (!isset($_SESSION['type']))) {
        header("location: ../../index.php");
  }
  include_once("../config.php");
  include_once("../mailer.php");

$message = include_once("../forms/message.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$email = mysqli_real_escape_string($mysqli,$_POST['email']);
	$name = mysqli_real_escape_string($mysqli,$_POST['name']);
	$password = mysqli_real_escape_string($mysqli,$_POST['password']);
    $confirm_password = mysqli_real_escape_string($mysqli,$_POST['confirm_password']);
	$phone = mysqli_real_escape_string($mysqli,$_POST['phone']);
    $type = mysqli_real_escape_string($mysqli,$_POST['type']);

	// checking empty fields
    if($password != $confirm_password){
        echo "<script>alert('Password doesn\\'t match!')</script>";
        echo "<script>window.location.reload();</script>";
    } else{

        $stmt = $mysqli->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Email already exists
            echo "<script>alert('Email already exists!')</script>";
            echo "<script>window.location.href = 'register.php';</script>";
        }
        //prepare statement
        $stmt = $mysqli->prepare("INSERT INTO user(email, name, password, phone, type) VALUES (?, ?, ?, ?, ?)");
        $log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('register', ?, NOW())");

        // Hash the password using SHA-256
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Bind parameters
        $stmt->bind_param("sssss", $email, $name, $hashed_password, $phone, $type);
        $log->bind_param("s", $email);
        
        // Execute the statement
        if ($stmt->execute() && $log->execute()) {
            $stmt->close();
            $log->close();
            $mysqli->close();
            sendMail($email, $message['register_title'], $message['register_body']);
            echo "<script>alert('Account created successfully!')</script>";
            echo "<script>window.location.href = 'index.php';</script>";
            exit; 
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="../assets/css/main/index.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('sidebar.php');?>    
    <div class="main-content">
        <form action="" method="post" class="registration-form" id="form">
            <h2>User Registration</h2>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="type">User Type</label>
                <input type="radio" id="admin" name="type" value="admin">
                <label for="admin">Admin</label>
                <input type="radio" id="doctor" name="type" value="doctor">
                <label for="css">Doctor</label>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
