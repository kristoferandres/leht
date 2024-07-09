<?php
ob_start(); // Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    $max = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $max)];
    }

    return $randomString;
}
    // Include the database connection file
    require_once "project_DB.php";

    if (isset($_POST['projectTitle'])) {
        $title = $_POST['projectTitle'];

        $query = "SELECT id FROM projects WHERE title='$title'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {

            $_SESSION['warning'] = "A project with this name already exists. Please choose a different name.";
            header('Location: index.php');
            exit();

        } else {
            $database = 'p_' . $title; // generate table name from title

            // create new database
            $query = 'CREATE TABLE ' . $database . "(
              id INT(11) NOT NULL AUTO_INCREMENT,
              name VARCHAR(255) NOT NULL,
              state ENUM('planning', 'working', 'done') NOT NULL DEFAULT 'planning',
              details VARCHAR(2000) NOT NULL,
              PRIMARY KEY (id)
            )";
            if (!mysqli_query($conn, $query)) {
                die('Error creating table: ' . mysqli_error($conn));
            }

            // insert new project into projects table
            $query = "INSERT INTO projects (title, table_name) VALUES ('$title', '$database')";
            if (!mysqli_query($conn, $query)) {
                die('Error inserting project: ' . mysqli_error($conn));
            }

            // Retrieve the project ID and title
            $query = "SELECT id, title FROM projects WHERE title='$title'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $projectId = $row['id'];
                $projectTitle = $row['title'];

                // Return the project ID and title in the response
                $response = array(
                    'projectId' => $projectId,
                    'projectTitle' => $projectTitle
                );

                echo json_encode($response);
            } else {
                echo "Error: Failed to retrieve project ID and title";
            }

            exit();
        }
    }
}
?>
