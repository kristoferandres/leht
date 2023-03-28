
<?php
// Database connection settings

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
$dbname = "allprojects";

// Create the database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check if the connection was successful
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === false) {
  die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create the projects table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS projects (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  table_name VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) === false) {
  die("Error creating table: " . $conn->error);
}


?>