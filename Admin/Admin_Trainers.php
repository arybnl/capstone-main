<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Trainers Page</title>
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
    <?php include 'admin_sidebar.php'; ?>
  
  <!-------------------------- Main Container for TRAINER PAGE ------------------------->

  <div class="content-wrapper">
    <div class="main-container">
      <div class="container-fluid h-100">
    
    <!-- Header -->
    <div class="row mb-3">
      <div class="col">
        <h1>TRAINERS</h1>
      </div>
    </div>

    <!-- Search & Sort -->
    <div class="row mb-3">
      <div class="col d-flex gap-3">
        <div class="search-bar" style="width: 300px;">
          <i class="bi bi-search" style="color:#F6F9F7;"></i>
          <input type="text" id="searchInput" placeholder="Search for Trainers">
        </div>
        <div class="dropdown">
          <button class="sort-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-sort-down"></i> Sort
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
            <th>User Name</th>
            <th>Account Created</th>
            <th>Last Activity Time Stamp</th>
            <th>Email</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="memberList">
          <tr>
            <td>Chad Mendoza</td>
            <td>09/10/2024</td>
            <td>06/20/2025 - 12:54 PM</td>
            <td>chad.mendoza@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Make Admin</a></li>
                  <li><a class="dropdown-item" href="#">Remove Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
          <tr>
            <td>Francis De Leon</td>
            <td>06/03/2024</td>
            <td>06/19/2025 - 10:31 PM</td>
            <td>francis.deleon@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Make Admin</a></li>
                  <li><a class="dropdown-item" href="#">Remove Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
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
