<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
  <link rel="stylesheet" href="ClientPage.css">
  <link rel="stylesheet" href="SideBar.css">
</head>

<body class="body">

  <!-- Toggle button -->
  <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
    <i class="bi bi-list"></i>
  </button>

    <!-- Side Bar -->
    <?php include 'Trainer_sidebar.php'; ?>

    <!------------------- Main Container for CLIENT PAGEEEE ----------------------->

    <div class="content-wrapper">
      <div class="main-container">
        <div class="container-fluid h-100">
    
    <!-- Header -->
    <div class="row mb-3"> 
      <div class="col">
        <h1>CLIENTS</h1>
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
            <li><a id="choices" class="dropdown-item" href="#" onclick="sortTable('oldest')">Oldest</a></li>
            <li><a id="choices" class="dropdown-item" href="#" onclick="sortTable('newest')">Newest</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table id="memberTable">
        <thead>
          <tr>
            <th>Client Name</th>
            <th>Member Since</th>
            <th>Email</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="memberList">
          <tr>
            <td>Maria Clara</td>
            <td>2023-05-01</td>
            <td>mariaclara@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a id="list-choices" class="dropdown-item" href="#">Message</a></li>
                  <li><a id="list-choices" class="dropdown-item" href="#">View Client Progress</a></li>
                  <li><a id="list-choices" class="dropdown-item" href="#">View Health Assessment</a></li>
                  <li><a id="list-choices" class="dropdown-item" href="#">Edit Nutrition/Diet Plan</a></li>
                  <li><a id="list-choices" class="dropdown-item" href="#">Edit Workout Plan</a></li>
                </ul>
              </div>
            </td>
          </tr>
          <tr>
            <td>Crisostomo Ibarra</td>
            <td>2024-01-15</td>
            <td>crisostomoibarra@gmail.com</td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm text-white" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a id="list-choices" class="dropdown-item" href="#">Message</a></li>
                  <li><a id="list-choices" class="dropdown-item" href="#">View Client Progress</a></li>
                  <li><a id="list-choices" class="dropdown-item" href="#">View Health Assessment</a></li>
                  <li><a id="list-choices" class="dropdown-item" href="#">Edit Nutrition/Diet Plan</a></li>
                  <li><a id="list-choices" class="dropdown-item" href="#">Edit Workout Plan</a></li>
                </ul>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div id="noMatch" class="text-center text-white mt-3" style="display: none;">No Match</div>
    </div>
  </div>

  <!--------------------------------------- POPPED UP MODALS CONTAINERS SIDE NA I2 --------------------------------------- >

  <!-- Client Progress Modal -->
<div class="modal fade" id="clientProgressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content progress-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-white" id="clientName"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <hr class="m-0 text-white">
      <div class="modal-body text-white">

       <div class="row mt-3">
  <!-- Full Width Stats -->
  <div class="col-12">
    <div class="row mb-4">
      <div class="col-md-6 col-12 mb-3">
        <h6 class="fw-bold">Workout Progress</h6>
        <p id="workoutsLeft">15 Workouts Left</p>
        <h6 class="fw-bold">Goal Everyday</h6>
        <p id="goalEveryday">2,500 KCal</p>
      </div>
      <div class="col-md-6 col-12 mb-3">
        <h6 class="fw-bold">Completed Workout</h6>
        <p id="completedWorkout">10 Workouts</p>
        <h6 class="fw-bold">Missed Workout</h6>
        <p id="missedWorkout">2 Workouts</p>
      </div>
    </div>
  </div>
</div>

        <!-- Upcoming Session -->
        <div class="mt-4">
          <h6 class="fw-bold">Upcoming Session</h6>
          <ul class="mt-2" id="upcomingSessions">
            <li>
              Next Workout: Upper Body Strength (Push Focus) <br>
              <small>Scheduled For: June 20, 2025 | 06:00 PM</small>
            </li>
            <li class="mt-2">
              Next Workout: Lower Body Strength <br>
              <small>Scheduled For: June 21, 2025 | 06:00 PM</small>
            </li>
            <li class="mt-2">
              Next Workout: Active Recovery | Cardio + Stretch <br>
              <small>Scheduled For: June 22, 2025 | 06:00 PM</small>
            </li>
            <li class="mt-2">
              Next Workout: Upper Body Strength (Pull Focus) <br>
              <small>Scheduled For: June 23, 2025 | 06:00 PM</small>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

  <!------------------------------------------- POPPED UP MODALS FOR HEALTH ASSESSEMENT -------------------------------------------->

  <!-- Health Assessment Modal -->
<div class="modal fade" id="healthAssessmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content health-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-white" id="healthClientName"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <hr class="m-0 text-white">
      <div class="modal-body text-white">
        
        <!-- Title -->
        <div class="mt-3">
          <h6 class="fw-bold">Health Assessment History</h6>
        </div>

        <!-- List of Health Assessments -->
        <div class="row mt-3">
          <div class="col-12">
            <ul class="list-unstyled" id="healthAssessmentList">
              <li class="assessment-item">June 03, 2025 - Health Assessment</li>
              <li class="assessment-item">May 03, 2025 - Health Assessment</li>
              <li class="assessment-item">April 03, 2025 - Health Assessment</li>
              <li class="assessment-item">March 03, 2025 - Health Assessment</li>
              <li class="assessment-item">February 03, 2025 - Health Assessment</li>
              <li class="assessment-item">January 03, 2025 - Health Assessment</li>
              <li class="assessment-item">December 03, 2024 - Health Assessment</li>
              <li class="assessment-item">November 03, 2024 - Health Assessment</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!------------------------------------------- POPPED UP MODALS FOR NUTRITION/DIET PLAN -------------------------------------------->

