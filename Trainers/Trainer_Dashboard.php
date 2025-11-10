<!-- Trainer_Dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trainer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
  <link rel="stylesheet" href="Trainer_Dashboard.css">
  <link rel="stylesheet" href="SideBar.css">
</head>

<body class="body">

  <!-- Toggle button for mobile -->
  <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
    <i class="bi bi-list"></i>
  </button>

    <!-- Side Bar -->
    <?php include 'Trainer_sidebar.php'; ?>

    <!------------------- Main Container for TRAINER DASHBOARD ----------------------->

    <div class="content-wrapper">
      <div class="main-container">
        <div class="container-fluid px-4 pt-4 dashboard-container">
          
          <!-- Header -->
          <div class="row mb-4"> 
            <div class="col-12">
              <h1>Welcome, Coach!</h1>
              <div class="welcome-subtext">MANAGE YOUR CLIENTS!</div>
            </div>
          </div>

          <!-- Top Stats Row (mimics client's stats layout) -->
          <div class="row mb-4 gx-4 gy-4">
            <div class="col-12 col-lg-8">
                <!-- Stat Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Members</h5>
                                <p class="card-text" id="totalMembers">130</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Active Members</h5>
                                <p class="card-text" id="totalActiveMembers">80</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Clients</h5>
                                <p class="card-text" id="totalClients">14</p>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Pending Assessments</h5>
                                <p class="card-text" id="pendingAssessments">3</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications/Activities Card -->
                <div class="activities-container">
                    <div class="activities-header-row">
                        <div class="accent-bar"></div>
                        <div class="activities-header">RECENT <span class="new-updates">ACTIVITIES</span></div>
                    </div>
                    <div class="activity-list" id="activityList">
                        <div class="activity-item">
                            <span class="activity-text">John Dela Cruz completed Leg Day.</span>
                            <small class="timestamp">[June 20, 2025 | 11:32 AM]</small>
                        </div>
                        <div class="activity-item">
                            <span class="activity-text">Louisa Ramirez finished all workouts in the current plan.</span>
                            <small class="timestamp">[June 20, 2025 | 10:27 AM]</small>
                        </div>
                        <div class="activity-item">
                            <span class="activity-text">Janine Zamora uploaded a new health assessment.</span>
                            <small class="timestamp">[June 19, 2025 | 08:44 PM]</small>
                        </div>
                        <div class="activity-item">
                            <span class="activity-text">Kendrick Isaiah Garcia has been assigned to you.</span>
                            <small class="timestamp">[June 19, 2025 | 07:01 PM]</small>
                        </div>
                        <div class="activity-item">
                            <span class="activity-text">Tom Rao Santos completed 100% of this week's workout plan.</span>
                            <small class="timestamp">[June 19, 2025 | 06:30 PM]</small>
                        </div>
                        <div class="activity-item">
                            <span class="activity-text">Clai Dela Cruz completed Upper Body Strength Training.</span>
                            <small class="timestamp">[June 19, 2025 | 01:06 PM]</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PART (Calendar & Schedules & Messages) -->
            <div class="col-12 col-lg-4">
                <div class="dashboard-right d-flex flex-column">

                    <!-- Calendar Container -->
                    <div class="calendar-container">
                        <div class="calendar-header w-100 d-flex align-items-center justify-content-between">
                            <button class="btn btn-link text-white p-0" id="prevMonthBtn" aria-label="Previous month">
                                <i class="bi bi-chevron-left" style="font-size:1.6rem;"></i>
                            </button>

                            <div class="d-flex align-items-center gap-2">
                                <div class="calendar-title" id="calendarMonthYear" role="button" tabindex="0"></div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-light" id="monthPickerBtn">Month</button>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-light" id="yearPickerBtn">Year</button>
                                </div>
                            </div>

                            <button class="btn btn-link text-white p-0" id="nextMonthBtn" aria-label="Next month">
                                <i class="bi bi-chevron-right" style="font-size:1.6rem;"></i>
                            </button>
                        </div>

                        <div class="calendar-dates mt-2" id="calendarDates" aria-live="polite"></div>
                    </div>

                    <!-- Month Picker Panel -->
                    <div class="picker-panel d-none" id="monthPanel" role="dialog" aria-modal="false">
                        <div class="picker-list p-2" id="monthsList"></div>
                    </div>

                    <!-- Year Picker Panel -->
                    <div class="picker-panel d-none" id="yearPanel" role="dialog" aria-modal="false">
                        <div class="picker-list p-2" id="yearsList"></div>
                    </div>

                    <!-- Add schedule bar -->
                    <form class="add-schedule-bar mt-3 d-flex" id="addScheduleForm" autocomplete="off">
                        <input type="text" class="add-schedule-input" id="scheduleInput" placeholder="Add your Schedule Here">
                        <button type="button" class="add-schedule-btn" id="openAddModal">Add</button>
                    </form>

                    <!-- Schedules Header -->
                    <div class="schedules-header-row mt-3 d-flex align-items-center justify-content-between">
                        <div class="schedules-title-group">
                            <div class="schedules-accent-bar accent-bar"></div>
                            <div class="schedules-title">MY SCHEDULES</div>
                        </div>
                        <a href="#" class="view-all-link" id="viewAllSchedules">view all</a>
                    </div>

                    <!-- Schedules List -->
                    <div id="schedulesList" class="mt-2">
                        <!-- schedule items inserted by JS -->
                    </div>

                    <!-- Messages Card -->
                    <div class="card messages-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Messages (2)</h5>
                                <a href="Trainer_Messenger.html" class="view-all">view all</a>
                            </div>
                            <div class="message-list">
                                <div class="message-item">
                                    <div class="message-sender">Clai Dela Cruz</div>
                                    <div class="message-preview">Noted Coach! Thanks!</div>
                                    <small class="message-time">1h ago</small>
                                </div>
                                <div class="message-item">
                                    <div class="message-sender">Princess Mae Lopez</div>
                                    <div class="message-preview">Hello Coach Chad! Hihingi po sana ako ng advice about sa leg day workout if ...</div>
                                    <small class="message-time">3h ago</small>
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
  
  <!-- Add/Edit Schedule Modal -->
  <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-light">
        <div class="modal-header border-bottom">
          <h5 class="modal-title" id="addScheduleModalLabel">Add Schedule</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Title</label>
          <input id="modalScheduleTitle" class="form-control mb-3" placeholder="e.g. Client session at 7:00 AM">
          <label class="form-label">Type</label>
          <select id="modalScheduleType" class="form-select mb-3">
            <option value="default">Select Type</option>
            <option value="session">Client Session</option>
            <option value="meeting">Meeting</option>
            <option value="admin">Admin Task</option>
            <option value="other">Other</option>
          </select>
          <label class="form-label">Date</label>
          <input id="modalScheduleDate" type="date" class="form-control mb-3">
          <label class="form-label">Time</label>
          <input id="modalScheduleTime" type="time" class="form-control mb-3">
          <input type="hidden" id="modalScheduleId"> <!-- Hidden input for editing -->
        </div>
        <div class="modal-footer justify-content-center border-top">
          <button type="button" class="btn btn-danger" id="saveScheduleBtn">Save</button>
          <button type="button" class="btn btn-outline-secondary" id="deleteScheduleBtn" style="display:none;">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Schedules Modal (View All) -->
  <div class="modal fade" id="schedulesModal" tabindex="-1" aria-labelledby="schedulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-dark text-light">
        <div class="modal-header">
          <h5 class="modal-title" id="schedulesModalLabel">All My Schedules</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modalSchedulesList"></div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script> 
  <script src="Trainer_Dashboard.js"></script>
  <script src="Sidebar.js"></script> <!-- Assuming Sidebar.js still handles general sidebar logic -->

</body>
</html>