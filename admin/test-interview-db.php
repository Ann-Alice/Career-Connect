<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    echo "Not logged in as admin";
    exit;
}

echo "<h1>Interview Database Test</h1>";

// Test connection to database
echo "<h2>Database Connection Test</h2>";
echo "<p>Connected to database: " . DB_NAME . "</p>";

// Check if required tables exist
$tables_to_check = [
    'tbljobregistration',
    'tblinterviewrecordings',
    'tblinterviewvideos',
    'tblinterviewinvitations'
];

foreach ($tables_to_check as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $mydb->setQuery($sql);
    $result = $mydb->loadResult();
    
    if ($result) {
        echo "<p style='color: green;'>✓ Table $table exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Table $table does not exist</p>";
    }
}

// Check if required columns exist in tbljobregistration
$required_columns = [
    'INTERVIEW_RESULTS',
    'INTERVIEW_STATUS',
    'INTERVIEW_COMPLETED_AT',
    'ADMIN_GRADE',
    'GRADED_AT',
    'EMAIL_SENT',
    'EMAIL_SENT_AT'
];

echo "<h2>tbljobregistration Column Check</h2>";
foreach ($required_columns as $column) {
    $sql = "SHOW COLUMNS FROM tbljobregistration LIKE '$column'";
    $mydb->setQuery($sql);
    $result = $mydb->loadResult();
    
    if ($result) {
        echo "<p style='color: green;'>✓ Column $column exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Column $column does not exist</p>";
    }
}

// Check for sample interview data
echo "<h2>Sample Interview Data</h2>";
$sql = "SELECT COUNT(*) as count FROM tbljobregistration WHERE INTERVIEW_STATUS IS NOT NULL OR INTERVIEW_RESULTS IS NOT NULL";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result && $result->count > 0) {
    echo "<p style='color: green;'>✓ Found {$result->count} interview records with data</p>";
} else {
    echo "<p style='color: orange;'>⚠ No interview records with data found</p>";
}

// Check for video recordings
echo "<h2>Video Recording Data</h2>";
$sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result && $result->count > 0) {
    echo "<p style='color: green;'>✓ Found {$result->count} video recordings</p>";
} else {
    echo "<p style='color: orange;'>⚠ No video recordings found</p>";
}

echo "<h2>Test Completed</h2>";
echo "<p><a href='interview-results.php'>Back to Interview Results</a></p>";
?>