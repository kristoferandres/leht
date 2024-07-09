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




echo '<div class="container">
<div id="projectContent-list">';
while ($row = mysqli_fetch_assoc($result)) {
  $state = $row['state'];
  $rowid = $row['id'];
  $color = $state == 'done' ? 'success' : ($state == 'working' ? 'warning' : 'secondary bg-opacity-50');
  $padding = $state == 'done' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : ($state == 'working' ? '&nbsp;' : '');

  echo '<div class="row align-items-center" id="row-'.$row['id'].'">
    <div class="col-7">
      <p>'.$row['name'].'</p>
      <input type="text" style="display:none">
    </div>
    <div class="col-1">
      <button class="btn btn-primary edit-btn" data-row-id="'.$row['id'].'"> <i class="fa-solid fa-pen"></i> </button>
      <button class="btn btn-success save-btn" data-row-id="'.$row['id'].'" style="display:none"> <i class="fa-solid fa-check"></i> </button>
    </div>
    <div class="col-1">
      <button class="btn btn-danger delete-btn" data-row-id="'.$row['id'].'"> <i class="fa-solid fa-xmark"></i> </button>
    </div>
    <div class="dropdown col-1">
      <button class="btn btn-'.$color.' dropdown-toggle" type="button" id="stateDropdown'.$row['id'].'" data-bs-toggle="dropdown" aria-expanded="false">
        '.$state.$padding.'
      </button>
      <ul class="dropdown-menu" aria-labelledby="stateDropdown'.$row['id'].'">
      <li><a class="dropdown-item" data-new-value="Planning" data-row-id="'.$row['id'].'">Planning</a></li>
      <li><a class="dropdown-item" data-new-value="Working" data-row-id="'.$row['id'].'">Working</a></li>
      <li><a class="dropdown-item" data-new-value="Done" data-row-id="'.$row['id'].'">Done</a></li>
      </ul>
    </div> 
    <div class="col-1">
      <button class="btn btn-primary details-button" data-bs-toggle="offcanvas" data-bs-target="#det'.$row['id'].'" onclick="console.log('.$row['id'].');">Details</button>
    </div>
  </div>
  ';
}


echo "</div>
</table>";
echo '        <div class="col-md-12">
<div >
    <button id="addRowButton" class="btn btn-primary mt-1" style="width:90.6%"><i class="fa-regular fa-plus"></i></button>
  </div>

      
      
          </div>  
          
';




// Close the database connection
mysqli_close($conn);
?>