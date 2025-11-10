<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archives</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="SideBar.css"> 
    <link rel="stylesheet" href="Archive.css">
</head>

<body class="body"> 
    
   <!-- Toggle button -->
     <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Client_sidebar.php'; ?>

    <!-------------------------------------------------- ARCHIVE CONTENT  -------------------------------------------------->
    
    <div class="content-wrapper">
        <div class="main-container">
            <!-- Header Section -->
            <div class="header-bar">
                <span class="header-title">Archive</span>
                <div class="position-relative">
                    <button class="sort-btn" id="sortBtn">
                        Sort <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <div class="sort-dropdown d-none" id="sortDropdown">
                        <div class="sort-dropdown-option" data-sort="newest">Newest</div>
                        <div class="sort-dropdown-option" data-sort="oldest">Oldest</div>
                    </div>
                </div>
            </div>

            <!-- Archive List -->
            <div class="archive-list" id="archiveList">
                <!-- Sample Item -->
                <div class="archive-item" data-id="1">
                    <span class="archive-info">Annual Physical Exam</span>
                    <div class="archive-right">
                        <span class="archive-date archive-info">2024-06-01</span>
                        <button class="restore-btn" data-bs-toggle="modal" data-bs-target="#archiveModal" data-id="1">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                </div>

                <!-- Dagdag Items kapag need pa -->
                <div class="archive-item" data-id="2">
                    <span class="archive-info">Blood Pressure Check</span>
                    <div class="archive-right">
                        <span class="archive-date archive-info">2024-05-15</span>
                        <button class="restore-btn" data-bs-toggle="modal" data-bs-target="#archiveModal" data-id="2">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Assessment Modal -->
    <div class="modal fade" id="assessmentModal" tabindex="-1" aria-labelledby="assessmentModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="assessmentModalLabel">Health Assessment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="assessmentModalBody">
            <!-- Assessment info pwede here -->
          </div>
        </div>
      </div>
    </div>

    <!-- Archive Confirmation Modal -->
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Restore History</h5>
          </div>
          <div class="modal-body">
            <div class="modal-subheading">Are you sure?</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-yes px-4" id="archiveYesBtn">Yes</button>
            <button type="button" class="btn btn-no px-4" data-bs-dismiss="modal">No</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Sidebar.js"></script>  <!-- ADD THIS -->
    <script src="Archive.js"></script>
</body>
</html>
