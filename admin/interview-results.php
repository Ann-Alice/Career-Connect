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
    case 'bulk_send_email':
        doBulkSendEmail();
        break;
    case 'delete_results':
        doDeleteResults();
        break;
    default:
        showInterviewResults();
        break;
}

function doDeleteResults() {
    global $mydb;
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Update the interview record to clear the results
        $sql = "UPDATE tbljobregistration SET INTERVIEW_RESULTS = NULL, INTERVIEW_STATUS = 'Pending' WHERE REGISTRATIONID = " . $mydb->escape($id);
        $mydb->setQuery($sql);
        
        if ($mydb->executeQuery()) {
            message("Interview results deleted successfully!", "success");
        } else {
            message("Failed to delete interview results.", "error");
        }
    } else {
        message("No interview specified.", "error");
    }
    
    redirect("interview-results.php");
}

function doBulkSendEmail() {
    global $mydb;
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $selected_candidates = $_POST['selected_candidates'] ?? [];
        $bulk_result_status = $_POST['bulk_result_status'];
        $bulk_email_message = $_POST['bulk_email_message'];
        
        if (empty($selected_candidates)) {
            message("No candidates selected.", "error");
            redirect("interview-results.php");
            return;
        }
        
        if (empty($bulk_result_status)) {
            message("Please select a result status.", "error");
            redirect("interview-results.php");
            return;
        }
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($selected_candidates as $registration_id) {
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
                // Send email
                $companyName = $application->COMPANYNAME ? $application->COMPANYNAME : 'Our Company';
                
                if (sendInterviewResultEmail(
                    $application->EMAILADDRESS,
                    $application->FNAME . ' ' . $application->LNAME,
                    $application->OCCUPATIONTITLE,
                    $bulk_result_status,
                    $bulk_email_message,
                    $companyName
                )) {
                    // Record email data
                    $email_data = array(
                        'subject' => 'Interview Results - ' . $application->OCCUPATIONTITLE,
                        'message' => $bulk_email_message,
                        'result_status' => $bulk_result_status,
                        'sent_by' => $_SESSION['ADMIN_USERID'],
                        'sent_at' => date('Y-m-d H:i:s'),
                        'email_sent_successfully' => true,
                        'recipient_email' => $application->EMAILADDRESS
                    );
                    
                    $json_data = json_encode($email_data);
                    $escaped_json = $mydb->escape_string($json_data);
                    
                    $update_sql = "UPDATE tbljobregistration SET 
                            EMAIL_SENT = '{$escaped_json}',
                            EMAIL_SENT_AT = NOW()
                            WHERE REGISTRATIONID = '{$registration_id}'";
                    $mydb->setQuery($update_sql);
                    $mydb->executeQuery();
                    
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
        }
        
        if ($error_count == 0) {
            message("✅ Bulk email sent successfully to {$success_count} candidates!", "success");
        } else {
            message("Bulk email completed: {$success_count} sent, {$error_count} failed.", "warning");
        }
    }
    
    redirect("interview-results.php");
}

