<?php

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}



require_once('config.php');


$guestidentifier = $_SESSION["guest_identifier"];

// Retrieve the share identifier from the database
$sql = "SELECT identifier FROM users WHERE guest_identifier = '$guestidentifier'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $shareIdentifier = $row['identifier'];

    // Store the share identifier in a session variable
    $_SESSION['identifier'] = $shareIdentifier;
}



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

  .notification {
        position: absolute;
        top: +80px;
        left: calc(100% - 79px);
        padding: 5px 10px;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .show-notification {
        opacity: 1;
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
                </div>';

                }
                ?>
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




</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        
function loadProject(projectId) {
    setActiveProject(projectId);
  // Make an AJAX call to fetch the project details
  $.ajax({
    url: 'guest_getproject.php',
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
          $('#data-container').append('<div class="offcanvas offcanvas-end" tabindex="-1" id="'+"det"+row.id+'" aria-labelledby="panelTitle" data-bs-backdrop="false" data-bs-scroll="true"> <div class="offcanvas-header"> <h5 class="offcanvas-title" id="panelTitle">'+row.name+'</h5> <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button> </div> <div class="offcanvas-body"><div class="row align-items-center" id="d'+row.id+'"> <div class="col-9"> <p class="text-break">'+row.details+'</p> <textarea type="text" style="display:none" rows="20" cols="34""> </textarea></div> <div class="col-1">  </div> </div></div> </div>');
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









</body>
</html>

