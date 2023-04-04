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
  <div class="col-7">
    <p>'.$row['name'].'</p>
    <input type="text" style="display:none">
  </div>
  <div class="col-1">
    <button onclick="editRow('.$rowid.')" class="btn btn-primary edit-btn" data-row-id="'.$rowid.'"> <i class="fa-solid fa-pen"></i> </button>
    <button onclick="saveRow('.$rowid.')" class="btn btn-success save-btn" data-row-id="'.$rowid.'" style="display:none"> <i class="fa-solid fa-check"></i> </button>
  </div>

  <div class="col-1">
  <button onclick="delrow('.$rowid.')" class="btn btn-danger delete-btn" data-row-id="'.$rowid.'"> <i class="fa-solid fa-xmark"></i> </button>
  </div>
  <div class="dropdown col-1">
  <button class="btn btn-'.$color.' dropdown-toggle" type="button" id="stateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
  '.$state.$padding.'
  </button>
  <ul class="dropdown-menu" aria-labelledby="stateDropdown">
    <li><a class="dropdown-item" onclick="savestate(\'Planning\','.$rowid.')">Planning</a></li>
    <li><a class="dropdown-item" onclick="savestate(\'Working\','.$rowid.')">Working</a></li>
    <li><a class="dropdown-item" onclick="savestate(\'Done\','.$rowid.')">Done</a></li>
  </ul>
</div> 



  <div class="col-1">
  <button class="btn btn-primary details-button" data-bs-toggle="offcanvas" data-bs-target="#det'.$rowid.'" onclick="console.log('.$rowid.');">Details</button>
  </div>
</div>';
}
echo "</table>";
echo '        <div class="col-md-12">
<div id="projectContent">

      <!-- Add Row Form -->
      <form action="addrow.php" method="POST">
              <input type="hidden" name="project_id" value="'.$project_id.'">
              <div class="form-group">
                  <input type="hidden" type="text" class="form-control" id="name" name="name" value="Task name here">
              </div>
              <div class="form-group">
              <select class="form-control" id="state" name="state" style="display: none;">
              <option value="planning" selected></option>
          </select>
              </div">
              <button type="submit" class="btn btn-primary mt-1" style="width:90.6%"><i class="fa-regular fa-plus"></i></button>
          </form>

      
      
          </div>  
';

// Close the database connection
mysqli_close($conn);
?>