<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the user ID and current state from the AJAX request
    $user_id = $_POST["user_id"];
    $current_state = $_POST["current_state"];

    // Check if the user ID and current state are valid
    if (!empty($user_id) && ($current_state === "true" || $current_state === "false")) {
        // Perform your database update operation here
        // Assuming you have a database connection established
        require_once "config.php";
        // Update the admin_low field in the users table based on the user ID
        $new_state = ($current_state === "true") ? 0 : 1;
        $stmt = $conn->prepare("UPDATE users SET admin_low = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_state, $user_id);
        if ($stmt->execute()) {
            // Update successful
            echo "success";
        } else {
            // Update failed
            echo "error";
        }

        // Close the database connection
        $stmt->close();
        $conn->close();
    } else {
        // Invalid parameters
        echo "error";
    }
} else {
    // Invalid request method
    echo "error";
}
?>
