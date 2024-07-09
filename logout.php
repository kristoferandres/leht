<?php

// unset all session variables
$_SESSION = array();

// destroy session
session_destroy();


// redirect to login page
header("location: index.php");
exit;
?>