<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set maximum upload size
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');
ini_set('max_execution_time', 300);

echo "Upload Test Endpoint\n";
echo "===================\n\n";

// Check PHP configuration
echo "PHP Configuration:\n";
echo "- upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "- post_max_size: " . ini_get('post_max_size') . "\n";
echo "- max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "- max_file_uploads: " . ini_get('max_file_uploads') . "\n\n";

// Check uploads directory
$uploadsDir = 'uploads/interviews';
echo "Uploads Directory Check:\n";
echo "- Directory exists: " . (file_exists($uploadsDir) ? 'Yes' : 'No') . "\n";
if (file_exists($uploadsDir)) {
    echo "- Directory writable: " . (is_writable($uploadsDir) ? 'Yes' : 'No') . "\n";
    echo "- Directory permissions: " . substr(sprintf('%o', fileperms($uploadsDir)), -4) . "\n";
}

// Check if we can create a test directory
$testDir = $uploadsDir . '/test_' . time();
echo "- Can create test directory: " . (mkdir($testDir, 0777, true) ? 'Yes' : 'No') . "\n";
if (file_exists($testDir)) {
    rmdir($testDir);
}

// Check request method
echo "\nRequest Information:\n";
echo "- Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "- Content-Type: " . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'Not set') . "\n";

// Check POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "\nPOST Data:\n";
    echo "- POST variables: " . count($_POST) . "\n";
    foreach ($_POST as $key => $value) {
        echo "  - $key: " . (is_string($value) ? $value : 'Array/Object') . "\n";
    }
    
    echo "\nFiles:\n";
    echo "- FILES variables: " . count($_FILES) . "\n";
    foreach ($_FILES as $key => $file) {
        echo "  - $key:\n";
        echo "    - name: " . $file['name'] . "\n";
        echo "    - type: " . $file['type'] . "\n";
        echo "    - size: " . $file['size'] . "\n";
        echo "    - error: " . $file['error'] . "\n";
        echo "    - tmp_name: " . $file['tmp_name'] . "\n";
    }
}

echo "\nTest completed.\n";
?> 