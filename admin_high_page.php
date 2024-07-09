<?php

// Check if the user is not logged in or does not have the necessary permissions
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["admin_high"] == false) {
    // Redirect the user to the login page or any other appropriate page
    header("location: index.php"); // Change "login.php" to the desired page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin High Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="welcome.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand">High Admin Page</a>
        </div>
        <ul class="navbar-nav ml-auto">
        <button onclick="window.location.href = 'welcome.php';" class="button-link me-2">Back to Homepage</button>
                <li class="nav-item">
                <a href="logout.php" class="btn btn-danger">Logout</a>
                </li>
            </ul>
    </nav>

    <div class="container mt-5">
        <h2>User List</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Low Admin</th>
                    <th>High Admin</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through the users here and display each user as a table row -->
                <?php
                // Fetch and display the users' data
                require_once "config.php";
                $sql = "SELECT * FROM users";
                $result = mysqli_query($link, $sql);
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $userId = $row['id'];
                        $username = $row['username'];
                        $myusername =  $_SESSION["username"];
                        $lowAdmin = $row['admin_low'];
                        $highAdmin = $row['admin_high'];
                        $superhighAdmin = $row['admin_super_high'];
                        $createdAt = $row['created_at'];

                        echo "<tr data-user-id='$userId'>";
                        echo "<td>$username</td>";
                        echo "<td><div class='color-square low-admin" . ($superhighAdmin ? " bg-pink" : ($highAdmin ? " bg-success" : ($lowAdmin ? " bg-success" : " bg-danger"))) . " 'data-user-id='$userId'></div></td>";
                        echo "<td><div class='color-square" . ($highAdmin ? " bg-success" : ($superhighAdmin ? " bg-pink" : " bg-danger")) . "'></div></td>";
                        echo "<td>$createdAt</td>";
                        echo "<td>";
                        if ($username !== $myusername ){
                            if ($highAdmin || $superhighAdmin){
                        echo "<button class='btn disabled btn-danger' data-user-id='$userId'>Delete</button>";
                    }else {   
                        echo "<button class='btn btn-danger delete-btn' data-user-id='$userId'>Delete</button>";
                    };
                        } else {
                            echo "<h5 class='ms-3' data-user-id='$userId'>You</h5>";
                        };
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="deleteUserBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
// Delete user confirmation handling
const deleteUserBtns = document.querySelectorAll('.delete-btn');
const deleteUserModal = document.querySelector('#confirmationModal');
const deleteUserModalBtn = document.querySelector('#deleteUserBtn');

let selectedUserId = null;

deleteUserBtns.forEach((btn) => {
  btn.addEventListener('click', () => {
    selectedUserId = btn.getAttribute('data-user-id');
    const modal = new bootstrap.Modal(deleteUserModal);
    modal.show();
  });
});

deleteUserModalBtn.addEventListener('click', () => {
  // Perform delete operation here using selectedUserId
  console.log(`Deleting user with ID: ${selectedUserId}`);

  // Send AJAX request to the server for delete operation
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'delete_user.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Process the response from the server
      const response = xhr.responseText;
      if (response === 'success') {
        // Delete operation successful
        console.log('User deleted successfully.');

        // Remove the table row from the DOM with fade-out animation
        const tableRow = document.querySelector(`tr[data-user-id="${selectedUserId}"]`);
        if (tableRow) {
          tableRow.classList.add('fade-out');
          tableRow.addEventListener('animationend', () => {
            tableRow.remove();
          });
        }
      } else {
        // Delete operation failed
        console.log('Error deleting user.');
      }
    }
  };
  xhr.send(`user_id=${selectedUserId}`);

  // Close the modal
  const modal = bootstrap.Modal.getInstance(deleteUserModal);
  modal.hide();
});







// Toggle low admin state
const lowAdminSquares = document.querySelectorAll('.low-admin');
lowAdminSquares.forEach((square) => {
  square.addEventListener('click', () => {
    const userId = square.getAttribute('data-user-id');
    const currentState = square.classList.contains('bg-success');
    const newClass = currentState ? 'bg-danger' : 'bg-success';
    // Check if the user is an Admin High
    const highAdminSquare = square.parentElement.nextElementSibling.firstElementChild;
    const isHighAdmin = highAdminSquare.classList.contains('bg-success');
    const isSuperHighAdmin = highAdminSquare.classList.contains('bg-pink');

    // Check if the user is trying to change their own privileges or change low admin color if they are a high admin
    if (isHighAdmin || isSuperHighAdmin) {
      return;
    }

    // Perform the update operation using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_low_admin.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        // Update the visual state based on the response
        if (xhr.responseText === 'success') {
          square.classList.remove('bg-success', 'bg-danger');
          square.classList.add(newClass);
          
        } else {
          console.log('Error updating low admin state.');
        }
      }
    };
    console.log(userId);
    console.log(currentState);
    xhr.send(`user_id=${userId}&current_state=${currentState}`);


  });
});


    </script>
</body>
</html>
