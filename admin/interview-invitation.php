<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/email_functions.php');

if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect_to(web_root . "admin/login.php");
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'approve':
        doApproveApplication();
        break;
    case 'reject':
        doRejectApplication();
        break;
    case 'send':
        doSendInvitation();
        break;
    default:
        $title = "Application Management";
        $content = 'interview-invitation-content.php';
        include('theme/templates.php');
        break;
}

function doApproveApplication() {
    global $mydb;
    
    if (!isset($_GET['id'])) {
        redirect_to("interview-invitation.php");
    }
    
    $registration_id = $_GET['id'];
    
    // Update application status
    $sql = "UPDATE tbljobregistration SET REMARKS = 'Approved' WHERE REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Get application details for messaging
    $sql = "SELECT r.*, a.EMAILADDRESS, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, a.APPLICANTID 
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $application = $mydb->loadSingleResult();
    
    // Try to send message
    $message = "Your application for {$application->OCCUPATIONTITLE} has been approved! You will receive an interview invitation soon.";
    sendMessageToCandidate($application->APPLICANTID, $_SESSION['ADMIN_USERID'], 'Application Approved', $message);
    
    message("Application approved successfully!", "success");
    redirect_to("interview-invitation.php");
}

function doRejectApplication() {
    global $mydb;
    
    if (!isset($_GET['id'])) {
        redirect_to("interview-invitation.php");
    }
    
    $registration_id = $_GET['id'];
    
    // Update application status
    $sql = "UPDATE tbljobregistration SET REMARKS = 'Rejected' WHERE REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Get application details for messaging
    $sql = "SELECT r.*, a.EMAILADDRESS, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, a.APPLICANTID
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $application = $mydb->loadSingleResult();
    
    // Try to send message
    $message = "We regret to inform you that your application for {$application->OCCUPATIONTITLE} has not been successful at this time.";
    sendMessageToCandidate($application->APPLICANTID, $_SESSION['ADMIN_USERID'], 'Application Status Update', $message);
    
    message("Application rejected successfully!", "success");
    redirect_to("interview-invitation.php");
}

function doSendInvitation() {
    global $mydb;
    
    if (!isset($_GET['id'])) {
        redirect_to("interview-invitation.php");
    }
    
    $registration_id = $_GET['id'];
    
    // Get application details
    $sql = "SELECT r.*, a.EMAILADDRESS, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, a.APPLICANTID
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $application = $mydb->loadSingleResult();
    
    if (!$application) {
        message("Application not found.", "error");
        redirect_to("interview-invitation.php");
    }
    
    // Generate unique token
    $token = bin2hex(random_bytes(32));
    $expiry_date = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    // Create invitation record
    $sql = "INSERT INTO tblinterviewinvitations 
            (REGISTRATIONID, APPLICANTID, JOBID, EXPIRY_DATE, TOKEN) 
            VALUES 
            ('{$registration_id}', '{$application->APPLICANTID}', 
             '{$application->JOBID}', '{$expiry_date}', '{$token}')";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Prepare email using professional template
    $to = $application->EMAILADDRESS;
    $candidateName = $application->FNAME . ' ' . $application->LNAME;
    $positionTitle = $application->OCCUPATIONTITLE;
    
    // Construct fully qualified URL for the interview
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $interview_url = $protocol . $host . web_root . "interview.php?token=" . $token;
    
    // Get company name
    $sql = "SELECT COMPANYNAME FROM tblcompany c 
            JOIN tbljob j ON c.COMPANYID = j.COMPANYID 
            WHERE j.JOBID = '{$application->JOBID}'";
    $mydb->setQuery($sql);
    $company = $mydb->loadSingleResult();
    $companyName = $company ? $company->COMPANYNAME : 'Our Company';
    
    // Send professional email template
    if (sendInterviewInvitationEmail($to, $candidateName, $positionTitle, $interview_url, $expiry_date, $companyName)) {
        // Try to send message
        $inbox_message = "You have been invited to complete an AI interview for {$application->OCCUPATIONTITLE}. Click here to start: {$interview_url}";
        sendMessageToCandidate($application->APPLICANTID, $_SESSION['ADMIN_USERID'], 'AI Interview Invitation', $inbox_message);
        
        message("Interview invitation sent successfully!", "success");
    } else {
        message("Error sending invitation email.", "error");
    }
    
    redirect_to("interview-invitation.php");
}
?>