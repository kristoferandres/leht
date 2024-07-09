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

        // Check the current admin_high state of the user
        $stmt = $conn->prepare("SELECT admin_high FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($admin_high);
        $stmt->fetch();
        $stmt->close();

        // Update the admin_high and admin_low fields in the users table based on the user ID
        if ($admin_high == 1) {
            // If current admin_high is 1, change to 0 and admin_low to 1
            $stmt = $conn->prepare("UPDATE users SET admin_high = ?, admin_low = ? WHERE id = ?");
            $new_admin_high = 0;
            $new_admin_low = 1;
            $stmt->bind_param("iii", $new_admin_high, $new_admin_low, $user_id);
        } else {
            // If current admin_high is 0, change to 1 and admin_low to 0
            $stmt = $conn->prepare("UPDATE users SET admin_high = ?, admin_low = ? WHERE id = ?");
            $new_admin_high = 1;
            $new_admin_low = 0;
            $stmt->bind_param("iii", $new_admin_high, $new_admin_low, $user_id);
        }

        if ($stmt->execute()) {
            // Update successful
            echo "success";
        } else {
            // Update failed
            echo "error";
        }

        // Close the database connection
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
