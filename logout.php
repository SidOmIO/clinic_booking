<?php
session_start();
include_once("forms/config.php");
if(isset($_SESSION['login'])) {
	
	$log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('logout', ?, NOW())");
	$log->bind_param("s", $_SESSION['login']);
	
	$stat = $row['type'];

	if ($log->execute()) {
		$log->close();
		$mysqli->close();
		unset($_SESSION['login']);
		session_destroy();
		header("location: index.php");
	}
	
}
?>