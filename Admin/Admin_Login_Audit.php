<?php
// Start the session
session_start();

// Include the database configuration file
require_once '../config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Pagination settings
$records_per_page = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filter settings
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$filter_email = isset($_GET['email']) ? trim($_GET['email']) : '';
$filter_days = isset($_GET['days']) ? (int)$_GET['days'] : 7;

// Build query
$where_clauses = [];
$params = [];
$param_types = '';

if ($filter_status !== 'all') {
    $where_clauses[] = "attempt_status = ?";
    $params[] = $filter_status;
    $param_types .= 's';
}

if (!empty($filter_email)) {
    $where_clauses[] = "email LIKE ?";
    $params[] = "%{$filter_email}%";
    $param_types .= 's';
}

$where_clauses[] = "attempted_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
$params[] = $filter_days;
$param_types .= 'i';

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total records
$count_sql = "SELECT COUNT(*) as total FROM login_audit_log $where_sql";
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$total_pages = ceil($total_records / $records_per_page);

// Get records
$sql = "SELECT log_id, user_id, email, ip_address, attempt_status, 
               failure_reason, attempted_at 
        FROM login_audit_log 
        $where_sql 
        ORDER BY attempted_at DESC 
        LIMIT ? OFFSET ?";

$params[] = $records_per_page;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$audit_logs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_attempts,
    SUM(CASE WHEN attempt_status = 'success' THEN 1 ELSE 0 END) as successful,
    SUM(CASE WHEN attempt_status = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN attempt_status = 'locked' THEN 1 ELSE 0 END) as locked
    FROM login_audit_log 
    WHERE attempted_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";

$stmt = $conn->prepare($stats_sql);
$stmt->bind_param('i', $filter_days);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Audit Log - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/t3-logo.png">
    <link rel="stylesheet" href="UserPage.css">
    <link rel="stylesheet" href="SideBar.css">
    <style>
        .stats-card {
            background: #313131;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            color: #F6F9F7;
        }
        .stats-card h5 {
            color: #9E0A0A;
            margin-bottom: 15px;
        }
        .stat-item {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }
        .stat-label {
            font-size: 14px;
            color: #999;
        }
        .badge-success { background-color: #28a745; }
        .badge-failed { background-color: #dc3545; }
        .badge-locked { background-color: #ffc107; color: #000; }
        .filter-section {
            background: #313131;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="body">
    <!-- Toggle button -->
    <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Admin_sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="main-container">
            <div class="container-fluid h-100">
                
                <!-- Header -->
                <div class="row mb-3">
                    <div class="col">
                        <h1>LOGIN AUDIT LOG</h1>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="stats-card">
                    <h5>Last <?php echo $filter_days; ?> Days Statistics</h5>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo number_format($stats['total_attempts']); ?></span>
                        <span class="stat-label">Total Attempts</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: #28a745;"><?php echo number_format($stats['successful']); ?></span>
                        <span class="stat-label">Successful</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: #dc3545;"><?php echo number_format($stats['failed']); ?></span>
                        <span class="stat-label">Failed</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: #ffc107;"><?php echo number_format($stats['locked']); ?></span>
                        <span class="stat-label">Locked</span>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-section">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>All</option>
                                <option value="success" <?php echo $filter_status == 'success' ? 'selected' : ''; ?>>Success</option>
                                <option value="failed" <?php echo $filter_status == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                <option value="locked" <?php echo $filter_status == 'locked' ? 'selected' : ''; ?>>Locked</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" placeholder="Search by email" value="<?php echo htmlspecialchars($filter_email); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Time Period</label>
                            <select name="days" class="form-select">
                                <option value="1" <?php echo $filter_days == 1 ? 'selected' : ''; ?>>Last 24 Hours</option>
                                <option value="7" <?php echo $filter_days == 7 ? 'selected' : ''; ?>>Last 7 Days</option>
                                <option value="30" <?php echo $filter_days == 30 ? 'selected' : ''; ?>>Last 30 Days</option>
                                <option value="90" <?php echo $filter_days == 90 ? 'selected' : ''; ?>>Last 90 Days</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="Admin_Login_Audit.php" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table id="memberTable">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Email</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($audit_logs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No records found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($audit_logs as $log): ?>
                                    <tr>
                                        <td><?php echo date('m/d/Y - h:i A', strtotime($log['attempted_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($log['email']); ?></td>
                                        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                        <td>
                                            <?php
                                            $badge_class = 'badge-' . $log['attempt_status'];
                                            echo '<span class="badge ' . $badge_class . '">' . 
                                                 strtoupper($log['attempt_status']) . '</span>';
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['failure_reason'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&status=<?php echo $filter_status; ?>&email=<?php echo urlencode($filter_email); ?>&days=<?php echo $filter_days; ?>">Previous</a>
                            </li>
                            
                            <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $filter_status; ?>&email=<?php echo urlencode($filter_email); ?>&days=<?php echo $filter_days; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&status=<?php echo $filter_status; ?>&email=<?php echo urlencode($filter_email); ?>&days=<?php echo $filter_days; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Sidebar.js"></script>
</body>
</html>