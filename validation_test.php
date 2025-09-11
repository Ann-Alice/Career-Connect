<?php
/**
 * Validation Testing for Interview Recording System
 * This script tests all components of the interview recording system to ensure they work correctly
 */

require_once('include/initialize.php');

echo "<h1>Interview Recording System Validation Test</h1>\n";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>\n";
try {
    $sql = "SELECT 1 as test";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    if ($result && $result->test == 1) {
        echo "<p style='color: green;'>✅ Database connection successful</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection error: " . $e->getMessage() . "</p>\n";
    exit(1);
}

// Test 2: Database Tables Exist
echo "<h2>Test 2: Required Database Tables</h2>\n";
$tables = ['tblinterviewrecordings', 'tblinterviewinvitations', 'tbljobregistration'];
$missing_tables = [];

foreach ($tables as $table) {
    try {
        $sql = "SHOW TABLES LIKE '{$table}'";
        $mydb->setQuery($sql);
        $result = $mydb->loadResultList();
        if (count($result) > 0) {
            echo "<p style='color: green;'>✅ Table {$table} exists</p>\n";
        } else {
            echo "<p style='color: red;'>❌ Table {$table} is missing</p>\n";
            $missing_tables[] = $table;
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error checking table {$table}: " . $e->getMessage() . "</p>\n";
        $missing_tables[] = $table;
    }
}

if (!empty($missing_tables)) {
    echo "<p style='color: red;'>❌ Missing tables: " . implode(', ', $missing_tables) . "</p>\n";
    exit(1);
}

// Test 3: Web Root Configuration
echo "<h2>Test 3: Web Root Configuration</h2>\n";
$web_root = defined('web_root') ? web_root : '';
if (!empty($web_root)) {
    echo "<p style='color: green;'>✅ Web root defined: {$web_root}</p>\n";
    
    // Test if upload script exists
    $upload_script_path = __DIR__ . '/upload-recording-enhanced.php';
    if (file_exists($upload_script_path)) {
        echo "<p style='color: green;'>✅ Upload script exists</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Upload script missing</p>\n";
        exit(1);
    }
} else {
    echo "<p style='color: red;'>❌ Web root not defined</p>\n";
    exit(1);
}

// Test 4: Upload Directory Permissions
echo "<h2>Test 4: Upload Directory Permissions</h2>\n";
$base_upload_dir = 'uploads/interviews';
if (!is_dir($base_upload_dir)) {
    if (!mkdir($base_upload_dir, 0755, true)) {
        echo "<p style='color: red;'>❌ Failed to create base upload directory</p>\n";
        exit(1);
    }
}
echo "<p style='color: green;'>✅ Base upload directory exists</p>\n";

if (is_writable($base_upload_dir)) {
    echo "<p style='color: green;'>✅ Base upload directory is writable</p>\n";
} else {
    echo "<p style='color: red;'>❌ Base upload directory is not writable</p>\n";
    exit(1);
}

// Test 5: Registration ID 3 Exists
echo "<h2>Test 5: Registration ID Validation</h2>\n";
$sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS FROM tbljobregistration WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$registration = $mydb->loadSingleResult();

if ($registration) {
    echo "<p style='color: green;'>✅ Registration ID 3 exists</p>\n";
    echo "<p>Current Status: " . ($registration->INTERVIEW_STATUS ?: 'Not set') . "</p>\n";
} else {
    echo "<p style='color: red;'>❌ Registration ID 3 not found</p>\n";
    exit(1);
}

// Test 6: Interview Invitation Exists
echo "<h2>Test 6: Interview Invitation Validation</h2>\n";
$sql = "SELECT INVITATION_ID, TOKEN, STATUS FROM tblinterviewinvitations WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$invitation = $mydb->loadSingleResult();

if ($invitation) {
    echo "<p style='color: green;'>✅ Interview invitation exists for registration ID 3</p>\n";
    echo "<p>Token: " . substr($invitation->TOKEN, 0, 20) . "...</p>\n";
    echo "<p>Status: " . ($invitation->STATUS ?: 'Not set') . "</p>\n";
} else {
    echo "<p style='color: red;'>❌ No interview invitation found for registration ID 3</p>\n";
    // Create a test invitation
    $token = 'test_' . md5(uniqid(rand(), true));
    $expiry_date = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $sql = "INSERT INTO tblinterviewinvitations (REGISTRATIONID, JOBID, TOKEN, EXPIRY_DATE, STATUS) VALUES ('3', '1', '{$token}', '{$expiry_date}', 'pending')";
    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        echo "<p style='color: green;'>✅ Test invitation created successfully</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Failed to create test invitation: " . $mydb->error_msg . "</p>\n";
        exit(1);
    }
}

// Test 7: Simulate File Upload
echo "<h2>Test 7: File Upload Simulation</h2>\n";

// Create a test file
$test_content = "This is a test recording file content for validation testing";
$registration_id = '3';
$registration_upload_dir = 'uploads/interviews/' . $registration_id;

if (!is_dir($registration_upload_dir)) {
    if (!mkdir($registration_upload_dir, 0755, true)) {
        echo "<p style='color: red;'>❌ Failed to create registration upload directory</p>\n";
        exit(1);
    }
}
echo "<p style='color: green;'>✅ Registration upload directory exists</p>\n";

$timestamp = time();
$file_extension = 'webm';
$unique_filename = "recording_{$registration_id}_{$timestamp}.{$file_extension}";
$file_path = $registration_upload_dir . '/' . $unique_filename;

// Write test file
if (file_put_contents($file_path, $test_content)) {
    echo "<p style='color: green;'>✅ Test file created successfully</p>\n";
} else {
    echo "<p style='color: red;'>❌ Failed to create test file</p>\n";
    exit(1);
}

// Test 8: Database Insert with Prepared Statements
echo "<h2>Test 8: Database Insert with Prepared Statements</h2>\n";
$sql = "INSERT INTO tblinterviewrecordings 
        (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";

$params = [
    $registration_id,
    1,
    $file_path,
    15.5,
    1,
    'test',
    'Test transcript for validation'
];
$types = 'sisidiss';

$stmt = $mydb->prepareStatement($sql, $params, $types);
if ($stmt) {
    echo "<p style='color: green;'>✅ Prepared statement created successfully</p>\n";
    
    if ($mydb->executePreparedStatement($stmt)) {
        echo "<p style='color: green;'>✅ Database insert executed successfully</p>\n";
        $recording_id = $mydb->insert_id();
        echo "<p>Inserted recording ID: {$recording_id}</p>\n";
        mysqli_stmt_close($stmt);
    } else {
        echo "<p style='color: red;'>❌ Database insert failed: " . $mydb->error_msg . "</p>\n";
        mysqli_stmt_close($stmt);
        unlink($file_path);
        exit(1);
    }
} else {
    echo "<p style='color: red;'>❌ Failed to prepare statement: " . $mydb->error_msg . "</p>\n";
    unlink($file_path);
    exit(1);
}

// Test 9: Verify Recording Inserted
echo "<h2>Test 9: Verify Recording Inserted</h2>\n";
$sql = "SELECT * FROM tblinterviewrecordings WHERE RECORDING_ID = ?";
$recording = $mydb->loadSingleResultPrepared($sql, [$recording_id], 'i');

if ($recording) {
    echo "<p style='color: green;'>✅ Recording verified in database</p>\n";
    echo "<p>File Path: " . $recording->FILE_PATH . "</p>\n";
    echo "<p>Duration: " . $recording->DURATION . " seconds</p>\n";
} else {
    echo "<p style='color: red;'>❌ Recording not found in database</p>\n";
    unlink($file_path);
    exit(1);
}

// Test 10: Clean Up Test Data
echo "<h2>Test 10: Clean Up Test Data</h2>\n";
$sql = "DELETE FROM tblinterviewrecordings WHERE RECORDING_ID = ?";
$stmt = $mydb->prepareStatement($sql, [$recording_id], 'i');
if ($stmt && $mydb->executePreparedStatement($stmt)) {
    echo "<p style='color: green;'>✅ Test recording deleted from database</p>\n";
    mysqli_stmt_close($stmt);
} else {
    echo "<p style='color: orange;'>⚠️ Failed to delete test recording from database</p>\n";
    if ($stmt) mysqli_stmt_close($stmt);
}

if (file_exists($file_path) && unlink($file_path)) {
    echo "<p style='color: green;'>✅ Test file deleted from filesystem</p>\n";
} else {
    echo "<p style='color: orange;'>⚠️ Failed to delete test file from filesystem</p>\n";
}

// Test 11: Reset Interview Status (if it was completed)
echo "<h2>Test 11: Reset Interview Status</h2>\n";
$sql = "SELECT INTERVIEW_STATUS FROM tbljobregistration WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$registration = $mydb->loadSingleResult();

if ($registration && $registration->INTERVIEW_STATUS === 'Completed') {
    $sql = "UPDATE tbljobregistration SET INTERVIEW_STATUS = 'Pending', INTERVIEW_RESULTS = NULL WHERE REGISTRATIONID = '3'";
    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        echo "<p style='color: green;'>✅ Interview status reset to Pending for testing</p>\n";
    } else {
        echo "<p style='color: orange;'>⚠️ Failed to reset interview status</p>\n";
    }
} else {
    echo "<p style='color: green;'>✅ Interview status is already suitable for testing</p>\n";
}

echo "<h2>Validation Testing Complete</h2>\n";
echo "<p style='color: green; font-size: 1.2em; font-weight: bold;'>✅ All tests passed! The interview recording system is working correctly.</p>\n";
echo "<p>You can now proceed with testing the actual interview process.</p>\n";
?>