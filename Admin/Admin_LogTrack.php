<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Log & Track Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
  <link rel="stylesheet" href="UserPage.css"> <!-- Reusing UserPage.css for table styling -->
  <link rel="stylesheet" href="SideBar.css">
</head>
<body class="body">
  <!-- Toggle button -->
  <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
    <i class="bi bi-list"></i>
  </button>

    <!-- Side Bar -->
    <?php include 'Admin_sidebar.php'; ?>
  
  <!-------------------------- Main Container for LOG & TRACK PAGE ------------------------->

  <div class="content-wrapper">
    <div class="main-container">
      <div class="container-fluid h-100">
    
    <!-- Header -->
    <div class="row mb-3">
      <div class="col">
        <h1>LOG & TRACK</h1>
      </div>
    </div>

    <!-- Search & Sort -->
    <div class="row mb-3">
      <div class="col d-flex gap-3">
        <div class="search-bar" style="width: 300px;">
          <i class="bi bi-search" style="color:#F6F9F7;"></i>
          <input type="text" id="searchInput" placeholder="Search">
        </div>
        <div class="dropdown">
          <button class="sort-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-sort-down"></i> Filter by
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">All</a></li>
            <li><a class="dropdown-item" href="#">Members</a></li>
            <li><a class="dropdown-item" href="#">Trainers</a></li>
            <li><a class="dropdown-item" href="#">Activities</a></li>
          </ul>
        </div>
        <div class="dropdown">
          <button class="sort-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-sort-down"></i> Sort by
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="sortTable('oldest')">Oldest</a></li>
            <li><a class="dropdown-item" href="#" onclick="sortTable('newest')">Newest</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table id="memberTable">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>User Name</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody id="memberList">
          <tr>
            <td>06/20/2025 - 12:54 PM</td>
            <td>Chad Mendoza</td>
            <td>Messaged Cyrus Almeda.</td>
          </tr>
          <tr>
            <td>06/20/2025 - 11:32 AM</td>
            <td>John Dela Cruz</td>
            <td>Completed Leg Day.</td>
          </tr>
          <tr>
            <td>06/20/2025 - 10:27 AM</td>
            <td>Cyrus Almeda</td>
            <td>Messaged Trainer Chad Mendoza.</td>
          </tr>
          <tr>
            <td>06/19/2025 - 08:44 PM</td>
            <td>Janine Zamora</td>
            <td>Uploaded a new health assessment.</td>
          </tr>
          <tr>
            <td>06/19/2025 - 07:01 PM</td>
            <td>Kendrick Isaiah Garcia</td>
            <td>Assigned to Trainer Chad Mendoza.</td>
          </tr>
          <tr>
            <td>06/19/2025 - 06:30 PM</td>
            <td>Tom Rae Santos</td>
            <td>Completed 100% of this week's workout plan.</td>
          </tr>
          <tr>
            <td>06/19/2025 - 06:30 PM</td>
            <td>Tom Rae Santos</td>
            <td>Completed HIIT Workout - Day 7.</td>
          </tr>
          <tr>
            <td>06/19/2025 - 01:06 PM</td>
            <td>Cyrus Almeda</td>
            <td>Completed Upper Body Strength Training.</td>
          </tr>
           <tr>
            <td>06/19/2025 - 12:11 PM</td>
            <td>John Lloyd Cruz</td>
            <td>Marked Rest Day Recovery as completed.</td>
          </tr>
           <tr>
            <td>06/19/2025 - 08:03 AM</td>
            <td>John Lloyd Cruz</td>
            <td>Completed Strength Training workout.</td>
          </tr>
        </tbody>
      </table>
      <div id="noMatch" class="text-center text-white mt-3" style="display: none;">No Match</div>
    </div>
  </div>
</div>
</div>

<script src="Sidebar.js"></script>
<script src="UserPage.js"></script> <!-- Reusing UserPage.js for search and sort -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script> 

</body>
</html>
