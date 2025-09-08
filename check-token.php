<?php
require_once('include/initialize.php');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['valid' => false, 'error' => 'Invalid request method']));
}

// Get token from request
$input = json_decode(file_get_contents('php://input'), true);
$token = isset($input['token']) ? $input['token'] : '';

if (empty($token)) {
    die(json_encode(['valid' => false, 'error' => 'No token provided']));
}

// Check if token is valid and not expired
$sql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = '{$token}' AND EXPIRY_DATE > NOW()";
$mydb->setQuery($sql);
$invitation = $mydb->loadSingleResult();

if ($invitation) {
    echo json_encode([
        'valid' => true, 
        'registrationId' => $invitation->REGISTRATIONID,
        'expiryDate' => $invitation->EXPIRY_DATE
    ]);
} else {
    // Check if token exists but is expired
    $checkSql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = '{$token}'";
    $mydb->setQuery($checkSql);
    $checkInvitation = $mydb->loadSingleResult();
    
    if ($checkInvitation) {
        echo json_encode([
            'valid' => false, 
            'error' => 'Token expired',
            'expiryDate' => $checkInvitation->EXPIRY_DATE
        ]);
    } else {
        echo json_encode([
            'valid' => false, 
            'error' => 'Invalid token'
        ]);
    }
}
?> 