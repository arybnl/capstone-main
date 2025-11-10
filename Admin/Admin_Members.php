<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Members Page</title>
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
  
  <!-------------------------- Main Container for USER PAGE ------------------------->

  <div class="content-wrapper">
    <div class="main-container">
      <div class="container-fluid h-100">
    
    <!-- Header -->
    <div class="row mb-3">
      <div class="col">
        <h1>MEMBERS</h1>
      </div>
    </div>

    <!-- Search & Sort -->
    <div class="row mb-3">
      <div class="col d-flex gap-3">
        <div class="search-bar">
          <i class="bi bi-search" style="color:#F6F9F7;"></i>
          <input type="text" id="searchInput" placeholder="Search for Members">
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
            <th>Member Since</th>
            <th>Last Activity Time Stamp</th>
            <th>Email</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="memberList">
          <tr>
            <td>Andrew John Gonzales</td>
            <td>11/03/2024</td>
            <td>11/03/2024</td>
            <td>06/18/2025 - 10:30 AM</td>
            <td>ajgonzales@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Assign to Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Remove Member</a></li>
                  <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
          <tr>
            <td>Clai Dela Cruz</td>
            <td>05/11/2025</td>
            <td>05/11/2025</td>
            <td>06/20/2025 - 10:27 AM</td>
            <td>claidela@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Assign to Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Remove Member</a></li>
                  <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
          <tr>
            <td>Janine Zamora</td>
            <td>12/28/2024</td>
            <td>12/28/2024</td>
            <td>06/19/2025 - 08:44 PM</td>
            <td>janine.zamora@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Assign to Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Remove Member</a></li>
                  <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
          <tr>
            <td>John Lloyd Cruz</td>
            <td>05/20/2025</td>
            <td>05/20/2025</td>
            <td>06/20/2025 - 11:32 AM</td>
            <td>johnlloydcruz@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Assign to Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Remove Member</a></li>
                  <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
          <tr>
            <td>Kendrick Isaiah Garcia</td>
            <td>06/19/2025</td>
            <td>06/19/2025</td>
            <td>06/18/2025 - 10:15 AM</td>
            <td>k.isaiahgarcia@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Assign to Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Remove Member</a></li>
                  <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
          <tr>
            <td>Lance Kenneth Valdez</td>
            <td>06/07/2025</td>
            <td>06/07/2025</td>
            <td>06/18/2025 - 12:53 PM</td>
            <td>lancekenvaldez@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Assign to Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Remove Member</a></li>
                  <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
          <tr>
            <td>Louisa Ramirez</td>
            <td>04/04/2025</td>
            <td>04/04/2025</td>
            <td>06/20/2025 - 02:09 PM</td>
            <td>louisaramirez@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Assign to Trainer</a></li>
                  <li><a class="dropdown-item" href="#">Remove Member</a></li>
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
