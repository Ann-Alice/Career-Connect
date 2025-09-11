<?php
// Simulate the JavaScript upload request
require_once('include/initialize.php');

// Create a test file
$test_content = "This is a test recording file content";
$base_upload_dir = 'uploads/interviews';
$registration_id = '3';
$registration_upload_dir = $base_upload_dir . '/' . $registration_id;

// Create directories if they don't exist
if (!is_dir($base_upload_dir)) {
    mkdir($base_upload_dir, 0755, true);
}
if (!is_dir($registration_upload_dir)) {
    mkdir($registration_upload_dir, 0755, true);
}

$timestamp = time();
$file_extension = 'webm';
$unique_filename = "recording_{$registration_id}_{$timestamp}.{$file_extension}";
$file_path = $registration_upload_dir . '/' . $unique_filename;

// Write test file
file_put_contents($file_path, $test_content);
echo "Test file created: " . $file_path . "\n";

// Simulate the $_FILES array
$_FILES['video'] = [
    'name' => $unique_filename,
    'type' => 'video/webm',
    'tmp_name' => $file_path,
    'error' => 0,
    'size' => filesize($file_path)
];

// Simulate the $_POST array
$_POST = [
    'conversationTurn' => 1,
    'questionType' => 'essential_answer',
    'currentEssentialQuestion' => 0,
    'duration' => 15.5,
    'token' => 'test_token',
    'registrationId' => '3',
    'transcript' => 'This is a test transcript',
    'test' => 'true' // Skip token validation for test
];

// Include the upload script
echo "Including upload script...\n";
include 'upload-recording-enhanced.php';

// Clean up test file
if (file_exists($file_path)) {
    unlink($file_path);
}
?>