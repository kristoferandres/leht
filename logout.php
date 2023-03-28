<?php
session_start();

// unset all session variables
$_SESSION = array();

// destroy session
session_destroy();

// unset cookie
setcookie("username", "", time() - 3600, "/"); // cookie expires in the past

// redirect to login page
header("location: index.php");
exit;
?>