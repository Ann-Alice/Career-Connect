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

echo "<h1>Table Structure Check</h1>";

// Check database connection
global $mydb;

// Get table structure
echo "<h2>tbljobregistration Table Structure</h2>";
$sql = "DESCRIBE tbljobregistration";
$mydb->setQuery($sql);
$columns = $mydb->loadResultList();

if ($columns) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column->Field . "</td>";
        echo "<td>" . $column->Type . "</td>";
        echo "<td>" . $column->Null . "</td>";
        echo "<td>" . $column->Key . "</td>";
        echo "<td>" . $column->Default . "</td>";
        echo "<td>" . $column->Extra . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Failed to get table structure<br>";
}

// Check for specific columns we're interested in
echo "<h2>Specific Column Check</h2>";
$required_columns = ['INTERVIEW_STATUS', 'INTERVIEW_RESULTS', 'ADMIN_GRADE', 'EMAIL_SENT'];
foreach ($required_columns as $column_name) {
    $sql = "SHOW COLUMNS FROM tbljobregistration LIKE '$column_name'";
    $mydb->setQuery($sql);
    $result = $mydb->loadResultList();
    if (!empty($result)) {
        echo "✅ Column '$column_name' exists<br>";
    } else {
        echo "❌ Column '$column_name' does NOT exist<br>";
    }
}

// Check for similar columns that might exist instead
echo "<h2>Similar Column Names</h2>";
$sql = "SHOW COLUMNS FROM tbljobregistration";
$mydb->setQuery($sql);
$all_columns = $mydb->loadResultList();
if ($all_columns) {
    foreach ($all_columns as $column) {
        if (stripos($column->Field, 'interview') !== false || stripos($column->Field, 'status') !== false) {
            echo "Found similar column: " . $column->Field . " (" . $column->Type . ")<br>";
        }
    }
}

?>