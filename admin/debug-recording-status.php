<?php
/**
 * Debug Recording Status
 * Diagnose why recordings are showing as unavailable
 */

require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

$registration_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Debug Recording Status</title>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f8f9fa;
            padding: 20px;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 30px; 
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
        }
        .section { 
            background: white; 
            border-radius: 8px; 
            padding: 20px; 
            margin: 20px 0; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #667eea;
        }
        .success { 
            border-left-color: #28a745;
            background: #d4edda;
        }
        .warning { 
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .error { 
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .info { 
            border-left-color: #17a2b8;
            background: #d1ecf1;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .file-info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1><i class='fa fa-bug'></i> Debug Recording Status</h1>
            <p>Diagnosing why recordings are showing as unavailable</p>
        </div>";

if ($registration_id > 0) {
    echo "<div class='section'>
            <h2><i class='fa fa-search'></i> Debugging Registration ID: {$registration_id}</h2>";
    
    try {
        // Get interview information
        $sql = "SELECT r.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME
                FROM tbljobregistration r 
                JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                JOIN tbljob j ON r.JOBID = j.JOBID 
                JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                WHERE r.REGISTRATIONID = ?";
        $mydb->setQuery($sql);
        $mydb->bind_param('i', $registration_id);
        $interview = $mydb->loadSingleResult();
        
        if ($interview) {
            echo "<h4>Interview Information:</h4>
                  <p><strong>Candidate:</strong> " . htmlspecialchars($interview->FNAME . ' ' . $interview->LNAME) . "</p>
                  <p><strong>Position:</strong> " . htmlspecialchars($interview->OCCUPATIONTITLE) . "</p>
                  <p><strong>Company:</strong> " . htmlspecialchars($interview->COMPANYNAME) . "</p>
                  <p><strong>Status:</strong> " . htmlspecialchars($interview->INTERVIEW_STATUS) . "</p>";
        } else {
            echo "<div class='error'><i class='fa fa-times'></i> ❌ Interview not found for Registration ID: {$registration_id}</div>";
        }
        
        // Check individual recordings
        echo "<h3><i class='fa fa-file-video-o'></i> Individual Recordings Check</h3>";
        $sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = ? ORDER BY RECORDED_AT DESC";
        $mydb->setQuery($sql);
        $mydb->bind_param('i', $registration_id);
        $recordings = $mydb->loadResultList();
        
        if ($recordings) {
            echo "<p>Found " . count($recordings) . " individual recordings:</p>
                  <table class='table table-striped'>
                    <tr>
                        <th>ID</th>
                        <th>Question #</th>
                        <th>File Path</th>
                        <th>Duration</th>
                        <th>Recorded At</th>
                        <th>File Status</th>
                    </tr>";
            
            foreach ($recordings as $recording) {
                $file_exists = file_exists($recording->FILE_PATH);
                $file_size = $file_exists ? filesize($recording->FILE_PATH) : 0;
                $file_status = $file_exists ? "<span class='text-success'>Available (" . round($file_size/1024, 2) . " KB)</span>" : "<span class='text-danger'>Missing</span>";
                
                echo "<tr>
                        <td>{$recording->RECORDINGID}</td>
                        <td>{$recording->QUESTION_NUMBER}</td>
                        <td>" . htmlspecialchars($recording->FILE_PATH) . "</td>
                        <td>{$recording->DURATION} sec</td>
                        <td>{$recording->RECORDED_AT}</td>
                        <td>{$file_status}</td>
                      </tr>";
                
                // Show file details if it exists
                if ($file_exists) {
                    echo "<tr>
                            <td colspan='6'>
                                <div class='file-info'>
                                    <strong>File Details:</strong><br>
                                    <strong>Permissions:</strong> " . substr(sprintf('%o', fileperms($recording->FILE_PATH)), -4) . "<br>
                                    <strong>Owner:</strong> " . (function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($recording->FILE_PATH))['name'] : 'N/A') . "<br>
                                    <strong>Size:</strong> " . $file_size . " bytes<br>
                                    <strong>Last Modified:</strong> " . date('Y-m-d H:i:s', filemtime($recording->FILE_PATH)) . "<br>
                                    <strong>Readable:</strong> " . (is_readable($recording->FILE_PATH) ? 'Yes' : 'No') . "<br>
                                    <strong>Writable:</strong> " . (is_writable($recording->FILE_PATH) ? 'Yes' : 'No') . "
                                </div>
                            </td>
                          </tr>";
                }
            }
            echo "</table>";
        } else {
            echo "<div class='warning'><i class='fa fa-info-circle'></i> ⚠️ No individual recordings found in database for Registration ID: {$registration_id}</div>";
        }
        
        // Check consolidated videos
        echo "<h3><i class='fa fa-film'></i> Consolidated Videos Check</h3>";
        $sql = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = ? ORDER BY CREATED_AT DESC";
        $mydb->setQuery($sql);
        $mydb->bind_param('i', $registration_id);
        $videos = $mydb->loadResultList();
        
        if ($videos) {
            echo "<p>Found " . count($videos) . " consolidated videos:</p>
                  <table class='table table-striped'>
                    <tr>
                        <th>ID</th>
                        <th>Video Path</th>
                        <th>Created At</th>
                        <th>File Status</th>
                    </tr>";
            
            foreach ($videos as $video) {
                // Check multiple possible paths
                $possible_paths = [
                    "uploads/interviews/consolidated/" . $video->VIDEO_PATH,
                    "uploads/interviews/" . $video->VIDEO_PATH,
                    "../uploads/interviews/consolidated/" . $video->VIDEO_PATH,
                    "../uploads/interviews/" . $video->VIDEO_PATH
                ];
                
                $file_exists = false;
                $actual_path = '';
                foreach ($possible_paths as $path) {
                    if (file_exists($path)) {
                        $file_exists = true;
                        $actual_path = $path;
                        break;
                    }
                }
                
                $file_size = $file_exists ? filesize($actual_path) : 0;
                $file_status = $file_exists ? "<span class='text-success'>Available (" . round($file_size/1024, 2) . " KB)</span>" : "<span class='text-danger'>Missing</span>";
                
                echo "<tr>
                        <td>{$video->VIDEOID}</td>
                        <td>" . htmlspecialchars($video->VIDEO_PATH) . "</td>
                        <td>{$video->CREATED_AT}</td>
                        <td>{$file_status}</td>
                      </tr>";
                
                if ($file_exists) {
                    echo "<tr>
                            <td colspan='4'>
                                <div class='file-info'>
                                    <strong>Actual Path:</strong> " . htmlspecialchars($actual_path) . "<br>
                                    <strong>Size:</strong> " . $file_size . " bytes<br>
                                    <strong>Readable:</strong> " . (is_readable($actual_path) ? 'Yes' : 'No') . "
                                </div>
                            </td>
                          </tr>";
                }
            }
            echo "</table>";
        } else {
            echo "<div class='warning'><i class='fa fa-info-circle'></i> ⚠️ No consolidated videos found in database for Registration ID: {$registration_id}</div>";
        }
        
        // Check uploads directory structure
        echo "<h3><i class='fa fa-folder'></i> Uploads Directory Check</h3>";
        $upload_dirs = [
            "uploads/",
            "uploads/interviews/",
            "uploads/interviews/{$registration_id}/",
            "uploads/interviews/consolidated/"
        ];
        
        foreach ($upload_dirs as $dir) {
            $full_path = "../" . $dir;
            if (is_dir($full_path)) {
                $writable = is_writable($full_path) ? "<span class='text-success'>Writable</span>" : "<span class='text-danger'>Not Writable</span>";
                echo "<p><strong>" . htmlspecialchars($dir) . ":</strong> Exists - {$writable}</p>";
                
                // List files in directory
                $files = scandir($full_path);
                $relevant_files = array_filter($files, function($file) use ($registration_id) {
                    return $file !== '.' && $file !== '..' && (strpos($file, (string)$registration_id) !== false || $registration_id == 0);
                });
                
                if (!empty($relevant_files)) {
                    echo "<ul>";
                    foreach ($relevant_files as $file) {
                        echo "<li>" . htmlspecialchars($file) . "</li>";
                    }
                    echo "</ul>";
                }
            } else {
                echo "<p><strong>" . htmlspecialchars($dir) . ":</strong> <span class='text-danger'>Does not exist</span></p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='error'><i class='fa fa-exclamation-triangle'></i> ❌ Error during debugging: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
} else {
    // Show list of recent interviews
    echo "<div class='section'>
            <h2><i class='fa fa-list'></i> Recent Interviews</h2>";
    
    try {
        $sql = "SELECT r.REGISTRATIONID, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME, r.INTERVIEW_STATUS
                FROM tbljobregistration r 
                JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                JOIN tbljob j ON r.JOBID = j.JOBID 
                JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                WHERE r.INTERVIEW_STATUS IS NOT NULL
                ORDER BY r.REGISTRATIONDATE DESC 
                LIMIT 20";
        $mydb->setQuery($sql);
        $interviews = $mydb->loadResultList();
        
        if ($interviews) {
            echo "<table class='table table-striped'>
                    <tr>
                        <th>Registration ID</th>
                        <th>Candidate</th>
                        <th>Position</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>";
            
            foreach ($interviews as $interview) {
                echo "<tr>
                        <td>{$interview->REGISTRATIONID}</td>
                        <td>" . htmlspecialchars($interview->FNAME . ' ' . $interview->LNAME) . "</td>
                        <td>" . htmlspecialchars($interview->OCCUPATIONTITLE) . "</td>
                        <td>" . htmlspecialchars($interview->COMPANYNAME) . "</td>
                        <td>" . htmlspecialchars($interview->INTERVIEW_STATUS) . "</td>
                        <td>
                            <a href='?id={$interview->REGISTRATIONID}' class='btn btn-info btn-sm'>
                                <i class='fa fa-bug'></i> Debug
                            </a>
                            <a href='stream_recording.php?id={$interview->REGISTRATIONID}' class='btn btn-success btn-sm' target='_blank'>
                                <i class='fa fa-play'></i> Stream
                            </a>
                        </td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='warning'><i class='fa fa-info-circle'></i> ⚠️ No interviews found in database</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'><i class='fa fa-exclamation-triangle'></i> ❌ Error fetching interviews: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
}

echo "<div class='section'>
        <h2><i class='fa fa-wrench'></i> Common Issues and Solutions</h2>
        <div class='info'>
            <h4>Possible Causes:</h4>
            <ol>
                <li><strong>Files not uploaded properly:</strong> The recording process may have failed or been interrupted</li>
                <li><strong>Incorrect file paths in database:</strong> Path stored in database doesn't match actual file location</li>
                <li><strong>Permission issues:</strong> Web server doesn't have read access to the files</li>
                <li><strong>Directory structure problems:</strong> Upload directories don't exist or aren't writable</li>
                <li><strong>Interview not completed:</strong> Recording process started but interview wasn't properly completed</li>
            </ol>
        </div>
        
        <div class='success'>
            <h4>Solutions:</h4>
            <ol>
                <li><strong>Complete a new interview:</strong> Go through the full interview process and ensure you click 'Complete Interview'</li>
                <li><strong>Check directory permissions:</strong> Ensure uploads directories are writable (755 or 777)</li>
                <li><strong>Verify file paths:</strong> Check that paths in database match actual file locations</li>
                <li><strong>Test with demo content:</strong> Use the upload tool to add sample videos for testing</li>
            </ol>
        </div>
        
        <div style='text-align: center; margin: 20px 0;'>
            <a href='create-real-videos.php' class='btn btn-warning'>
                <i class='fa fa-upload'></i> Upload Sample Video
            </a>
            <a href='interview-results.php' class='btn btn-primary'>
                <i class='fa fa-bar-chart'></i> View Interview Results
            </a>
            <a href='../interview.php' class='btn btn-success'>
                <i class='fa fa-video-camera'></i> Start New Interview
            </a>
        </div>
      </div>";

echo "</div>
</body>
</html>";
?>