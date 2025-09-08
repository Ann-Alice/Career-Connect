<?php
require_once("../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/login.php");
}

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_candidates,
    COUNT(CASE WHEN INTERVIEW_STATUS = 'Completed' THEN 1 END) as completed_interviews,
    COUNT(CASE WHEN ADMIN_GRADE IS NOT NULL THEN 1 END) as graded_interviews
FROM tbljobregistration 
WHERE INTERVIEW_STATUS IS NOT NULL";

$mydb->setQuery($stats_sql);
$stats_result = $mydb->loadResultList();
$stats = $stats_result ? (array)$stats_result[0] : ['total_candidates' => 0, 'completed_interviews' => 0, 'graded_interviews' => 0];

// Get recent interviews
$recent_sql = "SELECT r.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME
                FROM tbljobregistration r 
                JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                JOIN tbljob j ON r.JOBID = j.JOBID 
                JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                WHERE r.INTERVIEW_STATUS = 'Completed' 
                ORDER BY r.INTERVIEW_COMPLETED_AT DESC 
                LIMIT 5";

$mydb->setQuery($recent_sql);
$recent_result = $mydb->loadResultList();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ERIS</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .main-content {
            padding: 20px;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stats-label {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .bg-primary { background: linear-gradient(135deg, #667eea, #764ba2) !important; }
        .bg-success { background: linear-gradient(135deg, #11998e, #38ef7d) !important; }
        .bg-warning { background: linear-gradient(135deg, #f093fb, #f5576c) !important; }
        .bg-info { background: linear-gradient(135deg, #4facfe, #00f2fe) !important; }
        .text-white { color: white !important; }
        .recent-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .user-info {
            color: white;
            margin-right: 15px;
        }
        .logout-btn {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid white;
            border-radius: 20px;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: white;
            color: #667eea;
            text-decoration: none;
        }
        .nav-menu {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-btn {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            color: #495057;
            padding: 12px 20px;
            margin: 5px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: #667eea;
            border-color: #667eea;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <i class="fa fa-briefcase"></i> ERIS Admin Panel
                </a>
            </div>
            <div class="navbar-right">
                <span class="user-info">
                    <i class="fa fa-user"></i> 
                    Welcome, <?php echo $_SESSION['ADMIN_FULLNAME'] ?? 'Admin'; ?>
                </span>
                <a href="logout.php" class="logout-btn">
                    <i class="fa fa-sign-out"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-12 main-content">
                <h2 style="margin-bottom: 30px; color: #333;">
                    <i class="fa fa-dashboard"></i> Dashboard Overview
                </h2>

                <!-- Navigation Menu -->
                <div class="nav-menu">
                    <h4 style="margin-bottom: 20px; color: #495057;">
                        <i class="fa fa-bars"></i> Quick Navigation
                    </h4>
                    <a href="applicants/" class="nav-btn">
                        <i class="fa fa-users"></i> Applicants
                    </a>
                    <a href="vacancy/" class="nav-btn">
                        <i class="fa fa-briefcase"></i> Jobs
                    </a>
                    <a href="company/" class="nav-btn">
                        <i class="fa fa-building"></i> Companies
                    </a>
                    <a href="employee/" class="nav-btn">
                        <i class="fa fa-user-md"></i> Employees
                    </a>
                    <a href="user/" class="nav-btn">
                        <i class="fa fa-user-secret"></i> Users
                    </a>
                    <a href="category/" class="nav-btn">
                        <i class="fa fa-tags"></i> Categories
                    </a>
                    <a href="reports.php" class="nav-btn">
                        <i class="fa fa-chart-bar"></i> Reports
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon text-primary">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="stats-number text-primary">
                                <?php echo $stats['total_candidates'] ?? 0; ?>
                            </div>
                            <div class="stats-label">Total Candidates</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon text-success">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <div class="stats-number text-success">
                                <?php echo $stats['completed_interviews'] ?? 0; ?>
                            </div>
                            <div class="stats-label">Completed Interviews</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon text-warning">
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="stats-number text-warning">
                                <?php echo $stats['graded_interviews'] ?? 0; ?>
                            </div>
                            <div class="stats-label">Graded Interviews</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon text-info">
                                <i class="fa fa-chart-line"></i>
                            </div>
                            <div class="stats-number text-info">
                                <?php echo date('M Y'); ?>
                            </div>
                            <div class="stats-label">Current Month</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Interviews -->
                <div class="recent-table">
                    <h4 style="margin-bottom: 20px; color: #333;">
                        <i class="fa fa-clock-o"></i> Recent Interviews
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Candidate</th>
                                    <th>Job Title</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_result && count($recent_result) > 0): ?>
                                    <?php foreach ($recent_result as $row): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row->FNAME . ' ' . $row->LNAME); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($row->OCCUPATIONTITLE); ?></td>
                                            <td><?php echo htmlspecialchars($row->COMPANYNAME); ?></td>
                                            <td>
                                                <span class="label label-success">Completed</span>
                                            </td>
                                            <td>
                                                <a href="interview-results.php?id=<?php echo $row->REGISTRATIONID; ?>" 
                                                   class="btn btn-xs btn-primary">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No completed interviews yet
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html> 