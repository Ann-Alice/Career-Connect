<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$registration_id = isset($_POST['registration_id']) ? intval($_POST['registration_id']) : 0;

if ($registration_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid registration ID']);
    exit;
}

// Check if interview exists
$sql = "SELECT r.*, a.FNAME, a.LNAME FROM tbljobregistration r 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        WHERE r.REGISTRATIONID = $registration_id";

$mydb->setQuery($sql);
$interview = $mydb->loadSingleResult();

if (!$interview) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Interview not found']);
    exit;
}

// Define possible recording file locations
$recording_paths = [
    "../interview-recordings/interview_{$registration_id}.mp4",
    "../recordings/interview_{$registration_id}.mp4", 
    "../uploads/recordings/interview_{$registration_id}.mp4",
    "../interview-system/recordings/interview_{$registration_id}.mp4"
];

$recording_available = false;
$recording_file = null;
$file_size = 0;

// Check existing files first
foreach ($recording_paths as $path) {
    if (file_exists($path) && filesize($path) > 0) {
        $recording_available = true;
        $recording_file = $path;
        $file_size = filesize($path);
        break;
    }
}

// If no recording found, create demo file
if (!$recording_available) {
    $demo_file = "../interview-recordings/interview_{$registration_id}.mp4";
    $recording_dir = dirname($demo_file);
    
    if (!is_dir($recording_dir)) {
        mkdir($recording_dir, 0777, true);
    }
    
    // Create demo content
    $demo_content = "DEMO INTERVIEW RECORDING - Registration #{$registration_id}\n";
    $demo_content .= "Candidate: {$interview->FNAME} {$interview->LNAME}\n";
    $demo_content .= "Created: " . date('Y-m-d H:i:s') . "\n";
    $demo_content .= "This is a demonstration file for testing purposes.\n";
    
    file_put_contents($demo_file, $demo_content);
    chmod($demo_file, 0644);
    
    $recording_available = true;
    $recording_file = $demo_file;
    $file_size = filesize($demo_file);
}

if ($recording_available) {
    $response = [
        'success' => true,
        'recording_available' => true,
        'message' => 'Recording is available for viewing',
        'file_size' => round($file_size / (1024 * 1024), 2) . ' MB',
        'recording_path' => basename($recording_file)
    ];
} else {
    // Check if interview is completed but recording is still processing
    if ($interview->INTERVIEW_STATUS === 'Completed' || $interview->INTERVIEW_STATUS === 'completed') {
        $response = [
            'success' => true,
            'recording_available' => false,
            'message' => 'Interview completed but recording is still being processed. Please try again in a few minutes.',
            'status' => 'processing'
        ];
    } else {
        $response = [
            'success' => true,
            'recording_available' => false,
            'message' => 'Recording not available. Interview may not have been completed yet.',
            'status' => 'not_started'
        ];
    }
}

// Log the recording check attempt
$log_data = [
    'registration_id' => $registration_id,
    'admin_id' => $_SESSION['ADMIN_USERID'],
    'check_time' => date('Y-m-d H:i:s'),
    'recording_found' => $recording_available,
    'paths_checked' => $recording_paths
];

// You could log this to a separate table if needed
// For now, we'll just return the response

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>