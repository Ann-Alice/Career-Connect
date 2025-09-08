<?php
/**
 * Test Interview Results Page
 * Simple test to verify the interview results page is accessible
 */

require_once('../include/initialize.php');

// Check if user is admin
if (!isset($_SESSION['ADMIN_USERID'])) {
    // Redirect to login
    header("Location: login.php");
    exit;
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Interview Results</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        h2 { color: #333; }
        .btn { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
        .btn:hover { background: #005a87; }
    </style>
</head>
<body>
<div class='container'>
<h2>Test Interview Results Page</h2>";

// Test database connection
global $mydb;

if ($mydb) {
    echo "<p class='success'>✅ Database connection successful</p>";
    
    // Test if required tables exist
    $tables = ['tbljobregistration', 'tblinterviewrecordings', 'tblinterviewinvitations'];
    foreach ($tables as $table) {
        $sql = "SHOW TABLES LIKE '{$table}'";
        $mydb->setQuery($sql);
        $result = $mydb->loadResultList();
        
        if (!empty($result)) {
            echo "<p class='success'>✅ Table '{$table}' exists</p>";
        } else {
            echo "<p class='error'>❌ Table '{$table}' does not exist</p>";
        }
    }
    
    // Test if required columns exist in tbljobregistration
    $required_columns = ['INTERVIEW_RESULTS', 'INTERVIEW_STATUS', 'ADMIN_GRADE', 'EMAIL_SENT'];
    foreach ($required_columns as $column) {
        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'erisdb' 
                AND TABLE_NAME = 'tbljobregistration' 
                AND COLUMN_NAME = '{$column}'";
        $mydb->setQuery($sql);
        $result = $mydb->loadResultList();
        
        if (!empty($result)) {
            echo "<p class='success'>✅ Column '{$column}' exists in tbljobregistration</p>";
        } else {
            echo "<p class='error'>❌ Column '{$column}' does not exist in tbljobregistration</p>";
        }
    }
} else {
    echo "<p class='error'>❌ Database connection failed</p>";
}

echo "<h3>Next Steps:</h3>
<ul>
    <li><a href='init-interview-db.php' class='btn'>Initialize Database Schema</a> (if columns are missing)</li>
    <li><a href='interview-results.php' class='btn'>View Interview Results</a> (if everything is set up)</li>
    <li><a href='interview-invitation.php' class='btn'>Send Interview Invitations</a> (to create interview data)</li>
</ul>";

echo "</div></body></html>";
?>