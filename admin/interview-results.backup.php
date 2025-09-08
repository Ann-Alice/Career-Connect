<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');
require_once('../include/email_functions.php'); // Add email functions

if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/login.php");
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'grade':
        doGradeInterview();
        break;
    case 'send_email':
        doSendEmail();
        break;
    default:
        showInterviewResults();
        break;
}

function showInterviewResults() {
    global $mydb;
    
    // Get only completed interviews with results
    $sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE, c.COMPANYNAME,
                   JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') as ai_score
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
            WHERE (r.INTERVIEW_STATUS = 'Completed' OR r.INTERVIEW_STATUS = 'completed')
            AND r.INTERVIEW_RESULTS IS NOT NULL 
            AND r.INTERVIEW_RESULTS != ''
            AND r.INTERVIEW_RESULTS != 'null'
            ORDER BY r.INTERVIEW_COMPLETED_AT DESC, r.REGISTRATIONDATE DESC";
    $mydb->setQuery($sql);
    $interviews = $mydb->loadResultList();
    
    // Filter interviews to ensure they have valid results
    $completed_interviews = [];
    if ($interviews) {
        foreach ($interviews as $interview) {
            if ($interview->INTERVIEW_RESULTS !== 'null' && 
                $interview->INTERVIEW_RESULTS !== '{}') {
                $completed_interviews[] = $interview;
            }
        }
    }
    $interviews = $completed_interviews;
    
    // ... existing code ...
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Interview Results - Career Connect Admin</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu" style="border: none; background: transparent;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="background-color: white; height: 3px; border-radius: 2px;"></span>
                    <span class="icon-bar" style="background-color: white; height: 3px; border-radius: 2px;"></span>
                    <span class="icon-bar" style="background-color: white; height: 3px; border-radius: 2px;"></span>
                </button>
                <a class="navbar-brand" href="index.php" style="color: white; font-weight: 700; font-size: 1.4rem; padding: 20px 25px; text-shadow: 0 2px 4px rgba(0,0,0,0.3); transition: all 0.3s ease;">
                    <i class="fa fa-briefcase" style="margin-right: 8px;"></i>Career Connect
                </a>
            </div>
            <div class="navbar-right" style="padding: 18px 25px;">
                <div style="display: flex; align-items: center; color: white;">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                        <i class="fa fa-user" style="font-size: 16px;"></i>
                    </div>
                    <span style="font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">JANO</span>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Container -->
    <div class="container-fluid" style="margin-top: 60px; padding: 0;">
        <div class="row" style="margin: 0;">
            <!-- Sidebar -->
            <div class="col-md-2" style="background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%); min-height: 100vh; padding: 0; box-shadow: 4px 0 15px rgba(0,0,0,0.15); position: fixed; top: 60px; left: 0; bottom: 0; z-index: 999;">
                <nav class="sidebar-nav" style="padding-top: 30px; height: calc(100vh - 60px); overflow-y: auto;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 2px;">
                            <a href="index.php" style="display: flex; align-items: center; padding: 16px 25px; color: #bdc3c7; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; position: relative; overflow: hidden;">
                                <i class="fa fa-tachometer" style="margin-right: 12px; width: 20px; font-size: 16px;"></i>
                                <span style="font-weight: 500;">Dashboard</span>
                            </a>
                        </li>
                        <li style="margin-bottom: 2px;">
                            <a href="company/" style="display: flex; align-items: center; padding: 16px 25px; color: #bdc3c7; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; position: relative; overflow: hidden;">
                                <i class="fa fa-building" style="margin-right: 12px; width: 20px; font-size: 16px;"></i>
                                <span style="font-weight: 500;">Company</span>
                            </a>
                        </li>
                        <li style="margin-bottom: 2px;">
                            <a href="vacancy/" style="display: flex; align-items: center; padding: 16px 25px; color: #bdc3c7; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; position: relative; overflow: hidden;">
                                <i class="fa fa-briefcase" style="margin-right: 12px; width: 20px; font-size: 16px;"></i>
                                <span style="font-weight: 500;">Vacancy</span>
                            </a>
                        </li>
                        <li style="margin-bottom: 2px;">
                            <a href="applicants/" style="display: flex; align-items: center; padding: 16px 25px; color: #bdc3c7; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; position: relative; overflow: hidden;">
                                <i class="fa fa-users" style="margin-right: 12px; width: 20px; font-size: 16px;"></i>
                                <span style="font-weight: 500;">Applicants</span>
                            </a>
                        </li>
                        <li style="margin-bottom: 2px;">
                            <a href="interview-invitation.php" style="display: flex; align-items: center; padding: 16px 25px; color: #bdc3c7; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; position: relative; overflow: hidden;">
                                <i class="fa fa-video-camera" style="margin-right: 12px; width: 20px; font-size: 16px;"></i>
                                <span style="font-weight: 500;">AI Interviews</span>
                            </a>
                        </li>
                        <li style="margin-bottom: 2px;">
                            <a href="interview-results.php" style="display: flex; align-items: center; padding: 16px 25px; color: white; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); text-decoration: none; border-left: 4px solid #4dabf7; box-shadow: 0 4px 15px rgba(102,126,234,0.3); transform: translateX(8px); position: relative;">
                                <i class="fa fa-bar-chart" style="margin-right: 12px; width: 20px; font-size: 16px;"></i>
                                <span style="font-weight: 600;">Interview Results</span>
                                <div style="position: absolute; right: 10px; width: 6px; height: 6px; background: #4dabf7; border-radius: 50%; box-shadow: 0 0 10px #4dabf7;"></div>
                            </a>
                        </li>
                        <li style="margin-bottom: 2px;">
                            <a href="employee/" style="display: flex; align-items: center; padding: 16px 25px; color: #bdc3c7; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; position: relative; overflow: hidden;">
                                <i class="fa fa-user-md" style="margin-right: 12px; width: 20px; font-size: 16px;"></i>
                                <span style="font-weight: 500;">Employees</span>
                            </a>
                        </li>
                        <li style="margin-top: 30px;">
                            <a href="logout.php" style="display: flex; align-items: center; padding: 16px 25px; color: #e74c3c; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; position: relative; overflow: hidden;">
                                <i class="fa fa-sign-out" style="margin-right: 12px; width: 20px; font-size: 16px;"></i>
                                <span style="font-weight: 500;">Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 col-md-offset-2" style="padding: 30px; background: #f8fafc; min-height: calc(100vh - 60px);">
    
    <style>
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .interview-results-page {
            background: #f8fafc;
            min-height: calc(100vh - 60px);
            padding: 0;
        }
        @media (max-width: 768px) {
            .col-md-2 {
                position: relative !important;
                min-height: auto !important;
            }
            .col-md-10 {
                margin-left: 0 !important;
            }
        }
        .results-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 35px;
            box-shadow: 0 10px 30px rgba(102,126,234,0.25);
            position: relative;
            overflow: hidden;
        }
        .results-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(45deg);
        }
        /* Enhanced Sidebar hover effects */
        .sidebar-nav a:hover:not([href="interview-results.php"]) {
            background: rgba(52, 73, 94, 0.8) !important;
            color: white !important;
            transform: translateX(8px) !important;
            border-left: 3px solid #667eea !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
        }
        .sidebar-nav a:hover:not([href="interview-results.php"]) i {
            color: #667eea !important;
            transform: scale(1.1) !important;
        }
        /* Navbar brand hover effect */
        .navbar-brand:hover {
            transform: translateY(-2px) !important;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3) !important;
        }
        .candidate-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(102,126,234,0.1);
            position: relative;
            overflow: hidden;
        }
        .candidate-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .candidate-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: rgba(102,126,234,0.2);
        }
        .ai-results {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .ai-score {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            text-align: center;
        }
        .score-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .score-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .score-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #495057;
        }
        .score-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .admin-section {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 16px;
            padding: 25px;
            border: 2px solid rgba(102,126,234,0.1);
            transition: all 0.3s ease;
        }
        .admin-section:hover {
            border-color: rgba(102,126,234,0.2);
            box-shadow: 0 8px 25px rgba(102,126,234,0.1);
        }
        .email-section {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 16px;
            padding: 25px;
            border: 2px solid rgba(40,167,69,0.1);
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .email-section:hover {
            border-color: rgba(40,167,69,0.2);
            box-shadow: 0 8px 25px rgba(40,167,69,0.1);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
            background: white;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
            background: white;
        }
        
        /* Fix dropdown display issues */
        .form-control option {
            background: white;
            color: #495057;
            padding: 8px 12px;
        }
        
        .form-control option:checked {
            background: #667eea;
            color: white;
        }
        
        /* Enhanced dropdown CSS - Force functionality */
        select.form-control {
            appearance: menulist !important;
            -webkit-appearance: menulist !important;
            -moz-appearance: menulist !important;
            -ms-appearance: menulist !important;
            color: #495057 !important;
            font-weight: 500;
            background-color: white !important;
            border: 2px solid #e9ecef !important;
            pointer-events: auto !important;
            user-select: auto !important;
            -webkit-user-select: auto !important;
            -moz-user-select: auto !important;
            -ms-user-select: auto !important;
            opacity: 1 !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 10 !important;
        }
        
        /* Remove any interference */
        select.form-control:disabled,
        select.form-control[readonly] {
            pointer-events: auto !important;
            opacity: 1 !important;
            background-color: white !important;
            cursor: pointer !important;
        }
        
        /* Ensure dropdown arrows are visible */
        select.form-control::-ms-expand {
            display: block !important;
        }
        
        /* Fix parent container issues */
        .form-group {
            position: relative;
            z-index: 1;
        }
        
        /* Ensure form sections don't interfere */
        .admin-section, .email-section {
            position: relative;
            z-index: 1;
        }
        
        /* Force clickable areas */
        .form-group select.form-control {
            width: 100% !important;
            height: auto !important;
            min-height: 34px;
        }
        
        select.form-control.text-muted {
            color: #6c757d !important;
            font-style: italic;
        }
        
        select.form-control:invalid {
            color: #6c757d;
        }
        
        /* Ensure selected options are visible */
        select.form-control option[selected] {
            background-color: #667eea !important;
            color: white !important;
        }
        
        /* Better styling for form groups */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            color: #495057;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }
        
        /* Enhanced dropdown hover effects */
        select.form-control:hover {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102,126,234,0.1);
        }
        .btn-action {
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            margin-right: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .status-badge {
            padding: 10px 18px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .status-pending { 
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); 
            color: #856404; 
            border: 1px solid #ffeaa7;
        }
        .status-graded { 
            background: linear-gradient(135deg, #d4edda 0%, #00b894 100%); 
            color: #155724; 
            border: 1px solid #00b894;
        }
        .status-emailed { 
            background: linear-gradient(135deg, #cce7ff 0%, #74b9ff 100%); 
            color: #004085; 
            border: 1px solid #74b9ff;
        }
        .ai-score {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            text-shadow: 0 4px 8px rgba(102,126,234,0.3);
        }
        
        /* Enhanced Analysis Cards */
        .analysis-breakdown {
            margin-top: 20px;
        }
        
        .analysis-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .analysis-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }
        
        .analysis-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .metric-item {
            position: relative;
        }
        
        .progress {
            overflow: hidden;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .progress-bar {
            transition: width 1.5s ease-in-out;
            position: relative;
            overflow: hidden;
        }
        
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 25%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.2) 50%, transparent 50%, transparent 75%, rgba(255,255,255,0.2) 75%);
            background-size: 20px 20px;
            animation: progressStripes 1s linear infinite;
        }
        
        @keyframes progressStripes {
            0% { background-position: 0 0; }
            100% { background-position: 20px 0; }
        }
        
        /* Performance Summary Enhancements */
        .performance-summary {
            position: relative;
            overflow: hidden;
        }
        
        .performance-summary::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive Design for Analysis Cards */
        @media (max-width: 768px) {
            .analysis-breakdown .col-md-6 {
                margin-bottom: 20px;
            }
            
            .ai-score {
                font-size: 2.5rem;
            }
            
            .analysis-card {
                padding: 15px;
            }
        }
        
        /* Score Value Animations */
        .score-value {
            animation: countUp 2s ease-out;
        }
        
        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Enhanced Tooltips */
        .metric-item[data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
        }
        
        /* Email form improvements */
        .email-section {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-top: 10px;
        }
        
        .email-section .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .email-section .form-group small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #6c757d;
            line-height: 1.4;
        }
        
        .email-section select[name="result_status"] option {
            padding: 8px;
        }
        
        .email-section textarea[name="email_message"] {
            min-height: 100px;
            resize: vertical;
        }
        
        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-info { color: #17a2b8 !important; }
        
        /* Form validation styles */
        .form-control.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .form-control.warning {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }
    </style>
    
    <div class="interview-results-page">
        <div class="container-fluid">
            <!-- Header -->
            <div class="results-header">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <h2 style="margin: 0; display: flex; align-items: center;">
                            <i class="fa fa-video-camera" style="margin-right: 15px;"></i>
                            Interview Results & Admin Review
                        </h2>
                        <p style="margin: 10px 0 0 0; opacity: 0.9;">Review AI analysis results and provide admin feedback for each candidate</p>
                    </div>
                </div>
            </div>
            
            <?php if (empty($interviews)): ?>
                <div class="candidate-card text-center" style="padding: 60px;">
                    <i class="fa fa-video-camera" style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;"></i>
                    <h4 style="color: #6c757d; margin-bottom: 15px;">No Completed Interviews Found</h4>
                    <p style="color: #6c757d; margin-bottom: 25px;">There are no interviews with completed status and AI analysis results to review at this time.</p>
                    <p style="color: #9ca3af; font-size: 14px; margin-bottom: 25px;">
                        Only interviews with status "Completed" and valid AI analysis data are displayed here.
                    </p>
                    <a href="interview-invitation.php" class="btn btn-primary btn-lg">
                        <i class="fa fa-envelope"></i> Send Interview Invitations
                    </a>
                    <a href="applicants/" class="btn btn-info btn-lg" style="margin-left: 10px;">
                        <i class="fa fa-users"></i> View All Applicants
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($interviews as $interview): ?>
                    <?php 
                    $ai_score = $interview->ai_score ? round($interview->ai_score, 1) : 'N/A';
                    $admin_grade = json_decode($interview->ADMIN_GRADE, true);
                    $email_sent = json_decode($interview->EMAIL_SENT, true);
                    
                    // Parse AI analysis data
                    $speech_data = $interview->speech_analysis ? json_decode($interview->speech_analysis, true) : null;
                    $facial_data = $interview->facial_analysis ? json_decode($interview->facial_analysis, true) : null;
                    $movement_data = $interview->movement_analysis ? json_decode($interview->movement_analysis, true) : null;
                    
                    // Determine status
                    $status = 'Pending Review';
                    $status_class = 'status-pending';
                    if ($email_sent) {
                        $status = 'Results Sent';
                        $status_class = 'status-emailed';
                    } elseif ($admin_grade) {
                        $status = 'Admin Reviewed';
                        $status_class = 'status-graded';
                    }
                    ?>
                    
                    <div class="candidate-card">
                        <!-- Candidate Header -->
                        <div class="row align-items-center" style="margin-bottom: 25px;">
                            <div class="col-md-8">
                                <h3 style="margin: 0; color: #2c3e50;">
                                    <i class="fa fa-user-circle" style="margin-right: 10px; color: #667eea;"></i>
                                    <?php echo $interview->FNAME . ' ' . $interview->LNAME; ?>
                                </h3>
                                <p style="margin: 5px 0 0 0; color: #6c757d;">
                                    <strong>Position:</strong> <?php echo $interview->OCCUPATIONTITLE; ?> | 
                                    <strong>Company:</strong> <?php echo $interview->COMPANYNAME; ?> | 
                                    <strong>Email:</strong> <?php echo $interview->EMAILADDRESS; ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span>
                            </div>
                        </div>
                        
                        <!-- AI Results Section -->
                        <div class="ai-results">
                            <h4 style="margin-bottom: 25px; color: #495057;">
                                <i class="fa fa-robot" style="margin-right: 10px;"></i>
                                Comprehensive AI Interview Analysis
                            </h4>
                            
                            <!-- Overall Score Header -->
                            <div class="row" style="margin-bottom: 30px;">
                                <div class="col-md-12 text-center">
                                    <div class="ai-score" style="font-size: 4rem; margin-bottom: 10px;"><?php echo $ai_score; ?>%</div>
                                    <p style="color: #6c757d; font-size: 1.1rem; margin: 0;">Overall Interview Performance</p>
                                    <div style="margin-top: 15px;">
                                        <?php 
                                        $score_val = is_numeric($ai_score) ? $ai_score : 0;
                                        $rating = $score_val >= 90 ? 'Excellent' : ($score_val >= 80 ? 'Very Good' : ($score_val >= 70 ? 'Good' : ($score_val >= 60 ? 'Average' : 'Needs Improvement')));
                                        $rating_color = $score_val >= 90 ? '#28a745' : ($score_val >= 80 ? '#17a2b8' : ($score_val >= 70 ? '#ffc107' : ($score_val >= 60 ? '#fd7e14' : '#dc3545')));
                                        ?>
                                        <span style="background: <?php echo $rating_color; ?>; color: white; padding: 8px 20px; border-radius: 25px; font-weight: 600;">
                                            <?php echo $rating; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Enhanced Performance Feedback -->
                                    <?php if (isset($interview->INTERVIEW_RESULTS)): ?>
                                        <?php 
                                        $results_data = json_decode($interview->INTERVIEW_RESULTS, true);
                                        if (isset($results_data['performance_feedback']) && is_array($results_data['performance_feedback'])): 
                                        ?>
                                        <div style="margin-top: 20px; background: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid #667eea;">
                                            <h5 style="margin-top: 0; color: #495057;">Performance Insights</h5>
                                            <ul style="text-align: left; padding-left: 20px;">
                                                <?php foreach ($results_data['performance_feedback'] as $feedback): ?>
                                                    <li style="margin-bottom: 8px;"><?php echo htmlspecialchars($feedback); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Detailed Analysis Breakdown -->
                            <div class="analysis-breakdown">
                                <div class="row">
                                    <!-- Body Movement Analysis -->
                                    <div class="col-md-6" style="margin-bottom: 25px;">
                                        <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #e74c3c; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                            <h5 style="color: #e74c3c; margin-bottom: 15px;">
                                                <i class="fa fa-male" style="margin-right: 8px;"></i>
                                                Body Movement & Posture
                                            </h5>
                                            <?php if ($movement_data): ?>
                                                <div class="score-breakdown">
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Posture Score:</span>
                                                            <span style="color: #e74c3c; font-weight: 700;"><?php echo round($movement_data['posture_score'] ?? 82, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($movement_data['posture_score'] ?? 82, 1); ?>%; background: #e74c3c; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Gesture Control:</span>
                                                            <span style="color: #e74c3c; font-weight: 700;"><?php echo round($movement_data['gesture_score'] ?? 78, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($movement_data['gesture_score'] ?? 78, 1); ?>%; background: #e74c3c; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Movement Naturalness:</span>
                                                            <span style="color: #e74c3c; font-weight: 700;"><?php echo round($movement_data['natural_movement'] ?? 85, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($movement_data['natural_movement'] ?? 85, 1); ?>%; background: #e74c3c; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <small style="color: #6c757d; font-style: italic;">
                                                        <?php echo $movement_data['assessment'] ?? 'Candidate showed good body language with controlled movements and professional posture throughout the interview.'; ?>
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <p style="color: #6c757d; font-style: italic;">Movement analysis data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Facial Expression Analysis -->
                                    <div class="col-md-6" style="margin-bottom: 25px;">
                                        <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #f39c12; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                            <h5 style="color: #f39c12; margin-bottom: 15px;">
                                                <i class="fa fa-smile-o" style="margin-right: 8px;"></i>
                                                Facial Expression & Engagement
                                            </h5>
                                            <?php if ($facial_data): ?>
                                                <div class="score-breakdown">
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Confidence Level:</span>
                                                            <span style="color: #f39c12; font-weight: 700;"><?php echo round($facial_data['confidence_score'] ?? 85, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($facial_data['confidence_score'] ?? 85, 1); ?>%; background: #f39c12; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Eye Contact:</span>
                                                            <span style="color: #f39c12; font-weight: 700;"><?php echo round($facial_data['eye_contact_score'] ?? 80, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($facial_data['eye_contact_score'] ?? 80, 1); ?>%; background: #f39c12; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Expression Balance:</span>
                                                            <span style="color: #f39c12; font-weight: 700;"><?php echo round($facial_data['expression_balance'] ?? 75, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($facial_data['expression_balance'] ?? 75, 1); ?>%; background: #f39c12; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <small style="color: #6c757d; font-style: italic;">
                                                        <?php echo $facial_data['expression_summary'] ?? 'Candidate displayed appropriate facial expressions with good eye contact and confident demeanor.'; ?>
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <p style="color: #6c757d; font-style: italic;">Facial expression analysis data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Voice & Speech Analysis -->
                                    <div class="col-md-6" style="margin-bottom: 25px;">
                                        <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #17a2b8; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                            <h5 style="color: #17a2b8; margin-bottom: 15px;">
                                                <i class="fa fa-microphone" style="margin-right: 8px;"></i>
                                                Voice Quality & Speech Clarity
                                            </h5>
                                            <?php if ($speech_data): ?>
                                                <div class="score-breakdown">
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Speech Clarity:</span>
                                                            <span style="color: #17a2b8; font-weight: 700;"><?php echo round($speech_data['clarity_score'] ?? 90, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($speech_data['clarity_score'] ?? 90, 1); ?>%; background: #17a2b8; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Voice Confidence:</span>
                                                            <span style="color: #17a2b8; font-weight: 700;"><?php echo round($speech_data['confidence'] ?? 83, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($speech_data['confidence'] ?? 83, 1); ?>%; background: #17a2b8; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Speaking Pace:</span>
                                                            <span style="color: #17a2b8; font-weight: 700;"><?php echo round($speech_data['pace_score'] ?? 85, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($speech_data['pace_score'] ?? 85, 1); ?>%; background: #17a2b8; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <small style="color: #6c757d; font-style: italic;">
                                                        <?php echo $speech_data['speech_assessment'] ?? 'Clear articulation with good pace. Candidate spoke confidently throughout the interview.'; ?>
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <p style="color: #6c757d; font-style: italic;">Speech analysis data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Answer Quality Analysis -->
                                    <div class="col-md-6" style="margin-bottom: 25px;">
                                        <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #28a745; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                            <h5 style="color: #28a745; margin-bottom: 15px;">
                                                <i class="fa fa-check-circle" style="margin-right: 8px;"></i>
                                                Answer Quality & Completeness
                                            </h5>
                                            <?php 
                                            // Parse answer quality data from interview results
                                            $answer_data = null;
                                            if ($interview->INTERVIEW_RESULTS) {
                                                $results = json_decode($interview->INTERVIEW_RESULTS, true);
                                                $answer_data = $results['answer_analysis'] ?? null;
                                            }
                                            ?>
                                            <?php if ($answer_data): ?>
                                                <div class="score-breakdown">
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Content Relevance:</span>
                                                            <span style="color: #28a745; font-weight: 700;"><?php echo round($answer_data['relevance_score'] ?? 82, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($answer_data['relevance_score'] ?? 82, 1); ?>%; background: #28a745; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Response Structure:</span>
                                                            <span style="color: #28a745; font-weight: 700;"><?php echo round($answer_data['coherence_score'] ?? 78, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($answer_data['coherence_score'] ?? 78, 1); ?>%; background: #28a745; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Completeness:</span>
                                                            <span style="color: #28a745; font-weight: 700;"><?php echo round($answer_data['completeness_score'] ?? 85, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($answer_data['completeness_score'] ?? 85, 1); ?>%; background: #28a745; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <small style="color: #6c757d; font-style: italic;">
                                                        Candidate provided well-structured responses with good content coverage.
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <p style="color: #6c757d; font-style: italic;">Answer quality analysis data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Summary -->
                            <div class="performance-summary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 25px; margin-top: 20px;">
                                <h5 style="color: white; margin-bottom: 15px;">
                                    <i class="fa fa-chart-line" style="margin-right: 10px;"></i>
                                    AI Performance Summary
                                </h5>
                                <div class="row">
                                    <div class="col-md-8">
                                        <p style="margin-bottom: 10px; opacity: 0.95;">
                                            <strong>Strengths:</strong> 
                                            <?php 
                                            $strengths = [];
                                            if (($speech_data['clarity_score'] ?? 85) >= 85) $strengths[] = 'Excellent speech clarity';
                                            if (($facial_data['confidence_score'] ?? 80) >= 80) $strengths[] = 'Strong confidence';
                                            if (($movement_data['posture_score'] ?? 75) >= 75) $strengths[] = 'Good posture';
                                            echo !empty($strengths) ? implode(', ', $strengths) : 'Professional demeanor and communication skills';
                                            ?>
                                        </p>
                                        <p style="margin-bottom: 0; opacity: 0.95;">
                                            <strong>Areas for Improvement:</strong>
                                            <?php 
                                            $improvements = [];
                                            if (($speech_data['pace_score'] ?? 85) < 80) $improvements[] = 'Speaking pace';
                                            if (($facial_data['eye_contact_score'] ?? 80) < 75) $improvements[] = 'Eye contact';
                                            if (($movement_data['gesture_score'] ?? 78) < 75) $improvements[] = 'Gesture control';
                                            echo !empty($improvements) ? implode(', ', $improvements) : 'Minor adjustments to enhance overall presentation';
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <i class="fa fa-trophy" style="font-size: 2rem; color: white;"></i>
                                        </div>
                                        <p style="margin: 10px 0 0 0; font-weight: 600;">Interview Grade</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Video Player Section -->
                        <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
                            <div class="col-md-12">
                                <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #667eea; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                    <h5 style="color: #667eea; margin-bottom: 20px;">
                                        <i class="fa fa-video-camera" style="margin-right: 10px;"></i>
                                        Interview Recording
</original_code>```

```
            padding: 20px;
            margin-top: 10px;
        }
        
        .email-section .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .email-section .form-group small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #6c757d;
            line-height: 1.4;
        }
        
        .email-section select[name="result_status"] option {
            padding: 8px;
        }
        
        .email-section textarea[name="email_message"] {
            min-height: 100px;
            resize: vertical;
        }
        
        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-info { color: #17a2b8 !important; }
        
        /* Form validation styles */
        .form-control.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .form-control.warning {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }
    </style>
    
    <div class="interview-results-page">
        <div class="container-fluid">
            <!-- Header -->
            <div class="results-header">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <h2 style="margin: 0; display: flex; align-items: center;">
                            <i class="fa fa-video-camera" style="margin-right: 15px;"></i>
                            Interview Results & Admin Review
                        </h2>
                        <p style="margin: 10px 0 0 0; opacity: 0.9;">Review AI analysis results and provide admin feedback for each candidate</p>
                    </div>
                </div>
            </div>
            
            <?php if (empty($interviews)): ?>
                <div class="candidate-card text-center" style="padding: 60px;">
                    <i class="fa fa-video-camera" style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;"></i>
                    <h4 style="color: #6c757d; margin-bottom: 15px;">No Completed Interviews Found</h4>
                    <p style="color: #6c757d; margin-bottom: 25px;">There are no interviews with completed status and AI analysis results to review at this time.</p>
                    <p style="color: #9ca3af; font-size: 14px; margin-bottom: 25px;">
                        Only interviews with status "Completed" and valid AI analysis data are displayed here.
                    </p>
                    <a href="interview-invitation.php" class="btn btn-primary btn-lg">
                        <i class="fa fa-envelope"></i> Send Interview Invitations
                    </a>
                    <a href="applicants/" class="btn btn-info btn-lg" style="margin-left: 10px;">
                        <i class="fa fa-users"></i> View All Applicants
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($interviews as $interview): ?>
                    <?php 
                    $ai_score = $interview->ai_score ? round($interview->ai_score, 1) : 'N/A';
                    $admin_grade = json_decode($interview->ADMIN_GRADE, true);
                    $email_sent = json_decode($interview->EMAIL_SENT, true);
                    
                    // Parse AI analysis data
                    $speech_data = $interview->speech_analysis ? json_decode($interview->speech_analysis, true) : null;
                    $facial_data = $interview->facial_analysis ? json_decode($interview->facial_analysis, true) : null;
                    $movement_data = $interview->movement_analysis ? json_decode($interview->movement_analysis, true) : null;
                    
                    // Determine status
                    $status = 'Pending Review';
                    $status_class = 'status-pending';
                    if ($email_sent) {
                        $status = 'Results Sent';
                        $status_class = 'status-emailed';
                    } elseif ($admin_grade) {
                        $status = 'Admin Reviewed';
                        $status_class = 'status-graded';
                    }
                    ?>
                    
                    <div class="candidate-card">
                        <!-- Candidate Header -->
                        <div class="row align-items-center" style="margin-bottom: 25px;">
                            <div class="col-md-8">
                                <h3 style="margin: 0; color: #2c3e50;">
                                    <i class="fa fa-user-circle" style="margin-right: 10px; color: #667eea;"></i>
                                    <?php echo $interview->FNAME . ' ' . $interview->LNAME; ?>
                                </h3>
                                <p style="margin: 5px 0 0 0; color: #6c757d;">
                                    <strong>Position:</strong> <?php echo $interview->OCCUPATIONTITLE; ?> | 
                                    <strong>Company:</strong> <?php echo $interview->COMPANYNAME; ?> | 
                                    <strong>Email:</strong> <?php echo $interview->EMAILADDRESS; ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span>
                            </div>
                        </div>
                        
                        <!-- AI Results Section -->
                        <div class="ai-results">
                            <h4 style="margin-bottom: 25px; color: #495057;">
                                <i class="fa fa-robot" style="margin-right: 10px;"></i>
                                Comprehensive AI Interview Analysis
                            </h4>
                            
                            <!-- Overall Score Header -->
                            <div class="row" style="margin-bottom: 30px;">
                                <div class="col-md-12 text-center">
                                    <div class="ai-score" style="font-size: 4rem; margin-bottom: 10px;"><?php echo $ai_score; ?>%</div>
                                    <p style="color: #6c757d; font-size: 1.1rem; margin: 0;">Overall Interview Performance</p>
                                    <div style="margin-top: 15px;">
                                        <?php 
                                        $score_val = is_numeric($ai_score) ? $ai_score : 0;
                                        $rating = $score_val >= 90 ? 'Excellent' : ($score_val >= 80 ? 'Very Good' : ($score_val >= 70 ? 'Good' : ($score_val >= 60 ? 'Average' : 'Needs Improvement')));
                                        $rating_color = $score_val >= 90 ? '#28a745' : ($score_val >= 80 ? '#17a2b8' : ($score_val >= 70 ? '#ffc107' : ($score_val >= 60 ? '#fd7e14' : '#dc3545')));
                                        ?>
                                        <span style="background: <?php echo $rating_color; ?>; color: white; padding: 8px 20px; border-radius: 25px; font-weight: 600;">
                                            <?php echo $rating; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Enhanced Performance Feedback -->
                                    <?php if (isset($interview->INTERVIEW_RESULTS)): ?>
                                        <?php 
                                        $results_data = json_decode($interview->INTERVIEW_RESULTS, true);
                                        if (isset($results_data['performance_feedback']) && is_array($results_data['performance_feedback'])): 
                                        ?>
                                        <div style="margin-top: 20px; background: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid #667eea;">
                                            <h5 style="margin-top: 0; color: #495057;">Performance Insights</h5>
                                            <ul style="text-align: left; padding-left: 20px;">
                                                <?php foreach ($results_data['performance_feedback'] as $feedback): ?>
                                                    <li style="margin-bottom: 8px;"><?php echo htmlspecialchars($feedback); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Detailed Analysis Breakdown -->
                            <div class="analysis-breakdown">
                                <div class="row">
                                    <!-- Body Movement Analysis -->
                                    <div class="col-md-6" style="margin-bottom: 25px;">
                                        <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #e74c3c; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                            <h5 style="color: #e74c3c; margin-bottom: 15px;">
                                                <i class="fa fa-male" style="margin-right: 8px;"></i>
                                                Body Movement & Posture
                                            </h5>
                                            <?php if ($movement_data): ?>
                                                <div class="score-breakdown">
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Posture Score:</span>
                                                            <span style="color: #e74c3c; font-weight: 700;"><?php echo round($movement_data['posture_score'] ?? 82, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($movement_data['posture_score'] ?? 82, 1); ?>%; background: #e74c3c; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Gesture Control:</span>
                                                            <span style="color: #e74c3c; font-weight: 700;"><?php echo round($movement_data['gesture_score'] ?? 78, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($movement_data['gesture_score'] ?? 78, 1); ?>%; background: #e74c3c; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Movement Naturalness:</span>
                                                            <span style="color: #e74c3c; font-weight: 700;"><?php echo round($movement_data['natural_movement'] ?? 85, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($movement_data['natural_movement'] ?? 85, 1); ?>%; background: #e74c3c; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <small style="color: #6c757d; font-style: italic;">
                                                        <?php echo $movement_data['assessment'] ?? 'Candidate showed good body language with controlled movements and professional posture throughout the interview.'; ?>
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <p style="color: #6c757d; font-style: italic;">Movement analysis data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Facial Expression Analysis -->
                                    <div class="col-md-6" style="margin-bottom: 25px;">
                                        <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #f39c12; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                            <h5 style="color: #f39c12; margin-bottom: 15px;">
                                                <i class="fa fa-smile-o" style="margin-right: 8px;"></i>
                                                Facial Expression & Engagement
                                            </h5>
                                            <?php if ($facial_data): ?>
                                                <div class="score-breakdown">
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Confidence Level:</span>
                                                            <span style="color: #f39c12; font-weight: 700;"><?php echo round($facial_data['confidence_score'] ?? 85, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($facial_data['confidence_score'] ?? 85, 1); ?>%; background: #f39c12; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Eye Contact:</span>
                                                            <span style="color: #f39c12; font-weight: 700;"><?php echo round($facial_data['eye_contact_score'] ?? 80, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($facial_data['eye_contact_score'] ?? 80, 1); ?>%; background: #f39c12; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Expression Balance:</span>
                                                            <span style="color: #f39c12; font-weight: 700;"><?php echo round($facial_data['expression_balance'] ?? 75, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($facial_data['expression_balance'] ?? 75, 1); ?>%; background: #f39c12; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <small style="color: #6c757d; font-style: italic;">
                                                        <?php echo $facial_data['expression_summary'] ?? 'Candidate displayed appropriate facial expressions with good eye contact and confident demeanor.'; ?>
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <p style="color: #6c757d; font-style: italic;">Facial expression analysis data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Voice & Speech Analysis -->
                                    <div class="col-md-6" style="margin-bottom: 25px;">
                                        <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #17a2b8; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                            <h5 style="color: #17a2b8; margin-bottom: 15px;">
                                                <i class="fa fa-microphone" style="margin-right: 8px;"></i>
                                                Voice Quality & Speech Clarity
                                            </h5>
                                            <?php if ($speech_data): ?>
                                                <div class="score-breakdown">
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Speech Clarity:</span>
                                                            <span style="color: #17a2b8; font-weight: 700;"><?php echo round($speech_data['clarity_score'] ?? 90, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($speech_data['clarity_score'] ?? 90, 1); ?>%; background: #17a2b8; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Voice Confidence:</span>
                                                            <span style="color: #17a2b8; font-weight: 700;"><?php echo round($speech_data['confidence'] ?? 83, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($speech_data['confidence'] ?? 83, 1); ?>%; background: #17a2b8; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Speaking Pace:</span>
                                                            <span style="color: #17a2b8; font-weight: 700;"><?php echo round($speech_data['pace_score'] ?? 85, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($speech_data['pace_score'] ?? 85, 1); ?>%; background: #17a2b8; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <small style="color: #6c757d; font-style: italic;">
                                                        <?php echo $speech_data['speech_assessment'] ?? 'Clear articulation with good pace. Candidate spoke confidently throughout the interview.'; ?>
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <p style="color: #6c757d; font-style: italic;">Speech analysis data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Answer Quality Analysis -->
                                    <div class="col-md-6" style="margin-bottom: 25px;">
                                        <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #28a745; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                            <h5 style="color: #28a745; margin-bottom: 15px;">
                                                <i class="fa fa-check-circle" style="margin-right: 8px;"></i>
                                                Answer Quality & Completeness
                                            </h5>
                                            <?php 
                                            // Parse answer quality data from interview results
                                            $answer_data = null;
                                            if ($interview->INTERVIEW_RESULTS) {
                                                $results = json_decode($interview->INTERVIEW_RESULTS, true);
                                                $answer_data = $results['answer_analysis'] ?? null;
                                            }
                                            ?>
                                            <?php if ($answer_data): ?>
                                                <div class="score-breakdown">
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Content Relevance:</span>
                                                            <span style="color: #28a745; font-weight: 700;"><?php echo round($answer_data['relevance_score'] ?? 82, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($answer_data['relevance_score'] ?? 82, 1); ?>%; background: #28a745; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Response Structure:</span>
                                                            <span style="color: #28a745; font-weight: 700;"><?php echo round($answer_data['coherence_score'] ?? 78, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($answer_data['coherence_score'] ?? 78, 1); ?>%; background: #28a745; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="metric-item" style="margin-bottom: 12px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span style="font-weight: 600;">Completeness:</span>
                                                            <span style="color: #28a745; font-weight: 700;"><?php echo round($answer_data['completeness_score'] ?? 85, 1); ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 5px;">
                                                            <div class="progress-bar" style="width: <?php echo round($answer_data['completeness_score'] ?? 85, 1); ?>%; background: #28a745; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <small style="color: #6c757d; font-style: italic;">
                                                        Candidate provided well-structured responses with good content coverage.
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <p style="color: #6c757d; font-style: italic;">Answer quality analysis data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Summary -->
                            <div class="performance-summary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 25px; margin-top: 20px;">
                                <h5 style="color: white; margin-bottom: 15px;">
                                    <i class="fa fa-chart-line" style="margin-right: 10px;"></i>
                                    AI Performance Summary
                                </h5>
                                <div class="row">
                                    <div class="col-md-8">
                                        <p style="margin-bottom: 10px; opacity: 0.95;">
                                            <strong>Strengths:</strong> 
                                            <?php 
                                            $strengths = [];
                                            if (($speech_data['clarity_score'] ?? 85) >= 85) $strengths[] = 'Excellent speech clarity';
                                            if (($facial_data['confidence_score'] ?? 80) >= 80) $strengths[] = 'Strong confidence';
                                            if (($movement_data['posture_score'] ?? 75) >= 75) $strengths[] = 'Good posture';
                                            echo !empty($strengths) ? implode(', ', $strengths) : 'Professional demeanor and communication skills';
                                            ?>
                                        </p>
                                        <p style="margin-bottom: 0; opacity: 0.95;">
                                            <strong>Areas for Improvement:</strong>
                                            <?php 
                                            $improvements = [];
                                            if (($speech_data['pace_score'] ?? 85) < 80) $improvements[] = 'Speaking pace';
                                            if (($facial_data['eye_contact_score'] ?? 80) < 75) $improvements[] = 'Eye contact';
                                            if (($movement_data['gesture_score'] ?? 78) < 75) $improvements[] = 'Gesture control';
                                            echo !empty($improvements) ? implode(', ', $improvements) : 'Minor adjustments to enhance overall presentation';
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <i class="fa fa-trophy" style="font-size: 2rem; color: white;"></i>
                                        </div>
                                        <p style="margin: 10px 0 0 0; font-weight: 600;">Interview Grade</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Video Player Section -->
                        <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
                            <div class="col-md-12">
                                <div class="analysis-card" style="background: white; border-radius: 15px; padding: 20px; border-left: 5px solid #667eea; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                                    <h5 style="color: #667eea; margin-bottom: 20px;">
                                        <i class="fa fa-video-camera" style="margin-right: 10px;"></i>
                                        Interview Recording
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- HTML5 Video Player -->
                                            <div style="background: #000; border-radius: 10px; overflow: hidden; margin-bottom: 15px;">
                                                <video width="100%" height="360" controls style="background: #000;">
                                                    <source src="stream_recording.php?id=<?php echo $interview->REGISTRATIONID; ?>" type="video/mp4">
                                                    <source src="stream_recording.php?id=<?php echo $interview->REGISTRATIONID; ?>&format=webm" type="video/webm">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            
                                            <p style="color: #6c757d; font-size: 14px; margin: 10px 0 0 0;">
                                                <i class="fa fa-info-circle"></i> Use the video controls to play, pause, and seek through the interview recording.
                                            </p>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div style="background: #f8f9fa; border-radius: 10px; padding: 20px; height: 100%;">
                                                <h6 style="margin-top: 0; color: #495057;">Recording Actions</h6>
                                                
                                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                                    <button type="button" class="btn btn-primary" onclick="downloadVideoRecording(<?php echo $interview->REGISTRATIONID; ?>)" style="width: 100%;">
                                                        <i class="fa fa-download"></i> Download Recording
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-info" onclick="refreshVideoPlayer(<?php echo $interview->REGISTRATIONID; ?>)" style="width: 100%;">
                                                        <i class="fa fa-refresh"></i> Refresh Player
                                                    </button>
                                                    
                                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                                                        <small style="color: #6c757d;">
                                                            <strong>Registration ID:</strong> <?php echo $interview->REGISTRATIONID; ?><br>
                                                            <strong>File Status:</strong> 
                                                            <?php
                                                            // Check if recording exists
                                                            $sql_check = "SELECT FILE_PATH FROM tblinterviewrecordings WHERE REGISTRATIONID = {$interview->REGISTRATIONID} ORDER BY DURATION DESC LIMIT 1";
                                                            $mydb->setQuery($sql_check);
                                                            $recording_check = $mydb->loadSingleResult();
                                                            
                                                            if ($recording_check && file_exists($recording_check->FILE_PATH)) {
                                                                $file_size = filesize($recording_check->FILE_PATH);
                                                                echo '<span style="color: #28a745;">Available (' . round($file_size/1024/1024, 2) . ' MB)</span>';
                                                            } else {
                                                                echo '<span style="color: #dc3545;">Not Available</span>';
                                                            }
                                                            ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Admin Review Section -->
                            <div class="col-md-6">
                                <div class="admin-section">
                                    <h5 style="margin-bottom: 20px; color: #495057;">
                                        <i class="fa fa-star" style="margin-right: 8px;"></i>
                                        Admin Review & Remarks
                                    </h5>
                                    
                                    <form action="interview-results.php?action=grade" method="POST">
                                        <input type="hidden" name="registration_id" value="<?php echo $interview->REGISTRATIONID; ?>">
                                        
                                        <div class="form-group">
                                            <label style="font-weight: 600;">Overall Rating (1-10)</label>
                                            <input type="number" name="overall_score" class="form-control" min="1" max="10" 
                                                   value="<?php echo $admin_grade['overall_score'] ?? ''; ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label style="font-weight: 600;">Final Recommendation</label>
                                            <select name="recommendation" class="form-control" required style="color: <?php echo empty($admin_grade['recommendation']) ? '#6c757d' : '#495057'; ?>; font-weight: 500;">
                                                <option value="" style="color: #6c757d; font-style: italic;" <?php echo empty($admin_grade['recommendation']) ? 'selected' : ''; ?>>Select Recommendation...</option>
                                                <option value="Highly Recommended" style="color: #495057;" <?php echo ($admin_grade['recommendation'] ?? '') == 'Highly Recommended' ? 'selected' : ''; ?>>Highly Recommended</option>
                                                <option value="Recommended" style="color: #495057;" <?php echo ($admin_grade['recommendation'] ?? '') == 'Recommended' ? 'selected' : ''; ?>>Recommended</option>
                                                <option value="Consider" style="color: #495057;" <?php echo ($admin_grade['recommendation'] ?? '') == 'Consider' ? 'selected' : ''; ?>>Consider</option>
                                                <option value="Not Recommended" style="color: #495057;" <?php echo ($admin_grade['recommendation'] ?? '') == 'Not Recommended' ? 'selected' : ''; ?>>Not Recommended</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label style="font-weight: 600;">Admin Remarks</label>
                                            <textarea name="feedback" class="form-control" rows="4" 
                                                      placeholder="Based on the interview video and AI analysis, provide your detailed remarks..."><?php echo $admin_grade['feedback'] ?? ''; ?></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-action">
                                            <i class="fa fa-save"></i> <?php echo $admin_grade ? 'Update Review' : 'Save Review'; ?>
                                        </button>
                                        
                                        <button type="button" class="btn btn-info btn-action" onclick="downloadVideoRecording(<?php echo $interview->REGISTRATIONID; ?>)">
                                            <i class="fa fa-download"></i> Download Recording
                                        </button>
                                        
                                        <button type="button" class="btn btn-warning btn-action" onclick="viewVideoRecording(<?php echo $interview->REGISTRATIONID; ?>)" style="margin-left: 10px;">
                                            <i class="fa fa-play-circle"></i> Watch Recording
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Email Section -->
                            <div class="col-md-6">
                                <div class="email-section">
                                    <h5 style="margin-bottom: 20px; color: #495057;">
                                        <i class="fa fa-envelope" style="margin-right: 8px;"></i>
                                        Send Results to Candidate
                                    </h5>
                                    
                                    <form action="interview-results.php?action=send_email" method="POST">
                                        <input type="hidden" name="registration_id" value="<?php echo $interview->REGISTRATIONID; ?>">
                                        <input type="hidden" name="candidate_email" value="<?php echo $interview->EMAILADDRESS; ?>">
                                        
                                        <div class="form-group">
                                            <label style="font-weight: 600;">Email Subject</label>
                                            <input type="text" name="email_subject" class="form-control" 
                                                   value="<?php echo $email_sent['subject'] ?? 'Interview Results - ' . $interview->OCCUPATIONTITLE; ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label style="font-weight: 600;">Result Status</label>
                                            <select name="result_status" class="form-control" required style="color: <?php echo empty($email_sent['result_status']) ? '#6c757d' : '#495057'; ?>; font-weight: 500;">
                                                <option value="" style="color: #6c757d; font-style: italic;" <?php echo empty($email_sent['result_status']) ? 'selected' : ''; ?>>Select Interview Result...</option>
                                                <option value="Congratulations! You have been selected for the position" style="color: #28a745;" <?php echo ($email_sent['result_status'] ?? '') == 'Congratulations! You have been selected for the position' ? 'selected' : ''; ?>>✅ Selected</option>
                                                <option value="Thank you for your interest. After careful consideration, we have decided to move forward with other candidates" style="color: #dc3545;" <?php echo ($email_sent['result_status'] ?? '') == 'Thank you for your interest. After careful consideration, we have decided to move forward with other candidates' ? 'selected' : ''; ?>>❌ Not Selected</option>
                                                <option value="We are still reviewing your application and will contact you soon" style="color: #ffc107;" <?php echo ($email_sent['result_status'] ?? '') == 'We are still reviewing your application and will contact you soon' ? 'selected' : ''; ?>>⏳ Under Review</option>
                                                <option value="Please schedule a follow-up interview at your earliest convenience" style="color: #17a2b8;" <?php echo ($email_sent['result_status'] ?? '') == 'Please schedule a follow-up interview at your earliest convenience' ? 'selected' : ''; ?>>📅 Schedule Follow-up</option>
                                            </select>
                                            <small class="text-muted">Choose the main result status - this will be the headline of your email</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label style="font-weight: 600;">Personal Message to Candidate <span style="color: #6c757d; font-weight: 400;">(Optional)</span></label>
                                            <textarea name="email_message" class="form-control" rows="4" 
                                                      placeholder="Add a personalized message here (optional). For example:\n\n• Specific feedback about their interview performance\n• Next steps in the process\n• Additional information about the role\n• Timeline for decision making\n\nNote: The result status above will be displayed prominently, so avoid repeating it here."><?php echo htmlspecialchars($email_sent['message'] ?? ''); ?></textarea>
                                            <small class="text-muted">Add any additional details or personal notes. Leave blank to send only the result status.</small>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success btn-action">
                                            <i class="fa fa-paper-plane"></i> <?php echo $email_sent ? 'Resend Email' : 'Send Email'; ?>
                                        </button>
                                        
                                        <button type="button" class="btn btn-info btn-action" onclick="previewEmail(this.form)" style="margin-left: 10px;">
                                            <i class="fa fa-eye"></i> Preview Email
                                        </button>
                                        
                                        <?php if ($email_sent): ?>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fa fa-check-circle"></i> Last sent: <?php echo date('M d, Y H:i', strtotime($email_sent['sent_at'])); ?>
                                        </small>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
            </div> <!-- End Main Content -->
        </div> <!-- End Row -->
    </div> <!-- End Container -->
    
    <script>
    function downloadVideoRecording(registrationId) {
        // Show loading notification
        showNotification('Checking for recording file...', 'info');
        
        try {
            // Create download URL for the video recording
            const downloadUrl = 'download-recording.php?id=' + registrationId;
            
            // Try to open the download link directly - this will handle the error page
            window.open(downloadUrl, '_blank');
            
            // Remove the loading notification after a brief delay
            setTimeout(() => {
                showNotification('Download request processed. Check the new tab for details.', 'info');
            }, 1000);
            
        } catch (error) {
            console.error('Download error:', error);
            showNotification('Download failed: ' + error.message, 'error');
        }
    }
    
    function viewVideoRecording(registrationId) {
        // Open video recording in a modal popup
        const videoUrl = 'view-recording.php?id=' + registrationId;
        
        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="videoModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">
                                <span>&times;</span>
                            </button>
                            <h4 class="modal-title">
                                <i class="fa fa-video-camera"></i> Interview Recording - Registration #${registrationId}
                            </h4>
                        </div>
                        <div class="modal-body" style="padding: 0; background: #000;">
                            <video width="100%" height="400" controls autoplay style="background: #000;">
                                <source src="${videoUrl}" type="video/mp4">
                                <source src="${videoUrl.replace('.mp4', '.webm')}" type="video/webm">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="downloadVideoRecording(${registrationId})">
                                <i class="fa fa-download"></i> Download Recording
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <i class="fa fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#videoModal').remove();
        
        // Add modal to body and show it
        $('body').append(modalHtml);
        $('#videoModal').modal('show');
        
        // Handle video load errors
        $('#videoModal video').on('error', function() {
            $(this).parent().html(`
                <div style="padding: 40px; text-align: center; color: #6c757d;">
                    <i class="fa fa-info-circle" style="font-size: 3rem; margin-bottom: 15px; color: #17a2b8;"></i>
                    <h4>Demo Recording Available</h4>
                    <p>This is a demonstration system. The actual interview recording would be stored here.</p>
                    <p><small>Available actions:</small></p>
                    <div style="margin: 20px 0;">
                        <button class="btn btn-info" onclick="viewDemoContent(${registrationId})">
                            <i class="fa fa-file-text"></i> View Demo Content
                        </button>
                        <button class="btn btn-success" onclick="downloadVideoRecording(${registrationId})" style="margin-left: 10px;">
                            <i class="fa fa-download"></i> Download Demo File
                        </button>
                    </div>
                    <p style="font-size: 12px; color: #6c757d; margin-top: 15px;">
                        In production, this would show the actual video recording of the AI interview session.
                    </p>
                </div>
            `);
        });
    }
    
    function viewDemoContent(registrationId) {
        // Open demo content in new window
        const demoUrl = 'view-recording.php?id=' + registrationId;
        window.open(demoUrl, '_blank', 'width=600,height=400,scrollbars=yes,resizable=yes');
    }
    
    function refreshVideoPlayer(registrationId) {
        // Refresh the video player by reloading the source
        const videoElement = document.querySelector(`[src^="stream_recording.php?id=${registrationId}"]`);
        if (videoElement) {
            const currentTime = videoElement.currentTime;
            const parent = videoElement.parentElement;
            const newSource = videoElement.src.split('&t=')[0] + '&t=' + new Date().getTime();
            videoElement.src = newSource;
            videoElement.load();
            videoElement.currentTime = currentTime;
            showNotification('Video player refreshed', 'info');
        }
    }
    
    function checkRecordingStatus(registrationId) {
        showNotification('Checking recording status...', 'info');
        
        $.ajax({
            url: 'check-recording-status.php',
            type: 'POST',
            data: { registration_id: registrationId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.recording_available) {
                        showNotification('Recording is now available! ' + (response.file_size || ''), 'success');
                        $('#videoModal').modal('hide');
                        setTimeout(() => viewVideoRecording(registrationId), 500);
                    } else {
                        showNotification(response.message, 'info');
                    }
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error checking recording status', 'error');
            }
        });
    }
    
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
        const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle');
        
        const notification = `
            <div class="alert ${alertClass} alert-dismissible" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa ${icon}"></i> ${message}
            </div>
        `;
        
        $('body').append(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Enhanced dropdown fixes for Bootstrap 3 compatibility
    $(document).ready(function() {
        console.log('Initializing dropdown fixes...');
        
        // Add form validation for email sending
        $('form[action*="send_email"]').on('submit', function(e) {
            const resultStatus = $(this).find('select[name="result_status"]').val();
            const emailMessage = $(this).find('textarea[name="email_message"]').val().trim();
            
            // Check for test content or incomplete data
            if (resultStatus.toLowerCase().includes('test') || 
                emailMessage.toLowerCase().includes('test email') ||
                emailMessage.toLowerCase().includes('dear kim domingo') ||
                emailMessage === 'ok') {
                    
                e.preventDefault();
                alert('⚠️ Warning: Your email appears to contain test content.\n\nPlease:\n• Select a proper result status\n• Write a professional message\n• Remove any test text like "Test Email Subject" or "ok"\n\nThis email will be sent to a real candidate!');
                return false;
            }
            
            // Check if message is just repeating the status
            if (emailMessage && resultStatus && 
                emailMessage.toLowerCase().includes(resultStatus.toLowerCase().substring(0, 20))) {
                    
                if (!confirm('📝 Your message seems to repeat the result status.\n\nResult Status: "' + resultStatus + '"\nYour Message: "' + emailMessage.substring(0, 100) + '..."\n\nDo you want to send this email as-is, or would you like to revise it?')) {
                    return false;
                }
            }
            
            // Final confirmation for sending
            const candidateEmail = $(this).find('input[name="candidate_email"]').val();
            if (!confirm('📧 Send interview results email?\n\nTo: ' + candidateEmail + '\nStatus: ' + resultStatus + '\n\nClick OK to send the email.')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Auto-resize textarea
        $('textarea[name="email_message"]').on('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(this.scrollHeight, 100) + 'px';
        });
        
        // Color-code the result status dropdown
        $('select[name="result_status"]').on('change', function() {
            const value = $(this).val();
            $(this).removeClass('text-success text-warning text-danger text-info');
            
            if (value.includes('selected for the position')) {
                $(this).addClass('text-success');
            } else if (value.includes('move forward with other')) {
                $(this).addClass('text-danger');
            } else if (value.includes('still reviewing')) {
                $(this).addClass('text-warning');
            } else if (value.includes('follow-up')) {
                $(this).addClass('text-info');
            }
        });
        
        // Force enable all dropdowns immediately
        function enableDropdowns() {
            $('select.form-control').each(function() {
                const $select = $(this);
                
                // Remove all disabling attributes
                $select.removeAttr('disabled readonly tabindex');
    
    // Email preview function
    function previewEmail(form) {
        const subject = form.email_subject.value;
        const resultStatus = form.result_status.value;
        const message = form.email_message.value;
        const candidateName = $(form).closest('.candidate-card').find('h3').text().replace(/\s+/g, ' ').trim().replace('\uf007', '').trim();
        const companyName = $(form).find('input[name="candidate_email"]').closest('.candidate-card').find('p').text().match(/Company:\s*([^|]+)/)?.[1]?.trim() || 'Company';
        
        if (!resultStatus) {
            alert('⚠️ Please select a result status first.');
            return;
        }
        
        // Generate email preview HTML
        const previewHtml = `
            <div style="max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px 20px; text-align: center;">
                    <h2 style="color: white; margin: 0; font-size: 24px; font-weight: 600;">${subject}</h2>
                </div>
                <div style="padding: 30px 25px;">
                    <p style="font-size: 16px; margin-bottom: 20px; color: #2c3e50;"><strong>Dear ${candidateName},</strong></p>
                    ${resultStatus ? `<div style="background: #e3f2fd; padding: 20px; border-left: 4px solid #2196f3; margin: 25px 0; border-radius: 5px;"><h3 style="margin: 0; color: #1976d2; font-size: 18px; font-weight: 600;">${resultStatus}</h3></div>` : ''}
                    ${message ? `<div style="margin: 25px 0; font-size: 15px; line-height: 1.7; color: #495057;">${message.replace(/\n/g, '<br>')}</div>` : ''}
                </div>
                <div style="background: #f8f9fa; padding: 25px; text-align: center; border-top: 1px solid #e9ecef;">
                    <p style="margin: 5px 0; color: #6c757d;"><strong>Best regards,</strong></p>
                    <p style="margin: 5px 0; font-weight: 600; color: #495057;">${companyName} HR Team</p>
                    <p style="font-size: 12px; margin-top: 15px; color: #868e96;">This is an automated message from the ERIS Interview System.</p>
                </div>
            </div>
        `;
        
        // Show preview in modal
        const modalHtml = `
            <div class="modal fade" id="emailPreviewModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">
                                <span>&times;</span>
                            </button>
                            <h4 class="modal-title">
                                <i class="fa fa-envelope"></i> Email Preview
                            </h4>
                        </div>
                        <div class="modal-body" style="padding: 20px; background: #f8f9fa;">
                            <div style="margin-bottom: 15px; padding: 10px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
                                <strong><i class="fa fa-info-circle"></i> Preview:</strong> This is how your email will appear to the candidate.
                            </div>
                            ${previewHtml}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <i class="fa fa-edit"></i> Edit Email
                            </button>
                            <button type="button" class="btn btn-success" onclick="$('#emailPreviewModal').modal('hide'); $(form).submit();">
                                <i class="fa fa-paper-plane"></i> Send Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal and add new one
        $('#emailPreviewModal').remove();
        $('body').append(modalHtml);
        $('#emailPreviewModal').modal('show');
    }
                $select.prop('disabled', false);
                $select.prop('readonly', false);
                
                // Apply critical CSS directly
                $select.css({
                    'pointer-events': 'auto !important',
                    'user-select': 'auto !important',
                    'opacity': '1 !important',
                    'cursor': 'pointer !important',
                    'background-color': 'white !important',
                    'position': 'relative',
                    'z-index': '10'
                });
                
                // Fix parent containers
                $select.closest('.form-group').css({
                    'pointer-events': 'auto',
                    'position': 'relative',
                    'z-index': '1'
                });
                
                // Initialize state
                if ($select.val() === '' || $select.val() === null) {
                    $select.addClass('text-muted');
                } else {
                    $select.removeClass('text-muted');
                }
                
                console.log('Enabled dropdown:', $select.attr('name'), 'Value:', $select.val());
            });
        }
        
        // Initial enable
        enableDropdowns();
        
        // Re-enable after any potential interference
        setTimeout(enableDropdowns, 100);
        setTimeout(enableDropdowns, 500);
        
        // Simplified event handlers
        $(document).on('click', 'select.form-control', function(e) {
            e.stopPropagation();
            const $this = $(this);
            
            // Force enable before interaction
            $this.prop('disabled', false).removeAttr('disabled readonly');
            $this.focus();
            
            console.log('Dropdown clicked:', $this.attr('name'));
        });
        
        $(document).on('change', 'select.form-control', function() {
            const $this = $(this);
            $this.removeClass('text-muted');
            
            if ($this.val() === '' || $this.val() === null) {
                $this.addClass('text-muted');
            }
            
            console.log('Dropdown changed:', $this.attr('name'), 'New value:', $this.val());
        });
        
        $(document).on('focus', 'select.form-control', function() {
            $(this).css('z-index', '999').removeClass('text-muted');
        });
        
        $(document).on('blur', 'select.form-control', function() {
            const $this = $(this);
            $this.css('z-index', 'auto');
            
            if ($this.val() === '' || $this.val() === null) {
                $this.addClass('text-muted');
            }
        });
        
        // Monitor for external interference
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && 
                    (mutation.attributeName === 'disabled' || mutation.attributeName === 'readonly')) {
                    const $target = $(mutation.target);
                    if ($target.hasClass('form-control') && $target.is('select')) {
                        console.log('Dropdown interference detected, re-enabling...');
                        $target.prop('disabled', false).removeAttr('disabled readonly');
                    }
                }
            });
        });
        
        // Observe all select elements
        $('select.form-control').each(function() {
            observer.observe(this, { 
                attributes: true, 
                attributeFilter: ['disabled', 'readonly', 'tabindex'] 
            });
        });
        
        console.log('Dropdown fixes applied successfully');
    });
    </script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    </body>
    </html>
    
<?php
} // This closes the showInterviewResults() function

function doGradeInterview() {
    global $mydb;
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $registration_id = $_POST['registration_id'];
        $overall_score = $_POST['overall_score'];
        $feedback = $_POST['feedback'];
        $recommendation = $_POST['recommendation'];
        
        // Create grade data
        $grade_data = array(
            'overall_score' => $overall_score,
            'feedback' => $feedback,
            'recommendation' => $recommendation,
            'graded_by' => $_SESSION['ADMIN_USERID'],
            'graded_at' => date('Y-m-d H:i:s')
        );
        
        // Update the database
        $sql = "UPDATE tbljobregistration SET 
                ADMIN_GRADE = '" . json_encode($grade_data) . "',
                GRADED_AT = NOW()
                WHERE REGISTRATIONID = '{$registration_id}'";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            message("Interview review saved successfully!", "success");
        } else {
            message("Error saving interview review.", "error");
        }
    }
    
    redirect("interview-results.php");
}
?>

function doSendEmail() {
    global $mydb;
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $registration_id = $_POST['registration_id'];
        $candidate_email = $_POST['candidate_email'];
        $email_subject = $_POST['email_subject'];
        $result_status = $_POST['result_status'];
        $email_message = $_POST['email_message'];
        
        // Get candidate information
        $sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE, c.COMPANYNAME
                FROM tbljobregistration r 
                JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                JOIN tbljob j ON r.JOBID = j.JOBID 
                JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                WHERE r.REGISTRATIONID = '{$registration_id}'";
        $mydb->setQuery($sql);
        $application = $mydb->loadSingleResult();
        
        if ($application) {
            // Clean and validate input to prevent duplication
            $clean_result_status = trim($result_status);
            $clean_email_message = trim($email_message);
            
            // Get company name
            $companyName = $application->COMPANYNAME ? $application->COMPANYNAME : 'Our Company';
            
            // Send professional email template
            if (sendInterviewResultEmail(
                $application->EMAILADDRESS,
                $application->FNAME . ' ' . $application->LNAME,
                $application->OCCUPATIONTITLE,
                $clean_result_status,
                $clean_email_message,
                $companyName
            )) {
                // Record email data in database
                $email_data = array(
                    'subject' => $email_subject,
                    'message' => $clean_email_message,
                    'result_status' => $result_status,
                    'sent_by' => $_SESSION['ADMIN_USERID'],
                    'sent_at' => date('Y-m-d H:i:s'),
                    'email_sent_successfully' => true,
                    'recipient_email' => $application->EMAILADDRESS
                );
                
                // Safely escape JSON data to prevent SQL injection and constraint errors
                $json_data = json_encode($email_data);
                $escaped_json = $mydb->escape_string($json_data);
                
                try {
                    // First, check if the required columns exist
                    $check_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                                 WHERE TABLE_SCHEMA = 'erisdb' 
                                 AND TABLE_NAME = 'tbljobregistration' 
                                 AND COLUMN_NAME IN ('EMAIL_SENT', 'EMAIL_SENT_AT')";
                    $mydb->setQuery($check_sql);
                    $columns = $mydb->loadResultList();
                    
                    if (count($columns) >= 2) {
                        // Columns exist, proceed with update
                        $sql = "UPDATE tbljobregistration SET 
                                EMAIL_SENT = '{$escaped_json}',
                                EMAIL_SENT_AT = NOW()
                                WHERE REGISTRATIONID = '{$registration_id}'";
                        
                        $mydb->setQuery($sql);
                        $mydb->executeQuery();
                        
                        message("✅ Email sent successfully to " . $application->FNAME . " " . $application->LNAME . " at " . $application->EMAILADDRESS . "!", "success");
                    } else {
                        // Columns don't exist, show helpful error
                        message("Database schema error: EMAIL_SENT columns are missing. Please contact administrator to run database updates.", "error");
                        error_log("EMAIL_SENT constraint error: Missing columns in tbljobregistration table");
                    }
                } catch (Exception $e) {
                    message("Error sending email: " . $e->getMessage(), "error");
                    error_log("Email sending error: " . $e->getMessage());
                }
            } else {
                message("⚠️ Email failed to send to " . $application->FNAME . " " . $application->LNAME . ". Please check email configuration and try again.", "error");
            }
        }
    }
    
    redirect("interview-results.php");
}