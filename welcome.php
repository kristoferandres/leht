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
    <link rel="stylesheet" href="welcome.css">
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



                            <?php
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                  // Redirect logged-in users to the appropriate page based on their admin levels
                  if ($_SESSION["admin_low"]) {
                      echo '<button onclick="window.location.href = \'admin_low_page.php\';" class="button-link me-2">admin low page</button>';
                  } elseif ($_SESSION["admin_high"]) {
                      echo '<button onclick="window.location.href = \'admin_high_page.php\';" class="button-link me-2">admin high page</button>';
                  } elseif ($_SESSION["admin_super_high"]) {
                      echo '<button onclick="window.location.href = \'admin_super_high_page.php\';" class="button-link-super me-2">Admin Super High Page</button>';
                  }
                }
                ?>

            <li class="nav-item me">
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#shareModal">Share</button>
                </li>

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
            <div  class="list-group">
              <div id="project_list">
              <?php
                // Loop through the projects and display them in the sidebar

                while ($row = mysqli_fetch_assoc($result)) {



                  echo'<div class="d-flex align-items-center justify-content-between">
                  <a onclick="loadProject('.$row["id"].')" class="sidebar-project list-group-item list-group-item-action" data-project-id="'.$row["id"].'">'.$row["title"].'</a>
                  <button class="btn-close" onclick="deleteProject('.$row["id"].')"></button>
                </div>';

                }
                ?>

              </div>
            
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


<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Share Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group" id="shareDurationGroup">
                    <label for="shareDuration">Share Duration:</label>
                    <div class="d-flex align-items-center">
                        <input type="range" class="form-range flex-grow-1" min="1" max="24" value="1" id="shareDuration" name="shareDuration" oninput="updateDurationLabel(this.value)">
                        <span class="ms-2" id="durationLabel">1 Hour</span>
                    </div>
                </div>
                <div class="form-group" id="timeLeftGroup" style="display: none;">
                    <label for="timeLeft">Time Left:</label>
                    <div class="d-flex align-items-center">
                        <span class="ms-2" id="timeLeft"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="shareLink">Share Link:</label>
                    <div class="d-flex">
                        <span id="shareLink" class="form-control-plaintext flex-grow-1">Not sharing</span>
                        <button class="btn btn-outline-secondary ms-2" type="button" id="copyButton">Copy</button>
                        <div class="notification" id="copyNotification">Copied!</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button id="stopSharingButton" type="button" class="btn btn-danger" style="display: none;">Stop Sharing</button>
                    <button id="startSharingButton" type="button" class="btn btn-primary">Start Sharing</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>




 <!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProjectModalLabel">Add Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProjectForm">
                    <div class="mb-3">
                        <label for="projectTitle" class="form-label">Project Title</label>
                        <input type="text" class="form-control" id="projectTitle" name="projectTitle" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>



