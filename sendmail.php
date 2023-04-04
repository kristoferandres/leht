<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer library
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Instantiate PHPMailer
$mail = new PHPMailer(true);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
$databaseusername = $dbname = $_SESSION['username'];

try {
    // Set up SMTP connection
    $mail->isSMTP();
    $mail->Host = 'smtp.porkbun.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kandres@kandres.buzz';
    $mail->Password = 'Azqxwcevrbt';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Set up email message
    $mail->setFrom('kandres@kandres.buzz', 'kandres');
    $mail->addAddress('kristofer.andres6888@gmail.com', 'Kristofer Andres');
    $mail->Subject = 'Account activation request';
    $mail->Body = 'Request for account named: '.$databaseusername;

    // Send the email
    $mail->send();
    echo 'Email sent successfully';
} catch (Exception $e) {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = 'error_log';
}
?>