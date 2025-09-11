<?php
// Simple test of database class methods
require_once('include/initialize.php');

echo "Testing database connection...\n";

// Test simple query
$sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result) {
    echo "Simple query successful. Total recordings: " . $result->count . "\n";
} else {
    echo "Simple query failed: " . $mydb->error_msg . "\n";
    exit(1);
}

// Test prepared statement method directly
echo "Testing prepareStatement method...\n";
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = ? LIMIT 1";
$params = ['3'];
$types = 's';

$stmt = $mydb->prepareStatement($sql, $params, $types);
if ($stmt) {
    echo "prepareStatement successful\n";
    
    $result = $mydb->executePreparedStatement($stmt);
    if ($result) {
        echo "executePreparedStatement successful\n";
        $data = $mydb->fetch_object($result);
        if ($data) {
            echo "Data fetched successfully. Recording ID: " . $data->RECORDING_ID . "\n";
        } else {
            echo "No data fetched\n";
        }
    } else {
        echo "executePreparedStatement failed: " . $mydb->error_msg . "\n";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "prepareStatement failed: " . $mydb->error_msg . "\n";
}

// Test loadSingleResultPrepared method
echo "Testing loadSingleResultPrepared method...\n";
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = ? LIMIT 1";
$result = $mydb->loadSingleResultPrepared($sql, ['3'], 's');
if ($result) {
    echo "loadSingleResultPrepared successful. Recording ID: " . $result->RECORDING_ID . "\n";
} else {
    echo "loadSingleResultPrepared failed or no results\n";
}
?>