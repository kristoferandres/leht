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


    // Get the name and state from the form
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);

    // Insert the new row into the database
    $query = "INSERT INTO $table_name (name, state, details) VALUES ('$name', '$state', 'Task details here')";
    mysqli_query($conn, $query);

    // Redirect back to the project page

    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
    ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
    console_log("asdasdadasd".$project_id."fdhfsgdgdafsg");
    console_log("Location: welcome.php?project_id=$project_id");
    if ($project_id) {
        
        header("Location: welcome.php?project_id=$project_id");
      } else {
        die("Error: Project ID is missing.");
      }

    
    exit();
} else {
    // If the form wasn't submitted, redirect back to the home page
    header('Location: welcome.php');
    exit();
}
?>