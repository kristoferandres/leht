<?php

require_once "project_DB.php";
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the project ID from the form
    $project_id = $_POST['project_id'];

    // Get the project title from the database
    $query = "SELECT table_name FROM projects WHERE id='$project_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $table_name = $row['table_name'];

    // Insert the new row into the database
    $query = "INSERT INTO $table_name (name, state, details) VALUES ('Task name here', 'Planning', 'Task details here')";
    mysqli_query($conn, $query);

    // Get the last inserted row ID
    $lastRowIdQuery = "SELECT LAST_INSERT_ID() AS last_row_id";
    $lastRowIdResult = mysqli_query($conn, $lastRowIdQuery);
    $lastRowIdRow = mysqli_fetch_assoc($lastRowIdResult);
    $lastRowId = $lastRowIdRow['last_row_id'];

    // Retrieve the row details
    $rowQuery = "SELECT name, state FROM $table_name WHERE id='$lastRowId'";
    $rowResult = mysqli_query($conn, $rowQuery);
    $row = mysqli_fetch_assoc($rowResult);

    // Determine the color and padding based on the state
    $state = $row['state'];
    $color = $state == 'done' ? 'success' : ($state == 'working' ? 'warning' : 'secondary bg-opacity-50');
    $padding = $state == 'done' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : ($state == 'working' ? '&nbsp;' : '');

    // Prepare the response data
    $response = array(
        'rowid' => $lastRowId,
        'name' => $row['name'],
        'state' => $row['state'],
        'color' => $color,
        'padding' => $padding
    );

    // Convert the response data to JSON format
    $responseJson = json_encode($response);

    // Send the response back to the client
    header('Content-Type: application/json');
    echo $responseJson;
    exit();
} else {
    // If the form wasn't submitted, redirect back to the home page
    header('Location: welcome.php');
    exit();
}
?>
