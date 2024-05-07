<?php
require_once ('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//localhost
$databaseHost = $_ENV['DATABASE_HOST'];
$databaseName = $_ENV['DATABASE_NAME'];
$databaseUsername = $_ENV['DATABASE_USERNAME'];
$databasePassword = $_ENV['DATABASE_PASSWORD'];
$databasePort = $_ENV['DATABASE_PORT'];

$mysqli = mysqli_connect($databaseHost,$databaseUsername,$databasePassword,$databaseName,$databasePort);

?>