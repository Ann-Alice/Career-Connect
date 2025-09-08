<?php
// Simple upload test script
header('Content-Type: application/json');

// Basic error handling
try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Check if we have a file
    if (!isset($_FILES['video'])) {
        throw new Exception('No video file uploaded');
    }
    
    $file = $_FILES['video'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File too large (server limit)',
            UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
            UPLOAD_ERR_PARTIAL => 'File partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
        ];
        $errorMsg = isset($errors[$file['error']]) ? $errors[$file['error']] : 'Unknown upload error';
        throw new Exception('Upload error: ' . $errorMsg);
    }
    
    // Check file size
    if ($file['size'] === 0) {
        throw new Exception('File is empty');
    }
    
    // Create upload directory
    $uploadDir = 'uploads/interviews/test';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception('Cannot create upload directory');
        }
    }
    
    // Generate filename
    $filename = 'test_' . time() . '.webm';
    $filepath = $uploadDir . '/' . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'filename' => $filename,
        'size' => $file['size'],
        'path' => $filepath
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 