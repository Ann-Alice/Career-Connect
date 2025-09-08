<?php
// Minimal upload test - no database dependencies
header('Content-Type: application/json');

// Log the request
error_log("Minimal upload request received");

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Check for file
    if (!isset($_FILES['video'])) {
        throw new Exception('No video file uploaded');
    }
    
    $file = $_FILES['video'];
    error_log("File received: " . json_encode($file));
    
    // Check file error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error: ' . $file['error']);
    }
    
    // Check file size
    if ($file['size'] === 0) {
        throw new Exception('File is empty');
    }
    
    // Create test directory
    $testDir = 'uploads/interviews/minimal_test';
    if (!file_exists($testDir)) {
        if (!mkdir($testDir, 0777, true)) {
            throw new Exception('Cannot create test directory');
        }
    }
    
    // Save file
    $filename = 'minimal_test_' . time() . '.webm';
    $filepath = $testDir . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Success
    echo json_encode([
        'success' => true,
        'message' => 'Minimal upload successful',
        'filename' => $filename,
        'size' => $file['size']
    ]);
    
} catch (Exception $e) {
    error_log("Minimal upload error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 