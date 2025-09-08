<?php
require_once('../include/config.php');
require_once('../include/session.php');
require_once('../include/functions.php');
require_once('../include/email_functions.php');

// Check if user is admin
confirm_admin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Fix Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">📧 Email Fix Test</h1>
        
        <div class="alert alert-info">
            <h4>Testing Email Duplication Fix</h4>
            <p>This page tests the fix for email duplication issues.</p>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Test Case: Message with Candidate Name and Status</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Test the fix with the example from the issue
                        $testEmail = generateInterviewResultEmail(
                            "Kim Domingo",
                            "Accounting",
                            "Congratulations! You have been selected for the position",
                            "Dear Kim Domingo,\n\nWe are still reviewing your application and will contact you soon",
                            "URC"
                        );
                        echo $testEmail;
                        ?>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Test Case: Message without Candidate Name</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Test with message that doesn't contain candidate name
                        $testEmail2 = generateInterviewResultEmail(
                            "Jane Smith",
                            "Marketing Manager",
                            "We are still reviewing your application and will contact you soon",
                            "Thank you for your patience during our review process. We expect to have a decision within the next week.",
                            "Innovate Inc."
                        );
                        echo $testEmail2;
                        ?>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Test Case: Message Matching Status</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Test with message that contains the status
                        $testEmail3 = generateInterviewResultEmail(
                            "John Doe",
                            "Software Engineer",
                            "Congratulations! You have been selected for the position",
                            "Congratulations! You have been selected for the position\n\nWe're excited to offer you the Software Engineer position.",
                            "TechCorp"
                        );
                        echo $testEmail3;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-success">
            <h4>Fix Implementation</h4>
            <p>The fix addresses email duplication by:</p>
            <ul>
                <li>Removing duplicated candidate name from the message content</li>
                <li>Preventing the result status from appearing twice when it's already in the message</li>
                <li>Ensuring clean, professional email formatting</li>
            </ul>
        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Back to Admin Dashboard</a>
        </div>
    </div>
</body>
</html>