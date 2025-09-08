<?php
// Include initialization if not already included
if (!isset($mydb)) {
    require_once("../include/initialize.php");
    
    // Check if user is logged in as admin
    if(!isset($_SESSION['ADMIN_USERID'])){
        redirect(web_root."admin/login.php");
    }
}

// Dashboard code will use the existing $mydb Database class instance from initialize.php

// Get real data from database using correct columns
$stats_sql = "SELECT 
    COUNT(*) as total_candidates,
    COUNT(CASE WHEN REMARKS = 'Pending' THEN 1 END) as pending_applications,
    COUNT(CASE WHEN REMARKS != 'Pending' THEN 1 END) as reviewed_applications
FROM tbljobregistration";

$mydb->setQuery($stats_sql);
$stats = $mydb->loadSingleResult();
if (!$stats) {
    $stats = (object) ['total_candidates' => 0, 'pending_applications' => 0, 'reviewed_applications' => 0];
}

// Get recent applications with real data using correct columns
$recent_sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE, c.COMPANYNAME
               FROM tbljobregistration r 
               JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
               JOIN tbljob j ON r.JOBID = j.JOBID 
               JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
               ORDER BY r.REGISTRATIONDATE DESC 
               LIMIT 10";

$mydb->setQuery($recent_sql);
$recent_applications = $mydb->loadResultList();
if (!$recent_applications) {
    $recent_applications = [];
}

// Get total counts
$mydb->setQuery("SELECT COUNT(*) as count FROM tblapplicants");
$total_applicants_result = $mydb->loadSingleResult();
$total_applicants = $total_applicants_result ? $total_applicants_result->count : 0;

$mydb->setQuery("SELECT COUNT(*) as count FROM tbljob");
$total_jobs_result = $mydb->loadSingleResult();
$total_jobs = $total_jobs_result ? $total_jobs_result->count : 0;

