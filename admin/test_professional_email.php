<?php
require_once('../include/config.php');
require_once('../include/session.php');
require_once('../include/functions.php');
require_once('../include/email_functions.php');

// Check if user is admin
confirm_admin();

// Test the new professional email templates
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Email Template Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">📧 Professional Email Template System</h1>
        
        <div class="alert alert-info">
            <h4>Professional Email Templates</h4>
            <p>This system provides consistent, branded email templates for all Eris AI Interview System communications.</p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Interview Invitation Template</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Generate interview invitation email
                        $invitationEmail = generateInterviewInvitationEmail(
                            "John Doe",
                            "Senior Software Engineer",
                            "https://eris.example.com/interview.php?token=abc123",
                            "2025-10-15",
                            "TechCorp"
                        );
                        echo $invitationEmail;
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Interview Result Template</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Generate interview result email
                        $resultEmail = generateInterviewResultEmail(
                            "Jane Smith",
                            "Marketing Manager",
                            "🎉 Congratulations! You have been selected for the position",
                            "We were impressed with your skills and experience during the interview process. We would like to offer you the Marketing Manager position at our company.",
                            "Innovate Inc."
                        );
                        echo $resultEmail;
                        ?>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Generic Notification Template</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Generate notification email
                        $notificationEmail = generateNotificationEmail(
                            "Alex Johnson",
                            "Application Status Update",
                            "Thank you for your interest in our company. We wanted to inform you that your application is currently under review and we will contact you soon with further updates.",
                            "Global Solutions Ltd."
                        );
                        echo $notificationEmail;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-success">
            <h4>Implementation Notes</h4>
            <p>All emails sent through the Eris AI Interview System now use these professional templates:</p>
            <ul>
                <li>Consistent branding and styling across all emails</li>
                <li>Responsive design that works on all devices</li>
                <li>Professional color scheme and typography</li>
                <li>Clear visual hierarchy and spacing</li>
                <li>Company branding in the footer</li>
                <li>System identification note</li>
            </ul>
            <p>Functions available:</p>
            <ul>
                <li><code>sendInterviewInvitationEmail()</code> - For interview invitations</li>
                <li><code>sendInterviewResultEmail()</code> - For interview results</li>
                <li><code>sendNotificationEmail()</code> - For general notifications</li>
                <li><code>generateEmailTemplate()</code> - Base template for custom emails</li>
            </ul>
        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Back to Admin Dashboard</a>
        </div>
    </div>
</body>
</html>