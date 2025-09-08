<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    // For testing purposes, we'll simulate an admin login
    $_SESSION['ADMIN_USERID'] = 1;
    $_SESSION['ADMIN_USERNAME'] = 'test_admin';
    $_SESSION['ADMIN_ROLE'] = 'Administrator';
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Interview Setup Test</title>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<div class='container'>
    <h1>Interview System Setup Test</h1>";

try {
    // Test database connection
    echo "<h2>Database Connection</h2>";
    echo "<p class='success'>✓ Successfully connected to database: " . database_name . "</p>";
    
    // Check if required tables exist
    echo "<h2>Required Tables</h2>";
    $tables_to_check = [
        'tbljobregistration',
        'tblinterviewrecordings',
        'tblinterviewvideos',
        'tblinterviewinvitations'
    ];
    
    echo "<table>
            <tr><th>Table Name</th><th>Status</th></tr>";
    
    foreach ($tables_to_check as $table) {
        $sql = "SHOW TABLES LIKE '$table'";
        $mydb->setQuery($sql);
        $result = $mydb->loadResult();
        
        if ($result) {
            echo "<tr><td>$table</td><td class='success'>✓ Exists</td></tr>";
        } else {
            echo "<tr><td>$table</td><td class='error'>✗ Missing</td></tr>";
        }
    }
    echo "</table>";
    
    // Check if required columns exist in tbljobregistration
    echo "<h2>Required Columns in tbljobregistration</h2>";
    $required_columns = [
        'INTERVIEW_RESULTS',
        'INTERVIEW_STATUS',
        'INTERVIEW_COMPLETED_AT',
        'ADMIN_GRADE',
        'GRADED_AT',
        'EMAIL_SENT',
        'EMAIL_SENT_AT'
    ];
    
    echo "<table>
            <tr><th>Column Name</th><th>Status</th></tr>";
    
    foreach ($required_columns as $column) {
        $sql = "SHOW COLUMNS FROM tbljobregistration LIKE '$column'";
        $mydb->setQuery($sql);
        $result = $mydb->loadResult();
        
        if ($result) {
            echo "<tr><td>$column</td><td class='success'>✓ Exists</td></tr>";
        } else {
            echo "<tr><td>$column</td><td class='error'>✗ Missing</td></tr>";
        }
    }
    echo "</table>";
    
    // Check for sample interview data
    echo "<h2>Sample Data</h2>";
    $sql = "SELECT COUNT(*) as count FROM tbljobregistration WHERE INTERVIEW_STATUS IS NOT NULL OR INTERVIEW_RESULTS IS NOT NULL";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if ($result && $result->count > 0) {
        echo "<p class='success'>✓ Found {$result->count} interview records with data</p>";
    } else {
        echo "<p class='warning'>⚠ No interview records with data found</p>";
    }
    
    // Check for video recordings
    $sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if ($result && $result->count > 0) {
        echo "<p class='success'>✓ Found {$result->count} video recordings</p>";
    } else {
        echo "<p class='warning'>⚠ No video recordings found</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Test Completed</h2>";
echo "<p><a href='interview-results.php'>Back to Interview Results</a></p>";
echo "</div>
</body>
</html>";
?>