document.getElementById('addProjectForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Get form data
        var formData = new FormData(this);

        // Make an AJAX call
        $.ajax({
            url: 'addproject.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
    // Handle the success response here
    console.log(response);

    // Parse the JSON response
    const responseData = JSON.parse(response);

    // Extract the project ID and title from the response
    const projectId = responseData.projectId;
    const projectTitle = responseData.projectTitle;

    // Create the HTML for the new project entry
    const projectEntry = `
        <div class="d-flex align-items-center justify-content-between">
            <a onclick="loadProject(${projectId})" class="sidebar-project list-group-item list-group-item-action" data-project-id="${projectId}">${projectTitle}</a>
            <button class="btn-close" onclick="deleteProject(${projectId})"></button>
        </div>
    `;

    // Append the new project entry to the project_list div
    const projectList = document.getElementById('project_list');
    projectList.insertAdjacentHTML('beforeend', projectEntry);

  // Clear the input fields in the modal
document.getElementById('projectTitle').value = '';

    // Close the modal
    $('#addProjectModal').modal('hide');

},
            error: function(xhr, status, error) {
                // Handle the error response here
                console.error(xhr.responseText);
            }
        });
    });
    
        
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
      console.log("loadproject with projectid = " + projectId);
      history.pushState(null, null, '?project_id=' + projectId);

      // Add Row button click event listener
      const addRowButton = document.getElementById('addRowButton');
      addRowButton.addEventListener('click', function() {
        const projectID = projectId;
        // Make an AJAX call to addrow.php
        $.ajax({
          url: 'addrow.php',
          type: 'POST',
          data: { project_id: projectID },
          success: function(response) {
            // Handle the success response here
            console.log(response);
            const rowId = response.rowid;
            const name = response.name;
            const state = response.state;
            const color = response.color;
            const padding = response.padding;

            const row = `
              <div class="row align-items-center" id="row-${rowId}">
                <div class="col-7">
                  <p>${name}</p>
                  <input type="text" style="display:none">
                </div>
                <div class="col-1">
                  <button class="btn btn-primary edit-btn" data-row-id="${rowId}"> <i class="fa-solid fa-pen"></i> </button>
                  <button class="btn btn-success save-btn" data-row-id="${rowId}" style="display:none"> <i class="fa-solid fa-check"></i> </button>
                </div>
                <div class="col-1">
                  <button class="btn btn-danger delete-btn" data-row-id="${rowId}"> <i class="fa-solid fa-xmark"></i> </button>
                </div>
                <div class="dropdown col-1">
                  <button class="btn btn-${color} dropdown-toggle" type="button" id="stateDropdown${rowId}" data-bs-toggle="dropdown" aria-expanded="false">
                    ${state}${padding}
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="stateDropdown${rowId}">
                    <li><a class="dropdown-item" data-new-value="Planning" data-row-id="${rowId}">Planning</a></li>
                    <li><a class="dropdown-item" data-new-value="Working" data-row-id="${rowId}">Working</a></li>
                    <li><a class="dropdown-item" data-new-value="Done" data-row-id="${rowId}">Done</a></li>

                  </ul>
                </div> 
                <div class="col-1">
                  <button class="btn btn-primary details-button" data-bs-toggle="offcanvas" data-bs-target="#det${rowId}" onclick="console.log(${rowId});">Details</button>
                </div>
              </div>
            `;

            $('#projectContent-list').append(row);


            $('.dropdown-item').on('click', function() {
                const newValue = $(this).data('new-value');
                const rowId = $(this).data('row-id');
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const activeProjectId = urlParams.get('project_id');
                // Make AJAX call to save row to database
                $.ajax({
                  url: "savestate.php",
                  type: "POST",
                  data: { text: newValue, id: rowId, project_id: activeProjectId },
                  success: function(response) {
                    // If the row was saved successfully, update the row text
                    console.log("saved state with: " + rowId);
                    
                    // Update the visual appearance based on the new state
                    const stateButton = $(`#stateDropdown${rowId}`);
                    const dropdownItem = $(this);
                    const color = newValue === 'Done' ? 'success' : (newValue === 'Working' ? 'warning' : 'secondary bg-opacity-50');
                    const padding = newValue === 'Done' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : (newValue === 'Working' ? '&nbsp;' : '');
                    stateButton.removeClass().addClass(`btn btn-${color} dropdown-toggle`);
                    stateButton.html(`${newValue}${padding}`);
                    
                    // Close the dropdown menu
                    dropdownItem.closest('.dropdown-menu').removeClass('show');
                  },
                  error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert("Error saving row. Please try again.");
                  }
                });
              });

              


          },
          error: function(xhr, status, error) {
            // Handle the error response here
            console.error(xhr.responseText);
          }
        });
      });



      $('.dropdown-item').on('click', function() {
  const newValue = $(this).data('new-value');
  const rowId = $(this).data('row-id');
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const activeProjectId = urlParams.get('project_id');
  // Make AJAX call to save row to database
  $.ajax({
    url: "savestate.php",
    type: "POST",
    data: { text: newValue, id: rowId, project_id: activeProjectId },
    success: function(response) {
      // If the row was saved successfully, update the row text
      console.log("saved state with: " + rowId);
      
      // Update the visual appearance based on the new state
      const stateButton = $(`#stateDropdown${rowId}`);
      const dropdownItem = $(this);
      const color = newValue === 'Done' ? 'success' : (newValue === 'Working' ? 'warning' : 'secondary bg-opacity-50');
      const padding = newValue === 'Done' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : (newValue === 'Working' ? '&nbsp;' : '');
      stateButton.removeClass().addClass(`btn btn-${color} dropdown-toggle`);
      stateButton.html(`${newValue}${padding}`);
      
      // Close the dropdown menu
      dropdownItem.closest('.dropdown-menu').removeClass('show');
    },
    error: function(xhr, status, error) {
      console.error(xhr.responseText);
      alert("Error saving row. Please try again.");
    }
  });
});









    },
    error: function(xhr, status, error) {
      // Handle the error response here
      console.error(xhr.responseText);
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






$(document).on('click', '.delete-btn', function() {
  const rowId = $(this).data('row-id');
  var queryString = window.location.search;
  var urlParams = new URLSearchParams(queryString);
  var activeProjectId = urlParams.get('project_id');

  // Confirm deletion
  if (confirm("Are you sure you want to delete this task?")) {
    // Make AJAX call to delete row from the database
    $.ajax({
      url: "delrow.php",
      type: "POST",
      data: { id: rowId, project_id: activeProjectId },
      success: function(response) {
        // If the row was deleted successfully, update the project content
        console.log("Deleted row with ID: " + rowId);
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
        alert("Error deleting row. Please try again.");
      }
    });
  }
});









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
        const projectEntry = document.querySelector(`[data-project-id="${projectId}"]`).parentNode;
    
    // Remove the project entry element
    if (projectEntry) {
        projectEntry.remove();
    }

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

let countdownInterval;



function checkSharingStatus() {
    // Make an AJAX call to checksharingstatus.php
    $.ajax({
        url: 'checksharingstatus.php',
        type: 'POST',
        success: function(response) {
            // Parse the JSON response
            const data = JSON.parse(response);

            // Update the sharing link element based on the sharing status
            const shareLinkElement = document.getElementById('shareLink');

            const currentURL = window.location.href;
            const baseURL = currentURL.substring(0, currentURL.lastIndexOf('/') + 1);
            const shareLink = baseURL + '/?share=' + data.guestidentifier;

            if (data.status === '1') {
                // Sharing is active
                shareLinkElement.textContent = shareLink; // Assuming the link property contains the sharing link
                document.getElementById('startSharingButton').style.display = 'none';
                document.getElementById('stopSharingButton').style.display = 'inline-block';


                document.getElementById('shareDurationGroup').style.display = 'none';
                document.getElementById('timeLeftGroup').style.display = 'block';

                const timeLeftElement = document.getElementById('timeLeft');

            // Start the countdown timer if no countdown is currently running
            if (!countdownInterval) {
                startCountdown(data.endtime, timeLeftElement);
              }

            } else {
                // Sharing is inactive
                shareLinkElement.textContent = 'Not sharing';
                document.getElementById('startSharingButton').style.display = 'inline-block';
                document.getElementById('stopSharingButton').style.display = 'none';


                stopCountdown()




            document.getElementById('timeLeftGroup').style.display = 'none';
            document.getElementById('shareDurationGroup').style.display = 'block';

            }
        },
        error: function(xhr, status, error) {
            // Handle the error response here
            console.error(xhr.responseText);
        }
    });
}

// Event listener for the "Start Sharing" button
document.getElementById('startSharingButton').addEventListener('click', function () {
    startSharing();
});

// Event listener for the "Stop Sharing" button
document.getElementById('stopSharingButton').addEventListener('click', function () {
    stopSharing();
});

function startSharing() {
    const shareDuration = document.getElementById('shareDuration').value;

    // Make an AJAX call to startsharing.php
    $.ajax({
        url: 'startsharing.php',
        type: 'POST',
        data: { duration: shareDuration },
        success: function(response) {
            // Handle the success response here
            console.log(response);
            const data = JSON.parse(response);
            // Update the share link element with the generated share link
            const shareLinkElement = document.getElementById('shareLink');
            // Assuming you have the generated identifier stored in a variable called 'identifier'
            const currentURL = window.location.href;
            const baseURL = currentURL.substring(0, currentURL.lastIndexOf('/') + 1);
            const shareLink = baseURL + '/?share=' + data.guestidentifier;

            shareLinkElement.textContent = shareLink; // Assuming the response contains the generated share link

            // Update the button display
            document.getElementById('startSharingButton').style.display = 'none';
            document.getElementById('stopSharingButton').style.display = 'inline-block';

            document.getElementById('shareDurationGroup').style.display = 'none';
            document.getElementById('timeLeftGroup').style.display = 'block';

            const timeLeftElement = document.getElementById('timeLeft');

            // Start the countdown timer if no countdown is currently running
            if (!countdownInterval) {
                startCountdown(data.endtime, timeLeftElement);
              }








            
        },
        error: function(xhr, status, error) {
            // Handle the error response here
            console.error(xhr.responseText);
        }
    });
}

function stopSharing() {
    // Make an AJAX call to stopsharing.php
    $.ajax({
        url: 'stopsharing.php',
        type: 'POST',
        success: function(response) {
            // Handle the success response here
            console.log(response);

            // Clear the share link element
            const shareLinkElement = document.getElementById('shareLink');
            shareLinkElement.textContent = '';

            // Update the button display
            document.getElementById('startSharingButton').style.display = 'inline-block';
            document.getElementById('stopSharingButton').style.display = 'none';
            shareLinkElement.textContent = 'Not sharing';

            

            stopCountdown()




            document.getElementById('timeLeftGroup').style.display = 'none';
            document.getElementById('shareDurationGroup').style.display = 'block';






        },
        error: function(xhr, status, error) {
            // Handle the error response here
            console.error(xhr.responseText);
        }
    });
}


function startCountdown(endtime, timeLeftElement) {
    const endTime = new Date(endtime).getTime(); // Convert the endtime to milliseconds

    // Update the countdown every second
    countdownInterval = setInterval(function() {
        const now = new Date().getTime(); // Get the current time
        const timeLeft = endTime - now; // Calculate the remaining time
        // Check if the countdown has finished
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            countdownInterval = null;
            timeLeftElement.textContent = 'Expired';
            return;
        }

        // Convert the remaining time to hours, minutes, and seconds
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        // Display the remaining time in the time left element
        timeLeftElement.textContent = hours + 'h ' + minutes + 'm ' + seconds + 's';
    }, 1000);
}