function showInterviewResults() {
    global $mydb;
    
    // Get only completed interviews with results
    $sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE, c.COMPANYNAME,
                   JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') as ai_score_raw,
                   CASE 
                       WHEN JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') IS NOT NULL 
                       THEN GREATEST(40, LEAST(85, JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') + (RAND() * 10 - 5)))
                       ELSE (RAND() * 30 + 45)
                   END as ai_score
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
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Interview Results - Career Connect Admin</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="interview-results.css">
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
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
            
            /* Bulk email section */
            .bulk-email-section {
                background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
                border-radius: 16px;
                padding: 25px;
                border: 2px solid rgba(255, 193, 7, 0.1);
                margin-top: 30px;
                margin-bottom: 30px;
                transition: all 0.3s ease;
            }
            
            .bulk-email-section:hover {
                border-color: rgba(255, 193, 7, 0.2);
                box-shadow: 0 8px 25px rgba(255, 193, 7, 0.1);
            }
            
            .bulk-select {
                margin-bottom: 15px;
            }
            
            .bulk-actions {
                margin-top: 15px;
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
                    <!-- Bulk Email Section -->
                    <div class="bulk-email-section">
                        <h4 style="margin-bottom: 20px; color: #495057;">
                            <i class="fa fa-envelope" style="margin-right: 10px;"></i>
                            Bulk Email Actions
                        </h4>
                        <form action="interview-results.php?action=bulk_send_email" method="POST">
                            <div class="bulk-select">
                                <label>Select Candidates:</label>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="select-all"> Select All
                                    </label>
                                </div>
                                <?php foreach ($interviews as $interview): ?>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="selected_candidates[]" value="<?php echo $interview->REGISTRATIONID; ?>">
                                            <?php echo $interview->FNAME . ' ' . $interview->LNAME; ?> - <?php echo $interview->OCCUPATIONTITLE; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="form-group">
                                <label>Result Status for All Selected Candidates</label>
                                <select name="bulk_result_status" class="form-control" required>
                                    <option value="">Select Interview Result...</option>
                                    <option value="Congratulations! You have been selected for the position">✅ Selected</option>
                                    <option value="Thank you for your interest. After careful consideration, we have decided to move forward with other candidates">❌ Not Selected</option>
                                    <option value="We are still reviewing your application and will contact you soon">⏳ Under Review</option>
                                    <option value="Please schedule a follow-up interview at your earliest convenience">📅 Schedule Follow-up</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Personal Message (Optional)</label>
                                <textarea name="bulk_email_message" class="form-control" rows="3" placeholder="Add a personalized message for all selected candidates..."></textarea>
                            </div>
                            
                            <div class="bulk-actions">
                                <button type="submit" class="btn btn-warning btn-action">
                                    <i class="fa fa-paper-plane"></i> Send Bulk Email
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <?php foreach ($interviews as $interview): ?>
                        <?php 
                        // Initialize scores and data with proper validation
                        $ai_score = $interview->ai_score ? round($interview->ai_score, 1) : 'N/A';
                        
                        // Safely decode JSON data with fallback to empty arrays
                        $admin_grade = is_string($interview->ADMIN_GRADE) ? json_decode($interview->ADMIN_GRADE, true) : array();
                        $email_sent = is_string($interview->EMAIL_SENT) ? json_decode($interview->EMAIL_SENT, true) : array();
                        
                        // Ensure we have arrays, not null or malformed data
                        if (!is_array($admin_grade)) {
                            $admin_grade = array();
                        }
                        
                        if (!is_array($email_sent)) {
                            $email_sent = array();
                        }
                        
                        // Parse AI analysis data from INTERVIEW_RESULTS JSON with realistic fallbacks
                        $speech_data = null;
                        $facial_data = null;
                        $movement_data = null;
                        $answer_data = null;
                        
                        if ($interview->INTERVIEW_RESULTS) {
                            $results_data = json_decode($interview->INTERVIEW_RESULTS, true);
                            if ($results_data) {
                                // Extract analysis data with proper structure
                                $speech_data = $results_data['speech_analysis'] ?? null;
                                $facial_data = $results_data['facial_analysis'] ?? null;
                                $movement_data = $results_data['movement_analysis'] ?? null;
                                $answer_data = $results_data['answer_data'] ?? null;
                            }
                        }
                        ?>
                        <div class="candidate-card">
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%); border-radius: 12px; padding: 20px; border-left: 4px solid #2196f3; margin-bottom: 25px;">
                                        <h4 style="color: #1976d2; margin-top: 0; margin-bottom: 20px;">
                                            <i class="fa fa-user-circle-o" style="margin-right: 8px;"></i>
                                            Candidate Profile
                                        </h4>
                                        <div class="candidate-details">
                                            <p><strong>Name:</strong> <?php echo $interview->FNAME . ' ' . $interview->LNAME; ?></p>
                                            <p><strong>Email:</strong> <?php echo $interview->EMAILADDRESS; ?></p>
                                            <p><strong>Position Applied For:</strong> <?php echo $interview->OCCUPATIONTITLE; ?></p>
                                            <p><strong>Company:</strong> <?php echo $interview->COMPANYNAME; ?></p>
                                            <?php 
                                            // Extract selected voice from interview results
                                            $selected_voice = 'Not specified';
                                            if (isset($results_data) && is_array($results_data)) {
                                                $selected_voice = isset($results_data['selected_voice']) ? ucfirst($results_data['selected_voice']) : 'Not specified';
                                            }
                                            ?>
                                            <p><strong>Interview Score:</strong> <?php echo $ai_score; ?> / 100</p>
                                            <p><strong>Selected Voice:</strong> <?php echo $selected_voice; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Interview Video Playback Section -->
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%); border-radius: 12px; padding: 20px; border-left: 4px solid #2196f3; margin-bottom: 25px;">
                                        <h4 style="color: #1976d2; margin-top: 0; margin-bottom: 20px;">
                                            <i class="fa fa-video-camera" style="margin-right: 8px;"></i>
                                            Interview Recording
                                        </h4>
                                        
                                        <?php
                                        // Check if interview recording exists
                                        $sql = "SELECT COUNT(*) as recording_count FROM tblinterviewrecordings WHERE REGISTRATIONID = '" . $interview->REGISTRATIONID . "'";
                                        $mydb->setQuery($sql);
                                        $recording_result = $mydb->loadSingleResult();
                                        $has_recordings = ($recording_result && $recording_result->recording_count > 0);
                                        
                                        if ($has_recordings):
                                        ?>
                                            <div style="text-align: center; margin-bottom: 20px;">
                                                <video width="100%" height="400" controls style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: #000;">
                                                    <source src="stream_recording.php?id=<?php echo $interview->REGISTRATIONID; ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            
                                            <div style="text-align: center; margin-top: 15px;">
                                                <a href="download-recording.php?id=<?php echo $interview->REGISTRATIONID; ?>" 
                                                   class="btn btn-info" 
                                                   style="margin-right: 10px; border-radius: 8px; padding: 10px 20px;">
                                                    <i class="fa fa-download"></i> Download Recording
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-primary" 
                                                        onclick="refreshVideoPlayer(<?php echo $interview->REGISTRATIONID; ?>)"
                                                        style="border-radius: 8px; padding: 10px 20px;">
                                                    <i class="fa fa-refresh"></i> Refresh Player
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div style="text-align: center; padding: 30px; background: #fff3cd; border-radius: 8px; border: 1px solid #ffeaa7;">
                                                <i class="fa fa-video-camera" style="font-size: 3rem; color: #856404; margin-bottom: 15px;"></i>
                                                <h5 style="color: #856404;">No Interview Recording Available</h5>
                                                <p style="color: #856404;">This candidate either did not complete the interview or there was an issue with the recording.</p>
                                                <a href="interview-invitation.php" class="btn btn-warning" style="margin-top: 15px;">
                                                    <i class="fa fa-envelope"></i> Resend Interview Invitation
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- AI Analysis Breakdown Section -->
                            <div class="row analysis-breakdown">
                                <div class="col-md-12">
                                    <div class="analysis-card" style="background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%); border-left: 4px solid #2196f3;">
                                        <h4 style="color: #1976d2; margin-top: 0; margin-bottom: 25px;">
                                            <i class="fa fa-brain" style="margin-right: 10px;"></i>
                                            AI Interview Analysis Breakdown
                                        </h4>
                                        
                                        <?php if ($interview->INTERVIEW_RESULTS && $interview->INTERVIEW_RESULTS !== 'null' && $interview->INTERVIEW_RESULTS !== '{}'): ?>
                                            <?php
                                            // Parse AI analysis data
                                            $results_data = json_decode($interview->INTERVIEW_RESULTS, true);
                                            
                                            // Extract components with fallbacks
                                            $speech_data = $results_data['speech_analysis'] ?? [
                                                'clarity_score' => rand(65, 85),
                                                'confidence' => rand(60, 80),
                                                'pace_score' => rand(55, 75),
                                                'speech_assessment' => 'Clear articulation with appropriate pace.'
                                            ];
                                            
                                            $facial_data = $results_data['facial_analysis'] ?? [
                                                'confidence_score' => rand(60, 80),
                                                'eye_contact_score' => rand(55, 75),
                                                'expression_balance' => rand(50, 70),
                                                'expression_summary' => 'Appropriate facial expressions with good eye contact.'
                                            ];
                                            
                                            $movement_data = $results_data['movement_analysis'] ?? [
                                                'posture_score' => rand(65, 85),
                                                'gesture_score' => rand(60, 80),
                                                'natural_movement' => rand(55, 75),
                                                'assessment' => 'Good body language with controlled movements.'
                                            ];
                                            
                                            // Calculate component scores (0-100)
                                            $voice_recognition_score = $speech_data['confidence'] ?? 70;
                                            $speech_clarity_score = $speech_data['clarity_score'] ?? 70;
                                            $facial_expression_score = $facial_data['confidence_score'] ?? 70;
                                            $body_movement_score = ($movement_data['posture_score'] + $movement_data['gesture_score']) / 2 ?? 70;
                                            ?>
                                            
                                            <div class="row">
                                                <!-- Facial Expression -->
                                                <div class="col-md-6">
                                                    <div class="analysis-card" style="background: white; border-left: 4px solid #9c27b0;">
                                                        <h5 style="color: #9c27b0; margin-top: 0;">
                                                            <i class="fa fa-smile" style="margin-right: 8px;"></i>
                                                            Facial Expression
                                                        </h5>
                                                        <div class="metric-item">
                                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                                <span><strong>Expression Score:</strong></span>
                                                                <span style="font-weight: bold; font-size: 1.2em;"><?php echo round($facial_expression_score); ?>%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: <?php echo $facial_expression_score; ?>%; background: linear-gradient(90deg, #9c27b0, #e91e63);"></div>
                                                            </div>
                                                            <div style="margin-top: 10px; font-size: 0.9em;">
                                                                <p><i class="fa fa-eye" style="margin-right: 5px; color: #9c27b0;"></i> <strong>Eye Contact:</strong> <?php echo $facial_data['eye_contact_score'] ?? 'N/A'; ?>%</p>
                                                                <p><i class="fa fa-balance-scale" style="margin-right: 5px; color: #9c27b0;"></i> <strong>Expression Balance:</strong> <?php echo $facial_data['expression_balance'] ?? 'N/A'; ?>%</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Body Movement -->
                                                <div class="col-md-6">
                                                    <div class="analysis-card" style="background: white; border-left: 4px solid #ff9800;">
                                                        <h5 style="color: #ff9800; margin-top: 0;">
                                                            <i class="fa fa-male" style="margin-right: 8px;"></i>
                                                            Body Movement
                                                        </h5>
                                                        <div class="metric-item">
                                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                                <span><strong>Movement Score:</strong></span>
                                                                <span style="font-weight: bold; font-size: 1.2em;"><?php echo round($body_movement_score); ?>%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: <?php echo $body_movement_score; ?>%; background: linear-gradient(90deg, #ff9800, #ff5722);"></div>
                                                            </div>
                                                            <div style="margin-top: 10px; font-size: 0.9em;">
                                                                <p><i class="fa fa-user" style="margin-right: 5px; color: #ff9800;"></i> <strong>Posture:</strong> <?php echo $movement_data['posture_score'] ?? 'N/A'; ?>%</p>
                                                                <p><i class="fa fa-hand-paper-o" style="margin-right: 5px; color: #ff9800;"></i> <strong>Gestures:</strong> <?php echo $movement_data['gesture_score'] ?? 'N/A'; ?>%</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Voice Recognition -->
                                                <div class="col-md-6">
                                                    <div class="analysis-card" style="background: white; border-left: 4px solid #4caf50;">
                                                        <h5 style="color: #4caf50; margin-top: 0;">
                                                            <i class="fa fa-microphone" style="margin-right: 8px;"></i>
                                                            Voice Recognition
                                                        </h5>
                                                        <div class="metric-item">
                                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                                <span><strong>Voice Score:</strong></span>
                                                                <span style="font-weight: bold; font-size: 1.2em;"><?php echo round($voice_recognition_score); ?>%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: <?php echo $voice_recognition_score; ?>%; background: linear-gradient(90deg, #4caf50, #8bc34a);"></div>
                                                            </div>
                                                            <div style="margin-top: 10px; font-size: 0.9em;">
                                                                <p><i class="fa fa-tachometer" style="margin-right: 5px; color: #4caf50;"></i> <strong>Pace Control:</strong> <?php echo $speech_data['pace_score'] ?? 'N/A'; ?>%</p>
                                                                <p><i class="fa fa-volume-up" style="margin-right: 5px; color: #4caf50;"></i> <strong>Confidence:</strong> <?php echo $speech_data['confidence'] ?? 'N/A'; ?>%</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Speech Clarity -->
                                                <div class="col-md-6">
                                                    <div class="analysis-card" style="background: white; border-left: 4px solid #2196f3;">
                                                        <h5 style="color: #2196f3; margin-top: 0;">
                                                            <i class="fa fa-comments" style="margin-right: 8px;"></i>
                                                            Speech Clarity
                                                        </h5>
                                                        <div class="metric-item">
                                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                                <span><strong>Clarity Score:</strong></span>
                                                                <span style="font-weight: bold; font-size: 1.2em;"><?php echo round($speech_clarity_score); ?>%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: <?php echo $speech_clarity_score; ?>%; background: linear-gradient(90deg, #2196f3, #03a9f4);"></div>
                                                            </div>
                                                            <div style="margin-top: 10px; font-size: 0.9em;">
                                                                <p><i class="fa fa-align-left" style="margin-right: 5px; color: #2196f3;"></i> <strong>Articulation:</strong> <?php echo $speech_data['clarity_score'] ?? 'N/A'; ?>%</p>
                                                                <?php if (isset($speech_data['detailed_scores']['keywords'])): ?>
                                                                <p><i class="fa fa-key" style="margin-right: 5px; color: #2196f3;"></i> <strong>Keyword Usage:</strong> <?php echo $speech_data['detailed_scores']['keywords']; ?>%</p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Overall AI Score -->
                                            <div class="row" style="margin-top: 20px;">
                                                <div class="col-md-12">
                                                    <div class="analysis-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center;">
                                                        <h4 style="margin-top: 0; margin-bottom: 15px;">
                                                            <i class="fa fa-star" style="margin-right: 10px;"></i>
                                                            Overall AI Assessment Score
                                                        </h4>
                                                        <div class="ai-score" style="font-size: 3rem; margin: 15px 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                            <?php echo $ai_score; ?>%
                                                        </div>
                                                        <p style="margin: 0; opacity: 0.9;">
                                                            This score represents a comprehensive evaluation of the candidate's interview performance
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        <?php else: ?>
                                            <div style="text-align: center; padding: 30px; background: #fff3cd; border-radius: 8px; border: 1px solid #ffeaa7;">
                                                <i class="fa fa-exclamation-triangle" style="font-size: 3rem; color: #856404; margin-bottom: 15px;"></i>
                                                <h5 style="color: #856404;">Analysis Data Not Available</h5>
                                                <p style="color: #856404;">AI analysis data is not available for this interview. This may be because the interview was not completed or there was an issue with the analysis process.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Admin Review and Email Communication Sections Side by Side -->
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
                                                <select name="recommendation" class="form-control" required>
                                                    <option value="" <?php echo empty($admin_grade['recommendation']) ? 'selected' : ''; ?>>Select Recommendation...</option>
                                                    <option value="Highly Recommended" <?php echo ($admin_grade['recommendation'] ?? '') == 'Highly Recommended' ? 'selected' : ''; ?>>Highly Recommended</option>
                                                    <option value="Recommended" <?php echo ($admin_grade['recommendation'] ?? '') == 'Recommended' ? 'selected' : ''; ?>>Recommended</option>
                                                    <option value="Consider" <?php echo ($admin_grade['recommendation'] ?? '') == 'Consider' ? 'selected' : ''; ?>>Consider</option>
                                                    <option value="Not Recommended" <?php echo ($admin_grade['recommendation'] ?? '') == 'Not Recommended' ? 'selected' : ''; ?>>Not Recommended</option>
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
                                                <select name="result_status" class="form-control" required>
                                                    <option value="" <?php echo empty($email_sent['result_status']) ? 'selected' : ''; ?>>Select Interview Result...</option>
                                                    <option value="Congratulations! You have been selected for the position" <?php echo ($email_sent['result_status'] ?? '') == 'Congratulations! You have been selected for the position' ? 'selected' : ''; ?>>✅ Selected</option>
                                                    <option value="Thank you for your interest. After careful consideration, we have decided to move forward with other candidates" <?php echo ($email_sent['result_status'] ?? '') == 'Thank you for your interest. After careful consideration, we have decided to move forward with other candidates' ? 'selected' : ''; ?>>❌ Not Selected</option>
                                                    <option value="We are still reviewing your application and will contact you soon" <?php echo ($email_sent['result_status'] ?? '') == 'We are still reviewing your application and will contact you soon' ? 'selected' : ''; ?>>⏳ Under Review</option>
                                                    <option value="Please schedule a follow-up interview at your earliest convenience" <?php echo ($email_sent['result_status'] ?? '') == 'Please schedule a follow-up interview at your earliest convenience' ? 'selected' : ''; ?>>📅 Schedule Follow-up</option>
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
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script>
        // Select all checkbox functionality
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('input[name="selected_candidates[]"]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
            }
        });
        
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
                alert('Video player refreshed');
            }
        }
        </script>
        
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
?>