<?php

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}


session_start();

// check if user is not logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// set cookie
setcookie("username", $_SESSION["username"], time() + 3600, "/"); // cookie lasts for 1 hour

?>

<?php
require_once('project_DB.php');


// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$query = "SHOW TABLES LIKE 'projects'";
$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) == 1) {
// Query to fetch projects from the database
$sql = "SELECT * FROM projects";
$result = mysqli_query($conn, $sql);
}
// Close the database connection
mysqli_close($conn);




if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];
    console_log($project_id);
}


if (isset($_SESSION['warning'])) {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">' . $_SESSION['warning'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['warning']);
    
} 
?>















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
        .navbar-brand {
            margin-right: auto;
        }
  .project-container {
    position: relative;
    display: inline-block;
  }
  .close-button {
    position: absolute;
    top: 0;
    right: 0;
    margin: 5px;
    padding: 0;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
  }
</style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Project Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Projects</a>
                    </li>
                </ul>
            </div>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                <a href="logout.php" class="btn btn-danger">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <div class="list-group">
            <?php
                // Loop through the projects and display them in the sidebar

                while ($row = mysqli_fetch_assoc($result)) {



                  echo'<div class="d-flex align-items-center justify-content-between">
                  <a onclick="loadProject('.$row["id"].')" class="sidebar-project list-group-item list-group-item-action" data-project-id="'.$row["id"].'">'.$row["title"].'</a>
                  <button class="btn-close" onclick="deleteProject('.$row["id"].')"></button>
                </div>';

                }
                ?>
                <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#addProjectModal">
            Add Project
            
        </a>
            </div>
        </div>

        <!-- Content -->
        <div class="col-md-9" id="projectContent">


     
            
            


        </div>
    </div>
    <div class="container-fluid">
  <div class="row">
    <div class="col">
      <!-- Rows go here -->
    </div>
    <div class="col-md-7" id="details">
      <!-- Details go here -->
    </div>
  </div>
</div>


<div id="data-container"></div>






    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProjectModalLabel">Add Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="addproject.php" method="POST">
                                <div class="mb-3">
                                    <label for="projectTitle" class="form-label">Project Title </label>
                                    <input type="text" class="form-control" id="projectTitle" name="projectTitle" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
</div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        
function loadProject(projectId) {
    setActiveProject(projectId);
  // Make an AJAX call to fetch the project details
  $.ajax({
    url: 'getproject.php',
    type: 'POST',
    data: { id: projectId },
    success: function(response) {
      // Update the project content with the fetched data
      $('#projectContent').html(response);
            // Set the active project
            console.log("loadproject with projectid = "+projectId);
            history.pushState(null, null, '?project_id=' + projectId);
    }

  });

  $.ajax({
      url: 'getdetails.php',
      type: 'POST',
      data: { id: projectId },
      dataType: 'json',
      success: function(data) {
        // Clear the data container
        $('#data-container').html('');

        // Loop through each row and add it to the data container
        $.each(data, function(index, row) {
          $('#data-container').append('<div class="offcanvas offcanvas-end" tabindex="-1" id="'+"det"+row.id+'" aria-labelledby="panelTitle" data-bs-backdrop="false" data-bs-scroll="true"> <div class="offcanvas-header"> <h5 class="offcanvas-title" id="panelTitle">'+row.name+'</h5> <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button> </div> <div class="offcanvas-body"><div class="row align-items-center" id="d'+row.id+'"> <div class="col-9"> <p class="text-break">'+row.details+'</p> <textarea type="text" style="display:none" rows="20" cols="34""> </textarea></div> <div class="col-1"> <button onclick="editdet('+row.id+')" class="btn btn-primary edit-btn" data-row-id="'+row.id+'">Edit</button> <button onclick="savedet('+row.id+')" class="btn btn-success save-btn" data-row-id="'+row.id+'" style="display:none">Save</button> </div> </div></div> </div>');
          console.log(row.id+" "+row.name + row.details);
        });
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log('Error fetching data: ' + textStatus + ', ' + errorThrown+"  FAILED");
      }
    });

}
  </script>
 <!-- <script>
  $(document).ready(function() {
    console.log(projectId);
});
</script>   -->     


<script>
function setActiveProject(projectId) {
  // Update the activeProject variable
  console.log("setactiveproject: "+projectId);
  // Apply the 'active' class to the active project link
  $('.sidebar-project').removeClass('active');

// Add the active class to the clicked project
$('a[data-project-id="' + projectId + '"]').addClass('active');
}
</script>
<script>
  $(document).ready(function() {
    // Get the active project ID from the URL query string
    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var activeProjectId = urlParams.get('project_id');
    console.log("page loaded, query string: " + queryString);
    console.log("page loaded, url params: " + urlParams);
    console.log("page loaded, id: " + activeProjectId);
    if (activeProjectId != null){
    // Load the active project
    loadProject(activeProjectId);
    // Show the edit container when the edit button is clicked





    }
  });
</script>








<script>

