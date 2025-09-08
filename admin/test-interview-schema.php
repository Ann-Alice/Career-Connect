<?php
/**
 * Test Interview Schema
 * Verifies that all required columns exist in the database
 */

require_once('../include/initialize.php');

// Check if user is admin
if (!isset($_SESSION['ADMIN_USERID'])) {
    die('Access denied. Admin login required.');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Interview Schema</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        h2 { color: #333; }
        ul { list-style-type: none; padding: 0; }
        li { padding: 5px 0; }
    </style>
</head>
<body>
<div class='container'>
<h2>Interview Schema Test</h2>";

global $mydb;

// Required columns
$required_columns = [
    'INTERVIEW_RESULTS',
    'INTERVIEW_STATUS',
    'INTERVIEW_COMPLETED_AT',
    'ADMIN_GRADE',
    'GRADED_AT',
    'EMAIL_SENT',
    'EMAIL_SENT_AT'
];

echo "<h3>Checking Required Columns in tbljobregistration:</h3><ul>";

$missing_columns = [];
$existing_columns = [];

foreach ($required_columns as $column_name) {
    // Check if column exists
    $check_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE TABLE_SCHEMA = 'erisdb' 
                  AND TABLE_NAME = 'tbljobregistration' 
                  AND COLUMN_NAME = '{$column_name}'";
    
    $mydb->setQuery($check_sql);
    $result = $mydb->loadResultList();
    
    if (!empty($result)) {
        echo "<li class='success'>✅ Column exists: {$column_name}</li>";
        $existing_columns[] = $column_name;
    } else {
        echo "<li class='error'>❌ Missing column: {$column_name}</li>";
        $missing_columns[] = $column_name;
    }
}

echo "</ul>";

echo "<h3>Summary:</h3>";
echo "<ul>";
echo "<li class='success'>✅ Existing columns: " . count($existing_columns) . "</li>";
if (!empty($missing_columns)) {
    echo "<li class='error'>❌ Missing columns: " . count($missing_columns) . "</li>";
    echo "<li class='info'>🔧 Run <a href='update-interview-schema.php'>update-interview-schema.php</a> to add missing columns</li>";
} else {
    echo "<li class='success'>🎉 All required columns are present!</li>";
}
echo "</ul>";

echo "</div></body></html>";
?>