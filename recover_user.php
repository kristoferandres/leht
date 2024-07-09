<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the username from the AJAX request
    $username = $_POST["username"];

    // Check if the username is valid
    if (!empty($username)) {
        // Perform your recovery operation here
        // Assuming you have a database connection established
        require_once "config.php";

        // Get the user's data from the recover table
        $stmt = $conn->prepare("SELECT username, identifier, recoverdata FROM recover WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($recoveredUsername, $identifier, $userData);
        $stmt->fetch();
        $stmt->close();

        // Separate the user data
        $userDataParts = explode('}', $userData);
        $password = $userDataParts[0];
        $admin_low = $userDataParts[1];
        $admin_high = $userDataParts[2];
        $created_at = $userDataParts[3];

        // Insert the recovered user data into the users table
        $insertStmt = $conn->prepare("INSERT INTO users (username, password, identifier, admin_low, admin_high, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssss", $recoveredUsername, $password, $identifier, $admin_low, $admin_high, $created_at);
        if ($insertStmt->execute()) {
            // Recovery successful

            // Delete the recovered entry from the recover table
            $deleteStmt = $conn->prepare("DELETE FROM recover WHERE username = ?");
            $deleteStmt->bind_param("s", $recoveredUsername);
            $deleteStmt->execute();

            // Optional: You can perform additional actions or display a success message here
            echo "success";
        } else {
            // Recovery failed
            echo "error";
        }

        // Close the statements
        $insertStmt->close();
        $deleteStmt->close();
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
