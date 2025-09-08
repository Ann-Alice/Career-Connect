<?php
require_once('../include/initialize.php');
require_once('../include/email_functions.php');

echo "<h1>Email Functionality Test - Interview Results</h1>";

try {
    echo "<h2>1. Email Configuration Check</h2>";
    
    // Check if all required constants are defined
    $email_constants = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASS', 'SMTP_ENCRYPTION', 'ADMIN_EMAIL', 'ADMIN_NAME'];
    
    foreach ($email_constants as $const) {
        if (defined($const)) {
            echo "<p style='color: green;'>✅ {$const}: " . (in_array($const, ['SMTP_PASS']) ? '***HIDDEN***' : constant($const)) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ {$const} is not defined</p>";
        }
    }
    
    echo "<h2>2. PHPMailer Library Check</h2>";
    
    // Check if PHPMailer is available
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        echo "<p style='color: green;'>✅ PHPMailer library is loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ PHPMailer library not found</p>";
        echo "<p><strong>Solution:</strong> Run 'composer install' in the project root</p>";
    }
    
    echo "<h2>3. Email Function Test</h2>";
    
    // Test the sendEmail function with a test email
    if (function_exists('sendEmail')) {
        echo "<p style='color: green;'>✅ sendEmail function is available</p>";
        
        // Get a test candidate from the database
        $sql = "SELECT a.EMAILADDRESS, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME
                FROM tbljobregistration r 
                JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                JOIN tbljob j ON r.JOBID = j.JOBID 
                JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                LIMIT 1";
        
        $mydb->setQuery($sql);
        $test_candidate = $mydb->loadSingleResult();
        
        if ($test_candidate) {
            echo "<p><strong>Test candidate found:</strong> {$test_candidate->FNAME} {$test_candidate->LNAME} ({$test_candidate->EMAILADDRESS})</p>";
            
            // Prepare test email content
            $test_subject = "TEST: Interview Results - {$test_candidate->OCCUPATIONTITLE}";
            $test_message = "
            <html>
            <head>
                <title>Test Email from ERIS</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .header { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                    .content { padding: 20px; background: white; }
                    .footer { margin-top: 30px; padding: 15px; background: #e9ecef; border-radius: 5px; font-size: 14px; }
                </style>
            </head>
            <body>
                <div class='header'>
                    <h2 style='color: #495057; margin: 0;'>🧪 TEST EMAIL - Interview Results System</h2>
                </div>
                <div class='content'>
                    <p><strong>Dear {$test_candidate->FNAME} {$test_candidate->LNAME},</strong></p>
                    
                    <div style='background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 20px 0;'>
                        <h3 style='margin: 0; color: #1976d2;'>This is a test email from the ERIS system</h3>
                    </div>
                    
                    <p>If you receive this email, it means the interview results email functionality is working correctly!</p>
                    
                    <p><strong>Test Details:</strong></p>
                    <ul>
                        <li>Position: {$test_candidate->OCCUPATIONTITLE}</li>
                        <li>Company: {$test_candidate->COMPANYNAME}</li>
                        <li>Test Date: " . date('Y-m-d H:i:s') . "</li>
                    </ul>
                    
                    <p>You can safely ignore this test email.</p>
                </div>
                <div class='footer'>
                    <p style='margin: 0;'><strong>Best regards,</strong></p>
                    <p style='margin: 5px 0 0 0;'>{$test_candidate->COMPANYNAME} HR Team</p>
                    <p style='margin: 10px 0 0 0; font-size: 12px; color: #6c757d;'>This is a test message from the ERIS Interview System.</p>
                </div>
            </body>
            </html>
            ";
            
            echo "<h3>Sending Test Email...</h3>";
            
            try {
                $result = sendEmail($test_candidate->EMAILADDRESS, $test_subject, $test_message);
                
                if ($result) {
                    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>🎉 SUCCESS! Test email sent successfully!</p>";
                    echo "<p><strong>Recipient:</strong> {$test_candidate->EMAILADDRESS}</p>";
                    echo "<p><strong>Subject:</strong> {$test_subject}</p>";
                    echo "<p style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
                    echo "<strong>✅ The email functionality is working correctly!</strong><br>";
                    echo "Candidates should now receive interview result emails when sent from the admin panel.";
                    echo "</p>";
                } else {
                    echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ FAILED! Test email could not be sent.</p>";
                    echo "<p style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
                    echo "<strong>Email sending failed.</strong> Check the error log and SMTP configuration.";
                    echo "</p>";
                }
                
            } catch (Exception $e) {
                echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ EXCEPTION! " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            
        } else {
            echo "<p style='color: orange;'>⚠️ No test candidates found in database</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ sendEmail function not found</p>";
    }
    
    echo "<h2>4. SMTP Configuration Recommendations</h2>";
    
    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3 style='color: #856404;'>📧 Current SMTP Configuration</h3>";
    
    if (defined('SMTP_HOST')) {
        echo "<p><strong>SMTP Host:</strong> " . SMTP_HOST . "</p>";
        
        if (SMTP_HOST === 'sandbox.smtp.mailtrap.io') {
            echo "<p style='color: #fd7e14;'><strong>⚠️ Using Mailtrap (Testing)</strong></p>";
            echo "<ul>";
            echo "<li>Mailtrap is for <strong>testing only</strong> - emails won't reach real recipients</li>";
            echo "<li>Check your Mailtrap inbox at <a href='https://mailtrap.io' target='_blank'>mailtrap.io</a></li>";
            echo "<li>For production, use a real SMTP provider (Gmail, SendGrid, etc.)</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: green;'>✅ Using production SMTP server</p>";
        }
    }
    
    echo "</div>";
    
    echo "<h2>5. Production SMTP Setup Guide</h2>";
    echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3 style='color: #0c5460;'>🚀 For Real Email Delivery</h3>";
    echo "<p>To send emails to actual candidates, update your <code>include/config.php</code> with:</p>";
    
    echo "<h4>Gmail SMTP Example:</h4>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
    echo "define('SMTP_HOST', 'smtp.gmail.com');\n";
    echo "define('SMTP_PORT', '587');\n";
    echo "define('SMTP_USER', 'your-email@gmail.com');\n";
    echo "define('SMTP_PASS', 'your-app-password'); // Use App Password for Gmail\n";
    echo "define('SMTP_ENCRYPTION', 'tls');\n";
    echo "define('ADMIN_EMAIL', 'your-email@gmail.com');\n";
    echo "define('ADMIN_NAME', 'Your Company HR');";
    echo "</pre>";
    
    echo "<h4>SendGrid SMTP Example:</h4>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
    echo "define('SMTP_HOST', 'smtp.sendgrid.net');\n";
    echo "define('SMTP_PORT', '587');\n";
    echo "define('SMTP_USER', 'apikey');\n";
    echo "define('SMTP_PASS', 'your-sendgrid-api-key');\n";
    echo "define('SMTP_ENCRYPTION', 'tls');";
    echo "</pre>";
    
    echo "</div>";
    
    echo "<p style='text-align: center; margin-top: 30px;'>";
    echo "<a href='interview-results.php' style='background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Test Interview Results Page</a>";
    echo "<a href='../admin/login.php' style='background: #6c757d; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>Back to Admin</a>";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
    background: #f8f9fa;
}
h1, h2, h3 {
    color: #333;
}
h1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}
pre {
    white-space: pre-wrap;
    font-family: monospace;
}
</style>