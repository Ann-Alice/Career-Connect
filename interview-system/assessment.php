<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');

header('Content-Type: application/json');

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['token']) || !isset($data['registration_id']) || !isset($data['video']) || !isset($data['analysis'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

// Validate token
$sql = "SELECT * FROM tblinterviewinvitations 
        WHERE TOKEN = '{$data['token']}' 
        AND REGISTRATIONID = '{$data['registration_id']}' 
        AND EXPIRY_DATE > NOW()";
$mydb->setQuery($sql);
$invitation = $mydb->loadSingleResult();

if (!$invitation) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

try {
    // Save video file
    $video_data = base64_decode(preg_replace('#^data:video/\w+;base64,#i', '', $data['video']));
    $video_filename = 'interview_' . $data['registration_id'] . '_' . time() . '.webm';
    $video_path = '../uploads/interviews/' . $video_filename;
    
    if (!is_dir('../uploads/interviews')) {
        mkdir('../uploads/interviews', 0777, true);
    }
    
    file_put_contents($video_path, $video_data);
    
    // Save video record
    $sql = "INSERT INTO tblinterviewvideos (REGISTRATIONID, VIDEO_PATH) 
            VALUES ('{$data['registration_id']}', '{$video_filename}')";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Save interview results
    $analysis_json = json_encode($data['analysis']);
    $sql = "UPDATE tbljobregistration 
            SET INTERVIEW_RESULTS = '{$analysis_json}' 
            WHERE REGISTRATIONID = '{$data['registration_id']}'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Send notification email to admin
    $sql = "SELECT r.*, a.EMAILADDRESS, a.FNAME, a.LNAME, j.OCCUPATIONTITLE 
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.REGISTRATIONID = '{$data['registration_id']}'";
    $mydb->setQuery($sql);
    $application = $mydb->loadSingleResult();
    
    $to = ADMIN_EMAIL;
    $subject = "New AI Interview Completed - {$application->OCCUPATIONTITLE}";
    $message = "
    <html>
    <head>
        <title>New AI Interview Completed</title>
    </head>
    <body>
        <h2>New AI Interview Completed</h2>
        <p>An applicant has completed their AI interview:</p>
        <ul>
            <li><strong>Applicant:</strong> {$application->FNAME} {$application->LNAME}</li>
            <li><strong>Position:</strong> {$application->OCCUPATIONTITLE}</li>
            <li><strong>Email:</strong> {$application->EMAILADDRESS}</li>
        </ul>
        <p>Please review the interview results in the admin panel.</p>
    </body>
    </html>
    ";
    
    sendEmail($to, $subject, $message);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process interview results: ' . $e->getMessage()]);
}
?> 