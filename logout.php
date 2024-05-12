<?php
session_start();
include_once("config.php");
if(isset($_SESSION['login']) && isset($_SESSION['type'])) {
	
	$log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('logout', ?, NOW())");
	$log->bind_param("s", $_SESSION['login']);
	
	$stat = $row['type'];

	if ($log->execute()) {
		$log->close();
		$mysqli->close();
		unset($_SESSION['login']);
		unset($_SESSION['type']);
		session_destroy();
		header("location: index.php");
	}
	
}
?>