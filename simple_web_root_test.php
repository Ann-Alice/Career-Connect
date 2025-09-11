<?php
// Set a simple web root for testing
$web_root = '/Career Connect/Career-Connect/';
echo "Web Root: " . $web_root . "\n";

// Test accessing the upload script through the correct path
$test_url = "http://localhost" . $web_root . "upload-recording-enhanced.php";
echo "Test URL: " . $test_url . "\n";

// Check if the file exists
$upload_script_path = __DIR__ . "/upload-recording-enhanced.php";
echo "Upload script exists: " . (file_exists($upload_script_path) ? "Yes" : "No") . "\n";
?>