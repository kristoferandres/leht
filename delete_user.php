<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the user ID from the AJAX request
    $user_id = $_POST["user_id"];

    // Check if the user ID is valid
    if (!empty($user_id)) {
        // Perform your database delete operation here
        // Assuming you have a database connection established
        require_once "config.php";
        // Get the user's data
        $stmt = $conn->prepare("SELECT username, password, identifier, admin_low, admin_high, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($username, $password, $identifier, $admin_low, $admin_high, $created_at);
        $stmt->fetch();
        $stmt->close();

        // Insert the user's data into the recover table
        $insertStmt = $conn->prepare("INSERT INTO recover (username, identifier, recoverdata, delete_at) VALUES (?, ?, ?, ?)");
        $data = $password . "}" . $admin_low . "}" . $admin_high . "}" . $created_at;
        $delete_at = date("Y-m-d H:i:s", strtotime("+90 days"));
        $insertStmt->bind_param("ssss", $username, $identifier, $data, $delete_at);
        if ($insertStmt->execute()) {
            // Data insertion successful
            // Delete the user without removing the database
            $deleteUserStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $deleteUserStmt->bind_param("i", $user_id);
            if ($deleteUserStmt->execute()) {
                // Delete operation successful
                echo "success";
            } else {
                // Delete operation failed
                echo "error";
            }

            // Close the statements
            $deleteUserStmt->close();
            $insertStmt->close();
        } else {
            // Data insertion failed
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
