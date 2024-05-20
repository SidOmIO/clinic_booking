<?php
require_once ('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('APP_BASE_PATH', 'http://localhost/clinic');
define('APP_MAIN_PATH', APP_BASE_PATH . '/main');

//localhost
$databaseHost = $_ENV['DATABASE_HOST'];
$databaseName = $_ENV['DATABASE_NAME'];
$databaseUsername = $_ENV['DATABASE_USERNAME'];
$databasePassword = $_ENV['DATABASE_PASSWORD'];
$databasePort = $_ENV['DATABASE_PORT'];

$mysqli = mysqli_connect($databaseHost,$databaseUsername,$databasePassword,$databaseName,$databasePort);

?>