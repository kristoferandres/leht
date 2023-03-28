<?php
require_once "project_DB.php";

// Get the project ID from the POST request
$project_id = $_POST['id'];

// Get the project details from the projects table
$query = "SELECT * FROM projects WHERE id = $project_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Get the table name for the project from the result
$table_name = $row['table_name'];

// Get the rows from the project's table
$query = "SELECT * FROM $table_name";
$result = mysqli_query($conn, $query);

// Check for errors
if (!$result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

// Create an array to hold the data
$data = array();

// Loop through each row and add it to the data array
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Close the database connection
mysqli_close($conn);

// Convert the data array to a JSON object
$json_data = json_encode($data);

// Return the JSON object
echo $json_data;
?>