<?php

require_once "project_DB.php";

// Get the project ID from the POST request
$project_id = $_POST['id'];

// Get the project details from the projects table
$query = "SELECT * FROM projects WHERE id = $project_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
if(mysqli_num_rows($result) == 0) {
  die("");
}

// Get the table name for the project from the result
$table_name = $row['table_name'];

// Get the rows from the project's table
$query = "SELECT * FROM $table_name";
$result = mysqli_query($conn, $query);




echo '<div class="container">';
while ($row = mysqli_fetch_assoc($result)) {
  $state = $row['state'];
  $rowid = $row['id'];
  $color = $state == 'done' ? 'success' : ($state == 'working' ? 'warning' : 'secondary bg-opacity-50');
  $padding = $state == 'done' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : ($state == 'working' ? '&nbsp;' : '');
  echo'
  <div class="row align-items-center" id="'.$row['id'].'">
  <div class="col-10">
    <p>'.$row['name'].'</p>
    <input type="text" style="display:none">
  </div>

  <div class="dropdown col-1">
  <button class="btn btn-'.$color.' dropdown-toggle"  aria-expanded="false">
  '.$state.$padding.'
  </button>
</div> 



  <div class="col-1">
  <button class="btn btn-primary details-button" data-bs-toggle="offcanvas" data-bs-target="#det'.$rowid.'" onclick="console.log('.$rowid.');">Details</button>
  </div>
</div>';
}
echo "</table>";

// Close the database connection
mysqli_close($conn);
?>