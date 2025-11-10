<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Attendance Tracker</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="dashboard.css"> <!-- Reusing dashboard styles for consistency -->
  <link rel="stylesheet" href="Sidebar.css">
  <link rel="stylesheet" href="TrackerAttendance.css"> <!-- New attendance specific styles -->
</head>

<body class="body">

  <!-- Toggle button -->
     <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Client_sidebar.php'; ?>

  <!------------------------------ Main Container for Attendance Tracker -------------------------->

  <div class="content-wrapper">
    <div class="main-container">
      <div class="container-fluid px-4 pt-4 attendance-container">
        <h2 class="page-title">Attendance Tracker</h2>

        <div class="row gx-4 gy-4 mt-3">

          <!-- Today's Attendance Section -->
          <div class="col-12 col-lg-6">
            <div class="attendance-card">
              <div class="card-header">
                <h3>Today's Attendance (<span id="todayDate"></span>)</h3>
              </div>
              <div class="card-body text-center">
                <div id="clockStatus" class="attendance-status">You are not clocked in.</div>
                <div id="clockInOutTime" class="clock-time"></div>
                <div class="qr-code-section mt-4">
                  <p class="qr-instruction" id="qrInstruction">Scan this QR code at the gym to Clock In:</p>
                  <div class="qr-code-placeholder" id="qrCodePlaceholder">
                    <!-- Sample QR Code image -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=USER_QR_CODE_DATA_12345" alt="QR Code" id="userQRCode" style="display:none;">
                    <button class="btn btn-danger mt-3" id="clockInBtn">Clock In</button>
                  </div>
                  <button class="btn btn-outline-danger mt-3" id="clockOutBtn" style="display:none;">Clock Out</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Attendance Summary Section -->
          <div class="col-12 col-lg-6">
            <div class="attendance-card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Attendance Summary</h3>
                <div class="btn-group" role="group" aria-label="Attendance filter">
                  <input type="radio" class="btn-check" name="attendanceFilter" id="filterWeekly" autocomplete="off" checked>
                  <label class="btn btn-outline-danger btn-sm" for="filterWeekly">Weekly</label>

                  <input type="radio" class="btn-check" name="attendanceFilter" id="filterMonthly" autocomplete="off">
                  <label class="btn btn-outline-danger btn-sm" for="filterMonthly">Monthly</label>

                  <input type="radio" class="btn-check" name="attendanceFilter" id="filterYearly" autocomplete="off">
                  <label class="btn btn-outline-danger btn-sm" for="filterYearly">Yearly</label>
                </div>
              </div>
              <div class="card-body table-responsive">
                <table class="table table-dark table-hover attendance-table">
                  <thead>
                    <tr>
                      <th scope="col">Date</th>
                      <th scope="col">Clock In</th>
                      <th scope="col">Clock Out</th>
                      <th scope="col">Hours</th>
                      <th scope="col">Status</th>
                    </tr>
                  </thead>
                  <tbody id="attendanceSummaryBody">
                    <!-- Attendance records will be inserted here by JS -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap + Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="Sidebar.js"></script>
  <script src="TrackerAttendance.js"></script>
</body>
</html>                            
