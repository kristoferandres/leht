<?php
session_start();
require_once('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"])) {
    // User is not logged in, handle the error or redirect to the login page
    // Example: return an error message
    exit;
}

// Retrieve the posted duration and user ID
$duration = $_POST["duration"];
$userId = $_SESSION["id"];

// Generate the share identifier and expiration time
$shareIdentifier = generateShareIdentifier();
$expirationTime = date("Y-m-d H:i:s", strtotime("+$duration hours"));

// Save the share identifier and expiration time in the database
$sql = "UPDATE users SET guest_identifier = '$shareIdentifier', guest_identifier_expiration_time = '$expirationTime' WHERE id = '$userId'";

if (mysqli_query($conn, $sql)) {
    // Share link saved successfully, return the generated share link and expiration time
    $guestidentifier =$shareIdentifier;
    $response = array(
        'endtime' => $expirationTime,
        'guestidentifier' => $guestidentifier
    );
    echo json_encode($response);
} else {
    // Error saving share link, handle the error
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Function to generate a random share identifier
function generateShareIdentifier() {
    $length = 10;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $identifier = '';
    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $identifier .= $characters[$index];
    }
    return $identifier;
}
?>
