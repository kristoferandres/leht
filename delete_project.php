<?php
// Connect to the MySQL database
require_once "project_DB.php";

// Check for errors
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Get the project ID from the POST data
$project_id = $_POST["id"];



$query = "SELECT * FROM projects WHERE id = $project_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Get the table name for the project from the result
$table_name = $row['table_name'];

$query = "DELETE FROM projects WHERE id = $project_id";
mysqli_query($conn, $query);




$query = "DROP TABLE $table_name";
mysqli_query($conn, $query);



// Prepare the SQL query to delete the project with the given ID



// Close the database connection
mysqli_close($conn);