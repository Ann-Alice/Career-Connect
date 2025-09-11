<!DOCTYPE html>
<html>
<head>
    <title>Interview Recording System Validation Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .test-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .test-section { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        h1, h2 { color: #333; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .checklist { margin: 10px 0; }
        .checklist li { margin: 5px 0; }
        form { margin: 20px 0; }
        input[type=submit] { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        input[type=submit]:hover { background: #005a87; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>Interview Recording System Validation Test</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset']) && $_POST['reset'] === 'yes') {
            // Handle reset request
            require_once('include/initialize.php');
            global $mydb;
            
            $test_registration_id = 3;
            
            echo "<h2>Resetting Interview Status</h2>\n";
            echo "<pre class='info'>\n";
            
            if ($mydb) {
                // Delete recordings
                $sql = "DELETE FROM tblinterviewrecordings WHERE REGISTRATIONID = ?";
                $stmt = $mydb->prepareStatement($sql, [$test_registration_id], 'i');
                if ($stmt && $mydb->executePreparedStatement($stmt)) {
                    $affected = $mydb->affected_rows();
                    echo "✅ Deleted $affected recording(s) for registration ID $test_registration_id\n";
                }
                
                // Reset registration status
                $sql = "UPDATE tblregistration SET STATUS = 'Pending', INTERVIEW_STATUS = NULL, REMARKS = NULL WHERE REGISTRATIONID = ?";
                $stmt = $mydb->prepareStatement($sql, [$test_registration_id], 'i');
                if ($stmt && $mydb->executePreparedStatement($stmt)) {
                    echo "✅ Reset registration ID $test_registration_id status to Pending\n";
                }
                
                // Reset interview invitation status
                $sql = "UPDATE tblinterviewinvitation SET STATUS = 'Pending' WHERE REGISTRATIONID = ?";
                $stmt = $mydb->prepareStatement($sql, [$test_registration_id], 'i');
                if ($stmt && $mydb->executePreparedStatement($stmt)) {
                    echo "✅ Reset interview invitation status to Pending\n";
                }
                
                echo "✅ Reset complete. You can now retest the interview process.\n";
            } else {
                echo "❌ Database connection failed\n";
            }
            echo "</pre>\n";
            echo "<a href='validation-test.php' style='padding: 10px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 4px;'>Run Validation Test Again</a>\n";
            echo "<a href='index.php' style='padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;'>Back to Home</a>\n";
            echo "</div></body></html>";
            exit;
        }
        ?>
        
        <pre class="info">
<?php
// Validation Testing Script for Interview Recording System
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Test 1: Database Connection ===\n";
try {
    require_once('include/initialize.php');
    global $mydb;
    if ($mydb) {
        echo "✅ Database connection successful\n";
        echo "✅ Database selected: " . database_name . "\n";
    } else {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Required Tables
echo "\n=== Test 2: Required Tables ===\n";
$required_tables = ['tblinterviewrecordings', 'tblregistration'];
foreach ($required_tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $mydb->setQuery($sql);
    $result = $mydb->loadResultList();
    if (count($result) > 0) {
        echo "✅ Table $table exists\n";
    } else {
        echo "❌ Table $table does not exist\n";
    }
}

// Test 3: Web Root Configuration
echo "\n=== Test 3: Web Root Configuration ===\n";
echo "✅ Web root defined as: " . web_root . "\n";
$document_root = $_SERVER['DOCUMENT_ROOT'];
echo "✅ Document root: " . $document_root . "\n";

// Test 4: Upload Directory
echo "\n=== Test 4: Upload Directory ===\n";
$upload_dir = $document_root . web_root . 'uploads/';
echo "Upload directory path: " . $upload_dir . "\n";
if (is_dir($upload_dir)) {
    echo "✅ Upload directory exists\n";
    if (is_writable($upload_dir)) {
        echo "✅ Upload directory is writable\n";
    } else {
        echo "❌ Upload directory is not writable\n";
    }
} else {
    echo "❌ Upload directory does not exist\n";
    if (mkdir($upload_dir, 0755, true)) {
        echo "✅ Upload directory created successfully\n";
    } else {
        echo "❌ Failed to create upload directory\n";
    }
}

// Test 5: Registration ID Validation
echo "\n=== Test 5: Registration ID Validation ===\n";
$test_registration_id = 3; // The registration ID we're investigating
$sql = "SELECT * FROM tblregistration WHERE REGISTRATIONID = ?";
$registration = $mydb->loadSingleResultPrepared($sql, [$test_registration_id], 'i');
if ($registration) {
    echo "✅ Registration ID $test_registration_id found\n";
    echo "   Status: " . $registration->STATUS . "\n";
    echo "   Interview Status: " . ($registration->INTERVIEW_STATUS ?? 'N/A') . "\n";
} else {
    echo "❌ Registration ID $test_registration_id not found\n";
}

// Test 6: Interview Invitation Validation
echo "\n=== Test 6: Interview Invitation Validation ===\n";
$sql = "SELECT * FROM tblinterviewinvitation WHERE REGISTRATIONID = ?";
$invitation = $mydb->loadSingleResultPrepared($sql, [$test_registration_id], 'i');
if ($invitation) {
    echo "✅ Interview invitation found for registration ID $test_registration_id\n";
    echo "   Token: " . $invitation->TOKEN . "\n";
    echo "   Status: " . $invitation->STATUS . "\n";
} else {
    echo "❌ No interview invitation found for registration ID $test_registration_id\n";
}

// Test 7: File Upload Simulation
echo "\n=== Test 7: File Upload Simulation ===\n";
$test_file_path = $upload_dir . 'test_recording.webm';
$test_content = "This is a test recording file content";
if (file_put_contents($test_file_path, $test_content)) {
    echo "✅ Test file created successfully\n";
    
    // Test 8: Database Insert with Prepared Statements
    echo "\n=== Test 8: Database Insert with Prepared Statements ===\n";
    try {
        $sql = "INSERT INTO tblinterviewrecordings (
            REGISTRATIONID, 
            QUESTION_TYPE, 
            ESSENTIAL_QUESTION, 
            FILE_PATH, 
            DURATION, 
            TRANSCRIPT, 
            CREATED_AT
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $test_registration_id,
            'test',
            'Test Question',
            str_replace($document_root, '', $test_file_path),
            120.5,
            'This is a test transcript'
        ];
        $types = 'isssds';
        
        $stmt = $mydb->prepareStatement($sql, $params, $types);
        if ($stmt) {
            echo "✅ Prepared statement created successfully\n";
            
            $result = $mydb->executePreparedStatement($stmt);
            if ($result !== false) {
                $insert_id = $mydb->insert_id();
                echo "✅ Recording inserted successfully with ID: $insert_id\n";
                
                // Test 9: Recording Verification
                echo "\n=== Test 9: Recording Verification ===\n";
                $sql = "SELECT * FROM tblinterviewrecordings WHERE ID = ?";
                $recording = $mydb->loadSingleResultPrepared($sql, [$insert_id], 'i');
                if ($recording) {
                    echo "✅ Recording found in database\n";
                    echo "   Registration ID: " . $recording->REGISTRATIONID . "\n";
                    echo "   File Path: " . $recording->FILE_PATH . "\n";
                    echo "   Duration: " . $recording->DURATION . "\n";
                    
                    // Test 10: File Verification
                    echo "\n=== Test 10: File Verification ===\n";
                    $full_file_path = $document_root . $recording->FILE_PATH;
                    if (file_exists($full_file_path)) {
                        echo "✅ Recording file exists on disk\n";
                        echo "   File size: " . filesize($full_file_path) . " bytes\n";
                    } else {
                        echo "❌ Recording file does not exist on disk\n";
                    }
                } else {
                    echo "❌ Recording not found in database\n";
                }
                
                // Clean up test recording
                if ($recording) {
                    $sql = "DELETE FROM tblinterviewrecordings WHERE ID = ?";
                    $stmt = $mydb->prepareStatement($sql, [$insert_id], 'i');
                    if ($stmt && $mydb->executePreparedStatement($stmt)) {
                        echo "✅ Test recording cleaned up from database\n";
                    }
                }
            } else {
                echo "❌ Failed to execute prepared statement: " . $mydb->error_msg . "\n";
            }
            if ($stmt) {
                mysqli_stmt_close($stmt);
            }
        } else {
            echo "❌ Failed to prepare statement: " . $mydb->error_msg . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Database insert test failed: " . $e->getMessage() . "\n";
    }
    
    // Clean up test file
    if (file_exists($test_file_path)) {
        unlink($test_file_path);
        echo "✅ Test file cleaned up\n";
    }
} else {
    echo "❌ Failed to create test file\n";
}

// Test 11: Check Current Status of Registration ID 3
echo "\n=== Test 11: Current Status of Registration ID 3 ===\n";
$sql = "SELECT * FROM tblregistration WHERE REGISTRATIONID = ?";
$registration = $mydb->loadSingleResultPrepared($sql, [$test_registration_id], 'i');
if ($registration) {
    echo "Registration ID 3 Status:\n";
    echo "   STATUS: " . $registration->STATUS . "\n";
    echo "   INTERVIEW_STATUS: " . ($registration->INTERVIEW_STATUS ?? 'N/A') . "\n";
    echo "   REMARKS: " . ($registration->REMARKS ?? 'N/A') . "\n";
    
    // Check if there are any recordings for this registration
    $sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings WHERE REGISTRATIONID = ?";
    $result = $mydb->loadSingleResultPrepared($sql, [$test_registration_id], 'i');
    if ($result) {
        echo "   Recordings count: " . $result->count . "\n";
        if ($result->count == 0) {
            echo "   ⚠️  No recordings found for this registration\n";
        }
    }
}

echo "\n=== Validation Test Complete ===\n";
?>
        </pre>
        
        <h2>Reset Interview Status for Testing</h2>
        <p>Would you like to reset the interview status for registration ID 3 to allow retesting?</p>
        <p>This will set STATUS back to 'Pending' and remove any interview recordings.</p>
        
        <form method='post'>
            <input type='hidden' name='reset' value='yes'>
            <input type='submit' value='Reset Interview Status for Registration ID 3'>
        </form>
        
        <div style="margin-top: 20px;">
            <a href="security-test.php" style="padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">Run Security Test</a>
            <a href="index.php" style="padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Back to Home</a>
        </div>
    </div>
</body>
</html>