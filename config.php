<?php




// Database connection settings

define('DB_SERVER', '192.168.124.14');
define('DB_USERNAME', 'kandres');
define('DB_PASSWORD', 'kandres');
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

// Create the projects table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  )";
if ($conn->query($sql) === false) {
  die("Error creating table: " . $conn->error);
}

$query = "SELECT COUNT(*) FROM users";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_row($result);
$num_users = $row[0];

// If there are no users, add a default user with username "root" and password "root"
if ($num_users == 0) {
    $username = "root";
    $password = hash('sha256', 'root');
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    mysqli_query($conn, $query);
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