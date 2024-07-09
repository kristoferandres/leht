<?php
session_start();
require_once('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"])) {
    // User is not logged in, handle the error or redirect to the login page
    // Example: return an error message
    echo "User not logged in";
    exit;
}

// Retrieve the user ID
$userId = $_SESSION["id"];

// Update the guest_identifier column to NULL to stop sharing
$sql = "UPDATE users SET guest_identifier = NULL, guest_identifier_expiration_time = NULL WHERE id = '$userId'";

if (mysqli_query($conn, $sql)) {
    echo "Sharing stopped successfully";
} else {
    // Error occurred, handle the error
    echo "Error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
