<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer library
require 'vendor/autoload.php';

// Instantiate PHPMailer
$mail = new PHPMailer(true);

try {
    // Set up SMTP connection
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'projectmanagement1@gmail.com';
    $mail->Password = 'Azqxwcevrbt';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Set up email message
    $mail->setFrom('projectmanagement1@gmail.com', 'project management');
    $mail->addAddress('kristofer.andres6888@gmail.com', 'Kristofer Andres');
    $mail->Subject = 'Test email from PHP';
    $mail->Body = 'This is a test email from PHPMailer.';

    // Send the email
    $mail->send();
    echo 'Email sent successfully';
} catch (Exception $e) {
    echo "Error sending email: {$mail->ErrorInfo}";
}
?>