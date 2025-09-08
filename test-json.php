<?php
// Disable error display
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Start output buffering
ob_start();

// Test include files
try {
    require_once('include/initialize.php');
    echo "Include files loaded successfully\n";
} catch (Exception $e) {
    echo "Error loading include files: " . $e->getMessage() . "\n";
}

// Check for any output
$output = ob_get_clean();

if (!empty($output)) {
    echo "Output detected: " . $output . "\n";
} else {
    echo "No output detected\n";
}

// Test JSON output
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Test completed']);
?> 