<?php
// Simple upload test
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Simple upload test successful',
    'timestamp' => date('Y-m-d H:i:s'),
    'post_data' => $_POST,
    'files_data' => $_FILES
]);
?> 