function delrow(rowId) {
  var queryString = window.location.search;
  var urlParams = new URLSearchParams(queryString);
  var activeProjectId = urlParams.get('project_id');
// Get the row element
if (confirm("Are you sure you want to delete this task?")) {
// Get the value of the input box


// Set the new value to the text element

// Hide the input box and show the text

// Make AJAX call to save row to database
$.ajax({
  url: "delrow.php",
  type: "POST",
  data: { id: rowId, project_id: activeProjectId },
  success: function(response) {
    // If the row was saved successfully, update the row text
    console.log("deleted row with: "+activeProjectId)
    loadProject(response);
    location.reload();
  },
  error: function(xhr, status, error) {
    console.error(xhr.responseText);
    alert("Error saving row. Please try again.");
  }
  
});
}
}








function editdet(detId) {
  // Get the details element

  var det = document.getElementById("d"+detId);

  // Hide the text and show the input box
  det.getElementsByTagName('p')[0].style.display = 'none';
  det.getElementsByTagName('textarea')[0].style.display = 'block';

  // Set the input box value to the current text
  var text = det.getElementsByTagName('p')[0].innerText;
  det.getElementsByTagName('textarea')[0].value = text;

  // Hide the Edit button and show the Save button
  det.getElementsByTagName('button')[0].style.display = 'none';
  det.getElementsByTagName('button')[1].style.display = 'block';
}

function savedet(detId) {

    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var activeProjectId = urlParams.get('project_id');
  // Get the details element
  var det = document.getElementById("d"+detId);
// Get the value of the textarea box
  var newValue = det.getElementsByTagName('textarea')[0].value;

  // Set the new value to the text element
  det.getElementsByTagName('p')[0].innerText = newValue;

  // Hide the textarea box and show the text
  det.getElementsByTagName('p')[0].style.display = 'block';
  det.getElementsByTagName('textarea')[0].style.display = 'none';

  // Hide the Save button and show the Edit button
  det.getElementsByTagName('button')[0].style.display = 'block';
  det.getElementsByTagName('button')[1].style.display = 'none';
  
  // Make AJAX call to save details to database
  $.ajax({
    url: "savedet.php",
    type: "POST",
    data: { text: newValue, id: detId, project_id: activeProjectId },
    success: function(response) {
      // If the details was saved successfully, update the details text
      loadProject(response);
    },
    error: function(xhr, status, error) {
      console.error(xhr.responseText);
      alert("Error saving details. Please try again.");
    }
    
});
}



function editRow(rowId) {
  // Get the row element

  var row = document.getElementById(rowId);

  // Hide the text and show the input box
  row.getElementsByTagName('p')[0].style.display = 'none';
  row.getElementsByTagName('input')[0].style.display = 'block';

  // Set the input box value to the current text
  var text = row.getElementsByTagName('p')[0].innerText;
  row.getElementsByTagName('input')[0].value = text;

  // Hide the Edit button and show the Save button
  row.getElementsByTagName('button')[0].style.display = 'none';
  row.getElementsByTagName('button')[1].style.display = 'block';
}

function saveRow(rowId) {
  console.log(rowId);
  var queryString = window.location.search;
  var urlParams = new URLSearchParams(queryString);
  var activeProjectId = urlParams.get('project_id');
// Get the row element
var row = document.getElementById(rowId);
// Get the value of the input box
var newValue = row.getElementsByTagName('input')[0].value;

// Set the new value to the text element
row.getElementsByTagName('p')[0].innerText = newValue;

// Hide the input box and show the text
row.getElementsByTagName('p')[0].style.display = 'block';
row.getElementsByTagName('input')[0].style.display = 'none';

// Hide the Save button and show the Edit button
row.getElementsByTagName('button')[0].style.display = 'block';
row.getElementsByTagName('button')[1].style.display = 'none';

// Make AJAX call to save row to database
$.ajax({
  url: "saverow.php",
  type: "POST",
  data: { text: newValue, id: rowId, project_id: activeProjectId },
  success: function(response) {
    // If the row was saved successfully, update the row text
    console.log("saved row with: "+activeProjectId)
    loadProject(response);
  },
  error: function(xhr, status, error) {
    console.error(xhr.responseText);
    alert("Error saving row. Please try again.");
  }
  
});
   
}

function deleteProject(projectId) {
  if (confirm("Are you sure you want to delete this project?")) {
    $.ajax({
      url: "delete_project.php",
      method: "POST",
      data: { id: projectId },
      success: function(response) {
        // If the request was successful, remove the corresponding row from the sidebar
        location.reload();

      },
      error: function(xhr, status, error) {
        // Handle errors here
      }
    });
  }
}

  function savestate(newValue, rowId) {
  console.log(rowId);
  var queryString = window.location.search;
  var urlParams = new URLSearchParams(queryString);
  var activeProjectId = urlParams.get('project_id');

// Make AJAX call to save row to database
$.ajax({
  url: "savestate.php",
  type: "POST",
  data: { text: newValue, id: rowId, project_id: activeProjectId },
  success: function(response) {
    // If the row was saved successfully, update the row text
    console.log("saved state with: "+activeProjectId)
    loadProject(response);
    location.reload();
  },
  error: function(xhr, status, error) {
    console.error(xhr.responseText);
    alert("Error saving row. Please try again.");
  }
  
});
   


}
</script>


</body>
</html>

