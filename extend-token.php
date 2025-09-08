<?php
require_once('include/initialize.php');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'error' => 'Invalid request method']));
}

// Get token from request
$input = json_decode(file_get_contents('php://input'), true);
$token = isset($input['token']) ? $input['token'] : '';

if (empty($token)) {
    die(json_encode(['success' => false, 'error' => 'No token provided']));
}

// Check if token exists
$sql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = '{$token}'";
$mydb->setQuery($sql);
$invitation = $mydb->loadSingleResult();

if (!$invitation) {
    die(json_encode(['success' => false, 'error' => 'Invalid token']));
}

// Extend the token expiry by 2 hours
$newExpiry = date('Y-m-d H:i:s', strtotime('+2 hours'));
$updateSql = "UPDATE tblinterviewinvitations SET EXPIRY_DATE = '{$newExpiry}' WHERE TOKEN = '{$token}'";
$mydb->setQuery($updateSql);

if ($mydb->executeQuery()) {
    echo json_encode([
        'success' => true, 
        'newExpiry' => $newExpiry,
        'message' => 'Token expiry extended successfully'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Failed to extend token expiry'
    ]);
}
?> 