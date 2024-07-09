<?php
date_default_timezone_set('Europe/Tallinn');



// Database connection settings

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
$dbname = "login";

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

// Create the login table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    identifier VARCHAR(8),
    guest_identifier VARCHAR(10),
    guest_identifier_expiration_time DATETIME,
    admin_low BOOLEAN DEFAULT false,
    admin_high BOOLEAN DEFAULT false,
    admin_super_high BOOLEAN DEFAULT false,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  )";
if ($conn->query($sql) === false) {
  die("Error creating table: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS recover (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  identifier VARCHAR(8) NOT NULL,
  recoverdata VARCHAR(8000) NOT NULL,
  delete_at DATETIME
)";
if ($conn->query($sql) === false) {
die("Error creating table: " . $conn->error);
}

// Database credentials
define('DB_NAME', 'login');
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>