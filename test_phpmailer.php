<?php
echo "<h1>Testing PHPMailer Installation</h1>";

// Include the Composer autoloader
require_once 'vendor/autoload.php';

try {
    // Try to create a PHPMailer instance
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    
    echo "<p style='color: green;'>✅ PHPMailer class loaded successfully!</p>";
    echo "<p>PHPMailer version: " . $mail::VERSION . "</p>";
    
    echo "<h2>Next Steps</h2>";
    echo "<p>You can now send interview invitations without PHPMailer errors.</p>";
    echo "<p><a href='admin/interview-invitation.php'>Go to Interview Invitation Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>