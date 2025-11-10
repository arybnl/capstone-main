<?php
// [Client side - "dashboard.php"]
session_start();
require_once '../config.php'; // Adjust path based on your folder structure

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'member') {
    header('Location: ../index.php'); // Redirect to login or home page
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = 'Guest'; // Default value

// Fetch user's first name
$sql_user = "SELECT first_name FROM Users WHERE user_id = ?";
if ($stmt_user = $conn->prepare($sql_user)) {
    $stmt_user->bind_param("s", $user_id);
    if ($stmt_user->execute()) {
        $stmt_user->bind_result($fetched_first_name);
        $stmt_user->fetch();
        $first_name = $fetched_first_name;
    }
    $stmt_user->close();
}

// Fetch member specific data
$goal_calories_kcal = 0;
$sql_member = "SELECT goal_calories_kcal FROM Members WHERE user_id = ?";
if ($stmt_member = $conn->prepare($sql_member)) {
    $stmt_member->bind_param("s", $user_id);
    if ($stmt_member->execute()) {
        $stmt_member->bind_result($fetched_goal_calories_kcal);
        $stmt_member->fetch();
        $goal_calories_kcal = $fetched_goal_calories_kcal;
    }
    $stmt_member->close();
}

// You'll need tables for workouts, programs, and schedules to fully populate the dashboard.
// For now, these will be placeholders or use client-side logic as in your original HTML.
// Below are suggestions for how you might fetch data if those tables existed.

// Placeholder for calorie burnt (would come from actual workout logs)
$calorie_burnt = 1823; // Static for now, replace with dynamic data later

// Placeholder for programs joined (would come from a 'MemberPrograms' table)
$programs_joined = [
    'HIIT Challenge',
    'Yoga Flow',
    'Cardio Blast'
];

// Placeholder for schedules (would come from a 'Schedules' table)
// For demonstration, let's assume a basic schedule table structure:
// CREATE TABLE Schedules (
//    schedule_id VARCHAR(36) PRIMARY KEY,
//    member_id VARCHAR(36) NOT NULL,
//    title VARCHAR(255) NOT NULL,
//    workout_type ENUM('weights', 'yoga', 'cardio', 'other'),
//    schedule_date DATE NOT NULL,
//    schedule_time TIME NOT NULL,
//    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    FOREIGN KEY (member_id) REFERENCES Members(member_id) ON DELETE CASCADE
// );
// You would then fetch these. For now, empty array.
$schedules = []; // This will be populated via AJAX or JS based on the original structure.
// If you want to fetch schedules from PHP, you'd add:
/*
$sql_schedules = "SELECT s.title, s.workout_type, s.schedule_date, s.schedule_time
                  FROM Schedules s
                  JOIN Members m ON s.member_id = m.member_id
                  WHERE m.user_id = ? ORDER BY s.schedule_date, s.schedule_time";
if ($stmt_schedules = $conn->prepare($sql_schedules)) {
    $stmt_schedules->bind_param("s", $user_id);
    if ($stmt_schedules->execute()) {
        $result_schedules = $stmt_schedules->get_result();
        while ($row = $result_schedules->fetch_assoc()) {
            $schedules[] = $row;
        }
    }
    $stmt_schedules->close();
}
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="SideBar.css">
</head>

<body class="body">

  <!-- Toggle button -->
     <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Client_sidebar.php'; ?>

  <!------------------------------ Main Container for DASHBOARDD -------------------------->

  <div class="content-wrapper">
    <div class="main-container">
      <div class="container-fluid px-4 pt-4 dashboard-container">
    <div class="row gx-4 gy-4">

      <!-- LEFT PART (8) -->
      <div class="col-12 col-lg-8">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($first_name); ?>!</div>
        <div class="sweaty">LET'S GET SWEATY!</div>

        <!-- PROGRESS CONTAINER (Interactive) -->
        <div class="progress-container d-flex flex-column flex-md-row align-items-center gap-4" data-bs-toggle="modal" data-bs-target="#detailedProgressModal">
          <div class="progress-left">
            <div class="progress-title">WORKOUT PROGRESS</div>
            <div class="progress-info">15 workouts</div> <!-- This value would typically be dynamic -->
            <div class="goal-title">YOUR GOAL TODAY</div>
            <div class="goal-info"><?php echo htmlspecialchars($goal_calories_kcal); ?> cal.</div>
          </div>

          <div class="progress-chart position-relative">
            <canvas id="progressPieChart" aria-label="Workout progress chart" role="img"></canvas>
            <div class="progress-perc" id="progressPerc" aria-hidden="true"></div>
          </div>
        </div>

        <!-- Stats row -->
        <div class="row stats-row g-3">
          <div class="col-12 col-md-6">
            <div class="calorie-container">
              <div class="calorie-header d-flex align-items-start gap-2">
                <div class="calorie-title">Calorie Burnt</div>
                <i class="bi bi-fire ms-auto fire-icon" aria-hidden="true"></i>
              </div>
              <div class="calorie-value"><?php echo htmlspecialchars(number_format($calorie_burnt)); ?> KCal</div>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="program-container">
              <div class="program-header-row">
                <div class="accent-bar"></div>
                <div class="program-header">PROGRAM <span class="joined">JOINED</span></div>
                <div class="ms-auto small view-all-programs" id="viewAllPrograms" data-type="programs">view all</div>
              </div>

              <div class="program-list">
                <?php if (!empty($programs_joined)): ?>
                    <?php foreach ($programs_joined as $program): ?>
                        <div class="program-notification">Joined: <?php echo htmlspecialchars($program); ?></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="program-notification">No programs joined yet.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- RIGHT PART (4) -->
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
            <div class="d-flex align-items-center gap-2">
              <div class="schedules-accent-bar"></div>
              <div class="schedules-title">SCHEDULES</div>
            </div>
            <a href="Tracker.php" class="view-all-link">view all</a>
          </div>

          <!-- Schedules List -->
          <div id="schedulesList" class="mt-2">
            <!-- schedule items inserted by JS or PHP if you pre-fetch them -->
            <?php
            // If you decided to pre-fetch schedules in PHP:
            /*
            if (!empty($schedules)) {
                foreach ($schedules as $schedule) {
                    echo '<div class="schedule-item d-flex align-items-center mb-2" data-schedule-id="some-uuid">'; // You'd need a schedule_id from DB
                    echo '<div class="schedule-icon me-2">';
                    echo '<i class="bi bi-' . ($schedule['workout_type'] == 'weights' ? 'fire' : ($schedule['workout_type'] == 'yoga' ? 'flower' : 'activity')) . '"></i>';
                    echo '</div>';
                    echo '<div class="schedule-details">';
                    echo '<div class="schedule-title">' . htmlspecialchars($schedule['title']) . '</div>';
                    echo '<div class="schedule-time">' . htmlspecialchars(date('M d, Y', strtotime($schedule['schedule_date']))) . ' at ' . htmlspecialchars(date('h:i A', strtotime($schedule['schedule_time']))) . '</div>';
                    echo '</div>';
                    echo '<button class="btn btn-link text-white ms-auto p-0 edit-schedule-btn" aria-label="Edit schedule" data-bs-toggle="modal" data-bs-target="#addScheduleModal">';
                    echo '<i class="bi bi-pencil"></i>';
                    echo '</button>';
                    echo '</div>';
                }
            } else {
                echo '<div class="text-white-50 text-center mt-3">No schedules planned for this day.</div>';
            }
            */
            ?>
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
          <input id="modalScheduleTitle" class="form-control mb-3" placeholder="e.g. Yoga at 7:00 AM">
          <label class="form-label">Workout Type</label>
          <select id="modalWorkoutType" class="form-select mb-3">
            <option value="default">Select Type</option>
            <option value="weights">Strength Training</option>
            <option value="yoga">Yoga/Flexibility</option>
            <option value="cardio">Cardio</option>
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

  <!-- Programs / Schedules Modal (View All) -->
  <div class="modal fade" id="schedulesModal" tabindex="-1" aria-labelledby="schedulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-dark text-light">
        <div class="modal-header">
          <h5 class="modal-title" id="schedulesModalLabel">All Items</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modalSchedulesList"></div>
      </div>
    </div>
  </div>

  <!-- Detailed Progress Modal -->
  <div class="modal fade" id="detailedProgressModal" tabindex="-1" aria-labelledby="detailedProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-dark text-light">
        <div class="modal-header">
          <h5 class="modal-title" id="detailedProgressModalLabel">Detailed Workout Progress</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <canvas id="detailedProgressChart"></canvas>
        </div>
        <div class="modal-footer justify-content-center border-top">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

</div>
</div>

  <!-- Bootstrap + Chart.js + script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="dashboard.js"></script>
  <script src="Sidebar.js"></script>
</body>
</html>