<!-- Nutrition/Diet Plan Modal -->
<div class="modal fade" id="nutritionPlanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content nutrition-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-white" id="nutritionClientName"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <hr class="m-0 text-white">

      <div class="modal-body text-white">
        <div class="row">
          <!-- LEFT PART -->
          <div class="col-lg-5 col-md-12 mb-4">
            <h6 class="fw-bold">Edit Nutrition/Diet Plan</h6>

            <!-- Dropdown -->
            <div class="dropdown mt-3">
              <button class="btn w-100 d-flex justify-content-between align-items-center dropdown-toggle nutrition-dropdown" type="button" data-bs-toggle="dropdown">
                Breakfast <i class="bi bi-chevron-down"></i>
              </button>
              <ul class="dropdown-menu w-100 nutrition-dropdown-menu">
                <li><a class="dropdown-item" href="#">Lunch</a></li>
                <li><a class="dropdown-item" href="#">Snack</a></li>
                <li><a class="dropdown-item" href="#">Dinner</a></li>
              </ul>
            </div>

            <!-- Dish Input -->
            <input type="text" class="form-control nutrition-input mt-3" placeholder="Name of Dish">

            <!-- Calories -->
            <label id="calories" class="fw-bold mt-3">Calories:</label>
            <input type="text" class="form-control nutrition-input" placeholder="No. of Calories">

            <!-- Ingredients -->
            <label id="ingredients" class="fw-bold mt-3">Ingredients:</label>
            <textarea class="form-control nutrition-input" rows="4" placeholder="Type here"></textarea>

            <!-- Buttons -->
            <div class="d-flex gap-3 mt-4">
              <button class="btn btn-add flex-fill">Add</button>
              <button class="btn btn-delete flex-fill">Delete</button>
            </div>
          </div>

          <!-- RIGHT PART -->
          <div class="col-lg-7 col-md-12">
            <!-- Tabs -->
            <ul class="nav nav-tabs nutrition-tabs mb-3" id="nutritionTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="breakfast-tab" data-bs-toggle="tab" data-bs-target="#breakfast" type="button" role="tab">Breakfast</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="lunch-tab" data-bs-toggle="tab" data-bs-target="#lunch" type="button" role="tab">Lunch</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="snack-tab" data-bs-toggle="tab" data-bs-target="#snack" type="button" role="tab">Snack</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="dinner-tab" data-bs-toggle="tab" data-bs-target="#dinner" type="button" role="tab">Dinner</button>
              </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content nutrition-content">
              <div class="tab-pane fade show active" id="breakfast" role="tabpanel">
                <div class="nutrition-item">Simple Egg & Kangkong Stir-Fry</div>
                <div class="nutrition-item">Fried Tofu with Egg</div>
              </div>
              <div class="tab-pane fade" id="lunch" role="tabpanel">
                <div class="nutrition-item">Grilled Chicken Breast with Veggies</div>
              </div>
              <div class="tab-pane fade" id="snack" role="tabpanel">
                <div class="nutrition-item">Banana Smoothie</div>
              </div>
              <div class="tab-pane fade" id="dinner" role="tabpanel">
                <div class="nutrition-item">Steamed Fish with Rice</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!------------------------------------------- POPPED UP MODALS FOR WORKOUT PLAN -------------------------------------------->

<!-- Workout Plan Modal -->
<div class="modal fade" id="workoutPlanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content workout-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-white" id="workoutClientName"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <hr class="m-0 text-white">

      <div class="modal-body text-white">
        <div class="row">
          <!-- LEFT PART -->
          <div class="col-lg-5 col-md-12 mb-4">
            <h6 class="fw-bold">Edit Workout Plan</h6>

            <!-- Scheduled Workout -->
            <label class="fw-bold mt-3 schedule">Scheduled Workout:</label>
              <div class="input-group workout-date">
                <input type="date" class="form-control calendar" id="dob" placeholder="MM/DD/YYYY" required>
              </div>

            <!-- Title -->
            <label class="fw-bold mt-3 title-header">Title:</label>
            <input type="text" class="form-control workout-input" placeholder="Title">

            <!-- Workout Sets and Notes -->
            <label class="fw-bold mt-3 workout-title">Workout Sets and Notes:</label>
            <textarea class="form-control workout-input" rows="4" placeholder="Type Here"></textarea>

            <!-- Buttons -->
            <div class="d-flex gap-3 mt-4">
              <button class="btn btn-add flex-fill" id="addWorkout">Add</button>
              <button class="btn btn-delete flex-fill">Delete</button>
            </div>
          </div>

          <!-- RIGHT PART -->
          <div class="col-lg-7 col-md-12">
            <h6 id="current-wokrout" class="fw-bold mb-3">Current Workout Plan</h6>
            <div id="workoutList" class="workout-list">
              <!-- WORKOUT ITEMS MAG AAPPEAR here -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>

    </div>

  


<script src="Sidebar.js"></script>
<script src="ClientPage.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script> 

</body>
</html>
