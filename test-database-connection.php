<?php
require_once('include/initialize.php');

// Set proper headers
header('Content-Type: application/json');

try {
    // Test database connection
    $test_sql = "SELECT COUNT(*) as count FROM tblinterviewvideos";
    $mydb->setQuery($test_sql);
    $result = $mydb->loadSingleResult();
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection working',
        'video_count' => $result->count,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>