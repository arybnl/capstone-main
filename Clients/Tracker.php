<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Workout Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="img/t3-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="SideBar.css">
    <link rel="stylesheet" href="Tracker.css">
</head>
<body class="body">

    <!-- Toggle button for mobile sidebar -->
    <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Client_sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="main-container">
            <div class="container-fluid px-4 pt-4 tracker-container">
                <h1 class="tracker-title">Workout Tracker</h1>

                <div class="row gx-4 gy-4">
                    <!-- Calendar Section -->
                    <div class="col-12 col-lg-5">
                        <div class="tracker-section workout-calendar">
                            <div class="calendar-header">
                                <button class="btn btn-sm btn-outline-secondary" id="prevMonthBtn"><i class="bi bi-chevron-left"></i></button>
                                <h4 class="mb-0" id="currentMonthYear"></h4>
                                <button class="btn btn-sm btn-outline-secondary" id="nextMonthBtn"><i class="bi bi-chevron-right"></i></button>
                            </div>
                            <div class="calendar-weekdays">
                                <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                            </div>
                            <div class="calendar-grid" id="workoutCalendarGrid">
                                <!-- Calendar days will be rendered here by JavaScript -->
                            </div>
                        </div>
                        <!-- Workout for the Day Section -->
                        <div class="workout-for-day-card mt-4">
                            <h2 class="section-header">Workout for <span id="selectedDayHeader">Today</span></h2>
                            <div id="workoutForDayList">
                                <!-- Workouts for the selected day will be loaded here by JavaScript -->
                                <div class="no-workouts-message" id="noWorkoutsForDayMessage">
                                    No workouts scheduled for this day.
                                </div>
                            </div>
                            <div class="d-grid mt-3">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addScheduleModal" id="addWorkoutForDayBtn">Add Workout</button>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduled & Completed Workouts Table Section -->
                    <div class="col-12 col-lg-7">
                        <div class="tracker-section">
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
                                <h2 class="section-header mb-2 mb-md-0">All Scheduled Workouts</h2>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addScheduleModal" id="addNewScheduleBtn">+ Add New Schedule</button>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-dark table-striped table-hover tracker-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;"><i class="bi bi-check-circle"></i></th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Workout</th>
                                            <th>Type</th>
                                            <th class="text-center" style="width: 80px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="allWorkoutsList">
                                        <!-- All schedule items (upcoming and completed) will be loaded here by JavaScript -->
                                    </tbody>
                                </table>
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
                    <div class="mb-3">
                        <label for="modalScheduleTitle" class="form-label">Title</label>
                        <input id="modalScheduleTitle" class="form-control bg-secondary text-light border-dark" placeholder="e.g. Yoga at 7:00 AM">
                    </div>
                    <div class="mb-3">
                        <label for="modalWorkoutType" class="form-label">Workout Type</label>
                        <select id="modalWorkoutType" class="form-select bg-secondary text-light border-dark">
                            <option value="default">Select Type</option>
                            <option value="weights">Strength Training</option>
                            <option value="yoga">Yoga/Flexibility</option>
                            <option value="cardio">Cardio</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modalScheduleDate" class="form-label">Date</label>
                        <input id="modalScheduleDate" type="date" class="form-control bg-secondary text-light border-dark">
                    </div>
                    <div class="mb-3">
                        <label for="modalScheduleTime" class="form-label">Time</label>
                        <input id="modalScheduleTime" type="time" class="form-control bg-secondary text-light border-dark">
                    </div>
                    <!-- New Recurrence Options -->
                    <div class="mb-3">
                        <label for="modalRecurrenceType" class="form-label">Recurrence</label>
                        <select id="modalRecurrenceType" class="form-select bg-secondary text-light border-dark">
                            <option value="none">None</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="mb-3" id="recurrenceEndDateGroup" style="display:none;">
                        <label for="modalRecurrenceEndDate" class="form-label">Recurrence End Date</label>
                        <input id="modalRecurrenceEndDate" type="date" class="form-control bg-secondary text-light border-dark">
                    </div>
                    <input type="hidden" id="modalScheduleId">
                    <input type="hidden" id="modalOriginalRecurringId"> <!-- To link instances of a recurring schedule -->
                </div>
                <div class="modal-footer justify-content-center border-top">
                    <button type="button" class="btn btn-danger" id="saveScheduleBtn">Save</button>
                    <button type="button" class="btn btn-outline-secondary" id="deleteScheduleBtn" style="display:none;">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Recurring Schedule Options Modal -->
    <div class="modal fade" id="editRecurringModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">Edit Recurring Schedule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>This is a recurring workout. How would you like to edit it?</p>
                </div>
                <div class="modal-footer justify-content-center border-top">
                    <button type="button" class="btn btn-primary" id="editThisInstanceBtn">Edit This Instance Only</button>
                    <button type="button" class="btn btn-info" id="editFutureInstancesBtn">Edit This and Future Instances</button>
                    <button type="button" class="btn btn-danger" id="deleteAllFutureInstancesBtn">Delete This and Future Instances</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap + script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Sidebar.js"></script>
    <script src="Tracker.js"></script>
</body>
</html>
