<?php
// initialize variables
$username = "";
$email = "";
$password = "";
$confirm_password = "";
$username_err = "";
$email_err = "";
$password_err = "";
$confirm_password_err = "";

function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    $max = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $max)];
    }

    return $randomString;
}

// check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

        // validate username
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter a username.";
        } else{
            // create database connection
            require_once "config.php";
    
            // check connection
            if($conn === false){
                die("ERROR: Could not connect. " . $conn->connect_error);
            }
    
            // prepare statement
            $sql = "SELECT id FROM users WHERE username = ?";
            if($stmt = $conn->prepare($sql)){
    
                // bind parameters
                $stmt->bind_param("s", $param_username);
    
                // set parameters
                $param_username = trim($_POST["username"]);
    
                // execute statement
                if($stmt->execute()){
    
                    // store result
                    $stmt->store_result();
    
                    if($stmt->num_rows == 1){
                        $username_err = "This username is already taken.";
                    } else{
                        $username = trim($_POST["username"]);
                    }
    
                } else{
                    echo "ERROR: Could not execute query: $sql. " . $conn->error;
                }
    
                // close statement
                $stmt->close();
            }
    
            // close connection
            $conn->close();
        }
        


    // validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    // validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Passwords did not match.";
        }
    }

    // if no errors, register user
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // create database connection
        require "config.php";

        // check connection
        if($conn === false){
            die("ERROR: Could not connect. " . $conn->connect_error);
        }

        // Check if the generated random string is already used in either column
    $sql_check = "SELECT id FROM users WHERE identifier = ? OR guest_identifier = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        // bind parameters
        $stmt_check->bind_param("ss", $randomString, $randomString);

        // execute statement
        if ($stmt_check->execute()) {
            // store result
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                // If the random string already exists, generate a new one
                $randomString = generateRandomString(8);
            }
        } else {
            echo "ERROR: Could not execute query: $sql_check. " . $conn->error;
        }

        // close statement
        $stmt_check->close();
    }

        // prepare statement
        $sql = "INSERT INTO users (username, password, identifier) VALUES (?,  ?,  ?)";
        if($stmt = $conn->prepare($sql)){

            // bind parameters
            $stmt->bind_param("sss", $param_username,  $param_password, $randomString);

            // set parameters
            $param_username = $username;
            $param_password = hash('sha512', $password); // hash password
            $randomString = generateRandomString(8);




             // execute statement
             if ($stmt->execute()) {
                // set session variables
                session_start();
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $stmt->insert_id;
                $_SESSION["username"] = $username;
                $_SESSION["identifier"] = $randomString;
                $_SESSION["admin_low"] = false;
                $_SESSION["admin_high"] =false;
                $_SESSION["admin_super_high"] = false;
       

                // redirect to welcome page
                header("location: welcome.php");
                exit;
            } else {
                echo "ERROR: Could not execute query: $sql. " . $conn->error;
            }

            // close statement
            $stmt->close();
        }

        // close connection
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Register</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <div class="invalid-feedback"><?php echo $username_err; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <div class="invalid-feedback"><?php echo $password_err; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                        <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                    <p class="mt-3">Already have an account? <a href="index.php">Login here</a>.</p>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>