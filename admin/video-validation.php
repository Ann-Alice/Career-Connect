<?php
require_once("../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

/**
 * Validate video recording status and integrity for a given registration
 * @param int $registrationId The registration ID to validate
 * @return array Validation results with status and details
 */
function validateVideoRecording($registrationId) {
    global $mydb;
    
    $validation = [
        'status' => 'unknown',
        'consolidated_video' => false,
        'individual_recordings' => 0,
        'total_duration' => 0,
        'file_integrity' => [],
        'errors' => [],
        'warnings' => [],
        'downloadable' => false,
        'watchable' => false
    ];
    
    try {
        // Check for consolidated video first
        $sql = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = '{$registrationId}' ORDER BY CREATED_AT DESC LIMIT 1";
        $mydb->setQuery($sql);
        $consolidatedVideo = $mydb->loadSingleResult();
        
        if ($consolidatedVideo) {
            $validation['consolidated_video'] = true;
            $videoPath = $consolidatedVideo->VIDEO_PATH;
            
            // Determine full path
            if (strpos($videoPath, 'uploads/interviews/consolidated/') === 0) {
                $fullPath = web_root . $videoPath;
            } else {
                $fullPath = 'uploads/interviews/consolidated/' . $videoPath;
            }
            
            // Check file existence and integrity
            if (file_exists($fullPath)) {
                $fileSize = filesize($fullPath);
                $validation['file_integrity']['consolidated'] = [
                    'exists' => true,
                    'readable' => is_readable($fullPath),
                    'size' => $fileSize,
                    'size_mb' => round($fileSize / 1024 / 1024, 2),
                    'path' => $fullPath
                ];
                
                if ($fileSize > 0 && is_readable($fullPath)) {
                    $validation['downloadable'] = true;
                    $validation['watchable'] = true;
                    $validation['status'] = 'available';
                } else {
                    $validation['errors'][] = 'Consolidated video file is empty or not readable';
                    $validation['status'] = 'corrupted';
                }
            } else {
                $validation['errors'][] = 'Consolidated video file not found: ' . $fullPath;
                $validation['status'] = 'missing';
            }
        }
        
        // Check individual recordings
        $sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = '{$registrationId}' ORDER BY QUESTION_NUMBER ASC";
        $mydb->setQuery($sql);
        $recordings = $mydb->loadResultList();
        
        if ($recordings) {
            $validation['individual_recordings'] = count($recordings);
            $totalDuration = 0;
            $validRecordings = 0;
            
            foreach ($recordings as $recording) {
                $recordingPath = $recording->FILE_PATH;
                $recordingInfo = [
                    'question_number' => $recording->QUESTION_NUMBER,
                    'conversation_turn' => $recording->CONVERSATION_TURN,
                    'exists' => false,
                    'readable' => false,
                    'size' => 0,
                    'duration' => $recording->DURATION
                ];
                
                if (file_exists($recordingPath)) {
                    $recordingInfo['exists'] = true;
                    $recordingInfo['readable'] = is_readable($recordingPath);
                    $recordingInfo['size'] = filesize($recordingPath);
                    
                    if ($recordingInfo['size'] > 0 && $recordingInfo['readable']) {
                        $validRecordings++;
                        $totalDuration += (float)$recording->DURATION;
                    }
                } else {
                    $validation['errors'][] = "Individual recording missing: Question {$recording->QUESTION_NUMBER}, Turn {$recording->CONVERSATION_TURN}";
                }
                
                $validation['file_integrity']['recordings'][] = $recordingInfo;
            }
            
            $validation['total_duration'] = $totalDuration;
            
            // If no consolidated video but have valid individual recordings
            if (!$validation['consolidated_video'] && $validRecordings > 0) {
                $validation['downloadable'] = true;
                $validation['watchable'] = true;
                $validation['status'] = 'individual_only';
                $validation['warnings'][] = 'Only individual recordings available - no consolidated video';
            }
            
            // Check if we have expected number of recordings
            $sql = "SELECT COUNT(*) as question_count FROM tblinterviewquestions 
                    WHERE JOBID = (SELECT JOBID FROM tbljobregistration WHERE REGISTRATIONID = '{$registrationId}')";
            $mydb->setQuery($sql);
            $questionResult = $mydb->loadSingleResult();
            
            if ($questionResult && $questionResult->question_count > 0) {
                $expectedRecordings = $questionResult->question_count;
                if ($validRecordings < $expectedRecordings) {
                    $validation['warnings'][] = "Expected {$expectedRecordings} recordings but found {$validRecordings} valid recordings";
                }
            }
        } else {
            $validation['errors'][] = 'No individual recordings found';
            if (!$validation['consolidated_video']) {
                $validation['status'] = 'no_recordings';
            }
        }
        
        // Final status determination
        if ($validation['status'] === 'unknown') {
            if (empty($validation['errors'])) {
                $validation['status'] = 'partial';
            } else {
                $validation['status'] = 'error';
            }
        }
        
    } catch (Exception $e) {
        $validation['status'] = 'error';
        $validation['errors'][] = 'Database error: ' . $e->getMessage();
    }
    
    return $validation;
}

/**
 * Attempt to repair/consolidate video recordings for a registration
 * @param int $registrationId The registration ID
 * @return array Repair results
 */
function repairVideoRecording($registrationId) {
    global $mydb;
    
    $repair = [
        'success' => false,
        'actions_taken' => [],
        'errors' => []
    ];
    
    try {
        // Check if consolidated video already exists
        $sql = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = '{$registrationId}'";
        $mydb->setQuery($sql);
        $existingVideo = $mydb->loadSingleResult();
        
        if ($existingVideo) {
            $repair['actions_taken'][] = 'Consolidated video already exists';
            $repair['success'] = true;
            return $repair;
        }
        
        // Get individual recordings
        $sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = '{$registrationId}' ORDER BY QUESTION_NUMBER ASC";
        $mydb->setQuery($sql);
        $recordings = $mydb->loadResultList();
        
        if (!$recordings || count($recordings) === 0) {
            $repair['errors'][] = 'No individual recordings found to consolidate';
            return $repair;
        }
        
        // Create consolidated directory
        $consolidatedDir = 'uploads/interviews/consolidated/';
        if (!is_dir($consolidatedDir)) {
            if (mkdir($consolidatedDir, 0777, true)) {
                $repair['actions_taken'][] = 'Created consolidated video directory';
            } else {
                $repair['errors'][] = 'Failed to create consolidated video directory';
                return $repair;
            }
        }
        
        // Find the largest/best recording to use as consolidated video
        $primaryRecording = null;
        $largestSize = 0;
        
        foreach ($recordings as $recording) {
            if (file_exists($recording->FILE_PATH)) {
                $fileSize = filesize($recording->FILE_PATH);
                if ($fileSize > $largestSize) {
                    $largestSize = $fileSize;
                    $primaryRecording = $recording;
                }
            }
        }
        
        if (!$primaryRecording) {
            $repair['errors'][] = 'No valid recordings found to consolidate';
            return $repair;
        }
        
        // Create consolidated video file
        $consolidatedFileName = "interview_complete_{$registrationId}_" . time() . ".webm";
        $consolidatedPath = $consolidatedDir . $consolidatedFileName;
        
        if (copy($primaryRecording->FILE_PATH, $consolidatedPath)) {
            $repair['actions_taken'][] = 'Copied primary recording to consolidated video';
            
            // Save to database
            $sql = "INSERT INTO tblinterviewvideos (REGISTRATIONID, VIDEO_PATH, CREATED_AT) 
                    VALUES ('{$registrationId}', '{$consolidatedFileName}', NOW())";
            $mydb->setQuery($sql);
            if ($mydb->executeQuery()) {
                $repair['actions_taken'][] = 'Saved consolidated video record to database';
                $repair['success'] = true;
            } else {
                $repair['errors'][] = 'Failed to save consolidated video record to database';
                // Clean up the file
                unlink($consolidatedPath);
            }
        } else {
            $repair['errors'][] = 'Failed to copy recording to consolidated path';
        }
        
    } catch (Exception $e) {
        $repair['errors'][] = 'Error during repair: ' . $e->getMessage();
    }
    
    return $repair;
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $registrationId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($registrationId <= 0) {
        echo json_encode(['error' => 'Invalid registration ID']);
        exit;
    }
    
    switch ($_GET['action']) {
        case 'validate':
            $validation = validateVideoRecording($registrationId);
            echo json_encode($validation);
            break;
            
        case 'repair':
            $repair = repairVideoRecording($registrationId);
            echo json_encode($repair);
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}

// Web interface for testing
$registrationId = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video Recording Validation</title>
    <link href="<?php echo web_root; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-available { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .status-unknown { color: #6c757d; }
        .file-info { background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 5px; }
        .recording-item { border-left: 3px solid #007bff; padding-left: 15px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 20px;">
        <h2>Video Recording Validation</h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Test Registration ID</h4>
                    </div>
                    <div class="panel-body">
                        <form method="GET">
                            <div class="form-group">
                                <label for="registration_id">Registration ID:</label>
                                <input type="number" class="form-control" id="registration_id" name="id" value="<?php echo $registrationId; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Validate</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php if ($registrationId > 0): ?>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="panel-body">
                        <button class="btn btn-info" onclick="validateVideo(<?php echo $registrationId; ?>)">Validate Video</button>
                        <button class="btn btn-warning" onclick="repairVideo(<?php echo $registrationId; ?>)">Repair Video</button>
                        <a href="download-video.php?id=<?php echo $registrationId; ?>" class="btn btn-success" target="_blank">Download Video</a>
                        <a href="view-recording.php?id=<?php echo $registrationId; ?>" class="btn btn-primary" target="_blank">View Video</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div id="validation-results"></div>
    </div>

    <script src="<?php echo web_root; ?>js/jquery.min.js"></script>
    <script src="<?php echo web_root; ?>bootstrap/js/bootstrap.min.js"></script>
    <script>
        function validateVideo(registrationId) {
            $('#validation-results').html('<div class="alert alert-info">Validating video recording...</div>');
            
            $.get('video-validation.php', {action: 'validate', id: registrationId})
                .done(function(data) {
                    displayValidationResults(data);
                })
                .fail(function() {
                    $('#validation-results').html('<div class="alert alert-danger">Error validating video recording</div>');
                });
        }
        
        function repairVideo(registrationId) {
            $('#validation-results').html('<div class="alert alert-info">Attempting to repair video recording...</div>');
            
            $.get('video-validation.php', {action: 'repair', id: registrationId})
                .done(function(data) {
                    displayRepairResults(data);
                    // Re-validate after repair
                    setTimeout(function() {
                        validateVideo(registrationId);
                    }, 1000);
                })
                .fail(function() {
                    $('#validation-results').html('<div class="alert alert-danger">Error repairing video recording</div>');
                });
        }
        
        function displayValidationResults(data) {
            let html = '<div class="panel panel-default"><div class="panel-heading"><h4>Validation Results</h4></div><div class="panel-body">';
            
            // Status
            let statusClass = 'status-' + (data.status === 'available' ? 'available' : 
                                        data.status === 'error' || data.status === 'missing' || data.status === 'corrupted' || data.status === 'no_recordings' ? 'error' : 
                                        data.status === 'individual_only' || data.status === 'partial' ? 'warning' : 'unknown');
            html += '<p><strong>Status:</strong> <span class="' + statusClass + '">' + data.status.toUpperCase() + '</span></p>';
            
            // Basic info
            html += '<p><strong>Consolidated Video:</strong> ' + (data.consolidated_video ? 'Yes' : 'No') + '</p>';
            html += '<p><strong>Individual Recordings:</strong> ' + data.individual_recordings + '</p>';
            html += '<p><strong>Total Duration:</strong> ' + data.total_duration + ' seconds</p>';
            html += '<p><strong>Downloadable:</strong> ' + (data.downloadable ? 'Yes' : 'No') + '</p>';
            html += '<p><strong>Watchable:</strong> ' + (data.watchable ? 'Yes' : 'No') + '</p>';
            
            // Errors
            if (data.errors.length > 0) {
                html += '<div class="alert alert-danger"><strong>Errors:</strong><ul>';
                data.errors.forEach(function(error) {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul></div>';
            }
            
            // Warnings
            if (data.warnings.length > 0) {
                html += '<div class="alert alert-warning"><strong>Warnings:</strong><ul>';
                data.warnings.forEach(function(warning) {
                    html += '<li>' + warning + '</li>';
                });
                html += '</ul></div>';
            }
            
            // File integrity details
            if (data.file_integrity.consolidated) {
                let cons = data.file_integrity.consolidated;
                html += '<div class="file-info"><strong>Consolidated Video:</strong><br>';
                html += 'Exists: ' + (cons.exists ? 'Yes' : 'No') + '<br>';
                html += 'Readable: ' + (cons.readable ? 'Yes' : 'No') + '<br>';
                html += 'Size: ' + cons.size_mb + ' MB<br>';
                html += 'Path: ' + cons.path + '</div>';
            }
            
            if (data.file_integrity.recordings) {
                html += '<h5>Individual Recordings:</h5>';
                data.file_integrity.recordings.forEach(function(rec) {
                    html += '<div class="recording-item">';
                    html += '<strong>Question ' + rec.question_number + ', Turn ' + rec.conversation_turn + '</strong><br>';
                    html += 'Exists: ' + (rec.exists ? 'Yes' : 'No') + ' | ';
                    html += 'Readable: ' + (rec.readable ? 'Yes' : 'No') + ' | ';
                    html += 'Size: ' + Math.round(rec.size / 1024) + ' KB | ';
                    html += 'Duration: ' + rec.duration + 's';
                    html += '</div>';
                });
            }
            
            html += '</div></div>';
            $('#validation-results').html(html);
        }
        
        function displayRepairResults(data) {
            let html = '<div class="alert ' + (data.success ? 'alert-success' : 'alert-danger') + '">';
            html += '<strong>Repair ' + (data.success ? 'Successful' : 'Failed') + '</strong>';
            
            if (data.actions_taken.length > 0) {
                html += '<br><strong>Actions Taken:</strong><ul>';
                data.actions_taken.forEach(function(action) {
                    html += '<li>' + action + '</li>';
                });
                html += '</ul>';
            }
            
            if (data.errors.length > 0) {
                html += '<br><strong>Errors:</strong><ul>';
                data.errors.forEach(function(error) {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul>';
            }
            
            html += '</div>';
            $('#validation-results').html(html);
        }
        
        // Auto-validate if registration ID is provided
        <?php if ($registrationId > 0): ?>
        $(document).ready(function() {
            validateVideo(<?php echo $registrationId; ?>);
        });
        <?php endif; ?>
    </script>
</body>
</html>