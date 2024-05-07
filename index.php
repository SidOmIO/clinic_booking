<?php
//including the database connection file
session_start();
include_once("forms/config.php");

if(isset($_SESSION['login'])){
    $stmt = $mysqli->prepare("SELECT type FROM user WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['login']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $stat = $row['type'];

        $stmt->close();
        $mysqli->close();
        if($stat == 'doctor')
            header("location: doctor/index.php");

        else if($stat == 'patient')
            header("location: patient/index.php");
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$email = mysqli_real_escape_string($mysqli,$_POST['email']);
	$password = mysqli_real_escape_string($mysqli,$_POST['password']);

    $stmt = $mysqli->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {

        $row = $result->fetch_assoc();

        // Accessing values from the fetched row
        $password_db = $row['password'];

        if(!password_verify($password, $password_db)) {
            echo "<script>alert('Wrong password!')</script>";
        }
        else{
            //Storing the name of user in SESSION database
            $_SESSION['login']=$email;
            $log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('login', ?, NOW())");
            $log->bind_param("s", $email);
            
            $stat = $row['type'];

            if ($log->execute()) {
                $log->close();
                $stmt->close();
                $mysqli->close();
                if($stat == 'doctor')
                    header("location: doctor/index.php");

                else if($stat == 'patient')
                    header("location: patient/index.php");
            }
        }
    } else {
        echo "<script>alert('You are not registered!')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <div class="container">
        <form method="post" class="login-form">
            <h2>Login</h2>
            <div class="form-group">
                <label for="username">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
