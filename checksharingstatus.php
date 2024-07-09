<?php
start_session();
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

// Query to check if guest_identifier exists for the user
$sql = "SELECT guest_identifier, guest_identifier_expiration_time FROM users WHERE id = '$userId'";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $sharingStatus = isset($row['guest_identifier']) ? '1' : '0';
    $sharingLink = isset($row['guest_identifier']) ? $row['guest_identifier'] : '';
    $guestidentifier =  $sharingLink;
    $expirationTime = isset($row['guest_identifier_expiration_time']) ? $row['guest_identifier_expiration_time'] : 0;

    $response = array(
        'status' => $sharingStatus,
        'guestidentifier' => $guestidentifier,
        'endtime' => $expirationTime
    );
    echo json_encode($response);
} else {
    // Error occurred, handle the error
    echo "Error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
