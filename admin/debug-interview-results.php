<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');

if (!isset($_SESSION['ADMIN_USERID'])) {
    echo "User not logged in<br>";
    exit;
}

echo "<h1>Debug Interview Results</h1>";

// Check database connection
global $mydb;
echo "<h2>Database Connection Test</h2>";
echo "Database host: " . DB_SERVER . "<br>";
echo "Database name: " . DB_NAME . "<br>";

// Check if tbljobregistration table exists
echo "<h2>Table Existence Check</h2>";
$sql = "SHOW TABLES LIKE 'tbljobregistration'";
$mydb->setQuery($sql);
$tables = $mydb->loadResultList();
if (!empty($tables)) {
    echo "✅ tbljobregistration table exists<br>";
} else {
    echo "❌ tbljobregistration table does NOT exist<br>";
}

// Check if required columns exist
echo "<h2>Column Check</h2>";
$sql = "SHOW COLUMNS FROM tbljobregistration LIKE 'INTERVIEW_RESULTS'";
$mydb->setQuery($sql);
$columns = $mydb->loadResultList();
if (!empty($columns)) {
    echo "✅ INTERVIEW_RESULTS column exists<br>";
} else {
    echo "❌ INTERVIEW_RESULTS column does NOT exist<br>";
}

$sql = "SHOW COLUMNS FROM tbljobregistration LIKE 'INTERVIEW_STATUS'";
$mydb->setQuery($sql);
$columns = $mydb->loadResultList();
if (!empty($columns)) {
    echo "✅ INTERVIEW_STATUS column exists<br>";
} else {
    echo "❌ INTERVIEW_STATUS column does NOT exist<br>";
}

// Check for any interview records
echo "<h2>Interview Records Check</h2>";
$sql = "SELECT COUNT(*) as total FROM tbljobregistration";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();
echo "Total registration records: " . ($result ? $result->total : '0') . "<br>";

// Check for completed interviews
$sql = "SELECT COUNT(*) as completed FROM tbljobregistration WHERE INTERVIEW_STATUS = 'Completed' OR INTERVIEW_STATUS = 'completed'";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();
echo "Completed interview records: " . ($result ? $result->completed : '0') . "<br>";

// Check for interviews with results
$sql = "SELECT COUNT(*) as with_results FROM tbljobregistration WHERE (INTERVIEW_STATUS = 'Completed' OR INTERVIEW_STATUS = 'completed') AND INTERVIEW_RESULTS IS NOT NULL AND INTERVIEW_RESULTS != '' AND INTERVIEW_RESULTS != 'null'";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();
echo "Interviews with results: " . ($result ? $result->with_results : '0') . "<br>";

// Show sample data if any
if ($result && $result->with_results > 0) {
    echo "<h2>Sample Interview Data</h2>";
    $sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS, INTERVIEW_RESULTS FROM tbljobregistration WHERE (INTERVIEW_STATUS = 'Completed' OR INTERVIEW_STATUS = 'completed') AND INTERVIEW_RESULTS IS NOT NULL AND INTERVIEW_RESULTS != '' AND INTERVIEW_RESULTS != 'null' LIMIT 5";
    $mydb->setQuery($sql);
    $interviews = $mydb->loadResultList();
    
    if ($interviews) {
        foreach ($interviews as $interview) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
            echo "Registration ID: " . $interview->REGISTRATIONID . "<br>";
            echo "Status: " . $interview->INTERVIEW_STATUS . "<br>";
            echo "Results: " . substr($interview->INTERVIEW_RESULTS, 0, 100) . "...<br>";
            echo "</div>";
        }
    }
} else {
    echo "<h2>No Interview Data Found</h2>";
    echo "<p>There are no completed interviews with results in the database.</p>";
    
    // Check for any interviews at all
    $sql = "SELECT COUNT(*) as total FROM tbljobregistration";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    if ($result && $result->total > 0) {
        echo "<p>However, there are " . $result->total . " total registration records. Let's check their status:</p>";
        
        $sql = "SELECT DISTINCT INTERVIEW_STATUS, COUNT(*) as count FROM tbljobregistration GROUP BY INTERVIEW_STATUS";
        $mydb->setQuery($sql);
        $statuses = $mydb->loadResultList();
        if ($statuses) {
            echo "<ul>";
            foreach ($statuses as $status) {
                echo "<li>" . ($status->INTERVIEW_STATUS ?: 'NULL') . ": " . $status->count . " records</li>";
            }
            echo "</ul>";
        }
    }
}

echo "<h2>Session Data</h2>";
echo "ADMIN_USERID: " . (isset($_SESSION['ADMIN_USERID']) ? $_SESSION['ADMIN_USERID'] : 'Not set') . "<br>";
echo "All session data:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

?>