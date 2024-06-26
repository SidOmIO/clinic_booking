<?php
function sendMail($recipient,$subject,$body){
    // Include PHPMailer autoloader
    require_once realpath(__DIR__ . '/vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Create a new PHPMailer instance
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    // Set up SMTP
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_SERVER']; // SMTP server address
    $mail->SMTPAuth = false;
    // $mail->Username = $_ENV['MAIL_USERNAME'];
    // $mail->Password = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPAutoTLS = false; 
    $mail->Port = $_ENV['SMTP_PORT']; 

    // Set up sender and recipient
    $mail->setFrom($_ENV['MAIL_USERNAME'], 'Admin');

    $mail->addAddress($recipient);

    // Set email subject and body
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
}
?>