$mydb->setQuery("SELECT COUNT(*) as count FROM tblcompany");
$total_companies_result = $mydb->loadSingleResult();
$total_companies = $total_companies_result ? $total_companies_result->count : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Connect - Admin Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .main-header {
            background: #3498db;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        .logo:hover {
            color: white;
            text-decoration: none;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            color: white;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #3498db;
        }
        
        .breadcrumb {
            background: #ecf0f1;
            padding: 10px 0;
            margin: 0;
            border-radius: 0;
        }
        
        .sidebar {
            background: #2c3e50;
            min-height: calc(100vh - 120px);
            padding: 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover {
            background: #34495e;
            color: white;
            text-decoration: none;
            padding-left: 25px;
        }
        
        .sidebar-menu a.active {
            background: #3498db;
            color: white;
            border-left: 4px solid #2980b9;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-content {
            padding: 25px;
            background: #f8f9fa;
        }
        
        .content-header {
            margin-bottom: 20px;
        }
        
        .content-title {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 25px 0;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            border-left: 4px solid #3498db;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .stats-card:nth-child(2) { border-left-color: #27ae60; }
        .stats-card:nth-child(3) { border-left-color: #e74c3c; }
        .stats-card:nth-child(4) { border-left-color: #f39c12; }
        
        .stats-number {
            font-size: 2.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .stats-card:nth-child(1) .stats-number { color: #3498db; }
        .stats-card:nth-child(2) .stats-number { color: #27ae60; }
        .stats-card:nth-child(3) .stats-number { color: #e74c3c; }
        .stats-card:nth-child(4) .stats-number { color: #f39c12; }
        
        .stats-label {
            color: #6c757d;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 20px;
        }
        
        .table-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .table-responsive {
            margin: 0;
        }
        
        .table {
            margin: 0;
        }
        
        .table th {
            background: #f8f9fa;
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
        }
        
        .table td {
            border: none;
            padding: 15px;
            vertical-align: middle;
            font-weight: 400;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-reviewed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            margin-right: 5px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-view {
            background: #3498db;
            color: white;
        }
        
        .btn-approve {
            background: #27ae60;
            color: white;
        }
        
        .btn-action:hover {
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-view:hover {
            background: #2980b9;
            color: white;
        }
        
        .btn-approve:hover {
            background: #229954;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container-fluid">
            <div class="header-content">
                <div class="left-section">
                    <a href="#" class="logo">Career Connect</a>
                    <button class="btn btn-link" style="color: white; margin-left: 15px;">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo isset($_SESSION['ADMIN_PICLOCATION']) && $_SESSION['ADMIN_PICLOCATION'] != 'avatar.jpg' ? '👤' : '🐕'; ?>
                    </div>
                    <span><?php echo $_SESSION['ADMIN_FULLNAME'] ?? 'Admin'; ?></span>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li class="active">Application Management</li>
            </ol>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2">
                <div class="sidebar">
                    <ul class="sidebar-menu">
                        <li><a href="index.php" class="active"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <li><a href="company/"><i class="fa fa-building"></i> Company</a></li>
                        <li><a href="vacancy/"><i class="fa fa-briefcase"></i> Vacancy</a></li>
                        <li><a href="applicants/"><i class="fa fa-users"></i> Applicants</a></li>
                        <li><a href="interview-invitation.php"><i class="fa fa-video-camera"></i> AI Interviews</a></li>
                        <li><a href="interview-results.php"><i class="fa fa-bar-chart"></i> Interview Results</a></li>
                        <li><a href="employee/"><i class="fa fa-user"></i> Employees</a></li>
                        <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10">
                <div class="main-content">
                    <div class="content-header">
                        <h1 class="content-title">Application Management</h1>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row" style="margin-bottom: 30px;">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $total_applicants; ?></div>
                                <div class="stats-label">Total Applicants</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $total_jobs; ?></div>
                                <div class="stats-label">Total Jobs</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $total_companies; ?></div>
                                <div class="stats-label">Total Companies</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $stats->total_candidates ?? 0; ?></div>
                                <div class="stats-label">Total Applications</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-header">
                            <h3 class="table-title">Application Management</h3>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Position</th>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Application Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_applications): ?>
                                        <?php foreach ($recent_applications as $app): ?>
                                            <tr>
                                                <td><?php echo $app->FNAME . ' ' . $app->LNAME; ?></td>
                                                <td><?php echo $app->OCCUPATIONTITLE; ?></td>
                                                <td><?php echo $app->COMPANYNAME; ?></td>
                                                <td><?php echo $app->EMAILADDRESS; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($app->REGISTRATIONDATE)); ?></td>
                                                <td>
                                                    <?php 
                                                    $status_class = 'status-pending';
                                                    $status_text = 'Pending';
                                                    if ($app->REMARKS && $app->REMARKS != 'Pending') {
                                                        $status_class = 'status-reviewed';
                                                        $status_text = $app->REMARKS;
                                                    }
                                                    ?>
                                                    <span class="status-badge <?php echo $status_class; ?>">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn-action btn-view" onclick="viewApplicant(<?php echo $app->APPLICANTID; ?>)" data-toggle="modal" data-target="#applicantModal">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                    
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <p>No applications found. <a href="applicants/add.php">Add first applicant</a></p>
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
    </div>

    <!-- Applicant Details Modal -->
    <div class="modal fade" id="applicantModal" tabindex="-1" role="dialog" aria-labelledby="applicantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px 8px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="applicantModalLabel" style="margin: 0; font-weight: 600;">
                        <i class="fa fa-user-circle" style="margin-right: 10px;"></i><b> Applicant Details </b>
                       
                    </h4>
                </div>
                <div class="modal-body" id="applicantDetails" style="padding: 30px; background: #f8fafc;">
                    <div class="text-center" style="padding: 40px;">
                        <i class="fa fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i>
                        <p style="margin-top: 15px; color: #6c757d;">Loading applicant details...</p>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e9ecef; padding: 20px 30px;">
                    <button type="button" class="btn btn-info" id="viewResumeBtn" style="padding: 10px 20px; border-radius: 6px; margin-right: 10px;">
                        <i class="fa fa-eye"></i> View Resume
                    </button>
                    <button type="button" class="btn btn-success" id="downloadResumeBtn" style="padding: 10px 20px; border-radius: 6px; margin-right: 10px;">
                        <i class="fa fa-download"></i> Download Resume
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" style="padding: 10px 20px; border-radius: 6px;">
                        <i class="fa fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <script>
    function viewApplicant(applicantId) {
        // Reset modal content
        $('#applicantDetails').html(`
            <div class="text-center" style="padding: 40px;">
                <i class="fa fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i>
                <p style="margin-top: 15px; color: #6c757d;">Loading applicant details...</p>
            </div>
        `);
        
        // Update edit button with applicant ID
        $('#editApplicantBtn').off('click').on('click', function() {
            window.location.href = 'applicants/edit.php?id=' + applicantId;
        });
        
        // Update resume buttons with applicant ID
        $('#viewResumeBtn').off('click').on('click', function() {
            viewResume(applicantId);
        });
        
        $('#downloadResumeBtn').off('click').on('click', function() {
            downloadResumeManual(applicantId);
        });
        
        // Fetch applicant details via AJAX
        $.ajax({
            url: 'applicants/view.php',
            type: 'GET',
            data: { id: applicantId, ajax: 1 },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayApplicantDetails(response.data);
                } else {
                    showError(response.message || 'Failed to load applicant details');
                }
            },
            error: function() {
                showError('Error connecting to server. Please try again.');
            }
        });
    }
    
    function displayApplicantDetails(applicant) {
        const detailsHtml = `
            <div class="row">
                <div class="col-md-4 text-center" style="margin-bottom: 20px;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 30px; color: white; box-shadow: 0 8px 25px rgba(102,126,234,0.3);">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                            <i class="fa fa-user" style="font-size: 2rem; color: white;"></i>
                        </div>
                        <h4 style="margin: 0; font-weight: 600;">${applicant.FNAME} ${applicant.LNAME}</h4>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">${applicant.EMAILADDRESS}</p>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-user"></i><b> Full Name </b></label>
                                <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.FNAME} ${applicant.MNAME || ''} ${applicant.LNAME}</p>
                            </div>
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-envelope"></i><b> Email Address</b></label>
                                <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.EMAILADDRESS}</p>
                            </div>
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-phone"></i><b> Contact Number</b></label>
                                <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.CONTACTNO || 'Not provided'}</p>
                            </div>
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-venus-mars"></i><b> Gender</b></label>
                                <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.SEX || 'Not provided'}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-calendar"></i><b> Date of Birth</b></label>
                                <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.BIRTHDATE ? new Date(applicant.BIRTHDATE).toLocaleDateString() : 'Not provided'}</p>
                            </div>
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-map-pin"></i><b> Place of Birth</b></label>
                                <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.BIRTHPLACE || 'Not provided'}</p>
                            </div>
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-heart"></i><b> Civil Status</b></label>
                                <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.CIVILSTATUS || 'Not provided'}</p>
                            </div>
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-clock-o"></i><b> Age</b></label>
                                <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.AGE || 'Not provided'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr style="margin: 25px 0; border-color: #e9ecef;">
            
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #495057; margin-bottom: 15px; font-weight: 600;">
                        <i class="fa fa-map-marker" style="margin-right: 8px; color: #667eea;"></i>
                        Address Information
                    </h5>
                    <div class="detail-item" style="margin-bottom: 15px;">
                        <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-home"></i><b> Current Address</b></label>
                        <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.ADDRESS || 'Not provided'}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 style="color: #495057; margin-bottom: 15px; font-weight: 600;">
                        <i class="fa fa-graduation-cap" style="margin-right: 8px; color: #667eea;"></i>
                        Educational Background
                    </h5>
                    <div class="detail-item" style="margin-bottom: 15px;">
                        <label style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa fa-certificate"></i><b> Educational Attainment</b></label>
                        <p style="margin: 5px 0 0 0; color: #2c3e50; font-size: 14px;">${applicant.DEGREE || 'Not provided'}</p>
                    </div>
                </div>
            </div>
        `;
        
        $('#applicantDetails').html(detailsHtml);
    }
    
    function showError(message) {
        const errorHtml = `
            <div class="text-center" style="padding: 40px;">
                <i class="fa fa-exclamation-triangle" style="font-size: 3rem; color: #e74c3c; margin-bottom: 15px;"></i>
                <h4 style="color: #495057; margin-bottom: 10px;">Error Loading Details</h4>
                <p style="color: #6c757d;">${message}</p>
                <button type="button" class="btn btn-primary" onclick="$('#applicantModal').modal('hide')" style="margin-top: 15px;">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        `;
        
        $('#applicantDetails').html(errorHtml);
    }
    
    function viewResume(applicantId) {
        // Open resume in a new tab for viewing
        const resumeUrl = 'applicants/view-resume.php?id=' + applicantId;
        window.open(resumeUrl, '_blank', 'width=1000,height=800,scrollbars=yes,resizable=yes');
    }
    
    function downloadResumeManual(applicantId) {
        // Simple manual download - just navigate to the download URL
        window.location.href = 'applicants/download-resume.php?id=' + applicantId;
    }
    
    // Enhanced modal animations
    $('#applicantModal').on('show.bs.modal', function () {
        $(this).find('.modal-dialog').css({
            'transform': 'scale(0.7)',
            'opacity': '0'
        });
    });
    
    $('#applicantModal').on('shown.bs.modal', function () {
        $(this).find('.modal-dialog').animate({
            'transform': 'scale(1)',
            'opacity': '1'
        }, 300);
    });
    </script>
</body>
</html> 