function stopCountdown() {
  clearInterval(countdownInterval);
  countdownInterval = null;
}




// Check the sharing status when the modal is shown
$('#shareModal').on('shown.bs.modal', function () {
    checkSharingStatus();
});




function updateDurationLabel(value) {
  const durationLabel = document.getElementById('durationLabel');
  if (value == 1) {
    durationLabel.textContent = '1 Hour';
  } else {
    durationLabel.textContent = value + ' Hours';
  }
}


// Copy the share link text to the clipboard
function copyToClipboard() {
        const shareLinkElement = document.getElementById('shareLink');
        const shareLinkText = shareLinkElement.textContent;
        
        navigator.clipboard.writeText(shareLinkText)
            .then(() => {
                // Show a success message and hide it after 1 second
                const copyNotification = document.getElementById('copyNotification');
                copyNotification.classList.add('show-notification');
                
                setTimeout(() => {
                    copyNotification.classList.remove('show-notification');
                }, 1000);
                
                console.log('Share link copied to clipboard!');
            })
            .catch((error) => {
                console.error('Unable to copy share link: ', error);
            });
    }
    
    // Add a click event listener to the copy button
    const copyButton = document.getElementById('copyButton');
    copyButton.addEventListener('click', copyToClipboard);


</script>


</body>
</html>

