<?php

// Get the text and ID values from the AJAX request
$text = $_POST['text'];
$id = $_POST['id'];
$project_id = $_POST['project_id'];

require_once('project_DB.php');

// Get the project details from the projects table
$query = "SELECT * FROM projects WHERE id = $project_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Get the table name for the project from the result
$table_name = $row['table_name'];

// Insert the new row into the database
$sql = "UPDATE $table_name SET details='$text' WHERE id='$id'";

$conn->query($sql);



echo $project_id;