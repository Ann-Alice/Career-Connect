<?php
// Test script to verify upload functionality
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Upload Functionality Test</h1>";

// Test 1: Check if simple upload script exists
echo "<h2>1. File Check</h2>";
$upload_scripts = [
    'simple-upload-fixed.php',
    'test-upload-simple.php',
    'upload-interview.php'
];

foreach ($upload_scripts as $script) {
    if (file_exists($script)) {
        echo "✅ $script exists<br>";
    } else {
        echo "❌ $script missing<br>";
    }
}

// Test 2: Check database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'erisdb';
    $port = 4306;
    
    $conn = mysqli_connect($host, $username, $password, $database, $port);
    if (!$conn) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }
    echo "✅ Database connection successful<br>";
    
    // Test interview invitations table
    $sql = "SELECT COUNT(*) as count FROM tblinterviewinvitations WHERE EXPIRY_DATE > NOW()";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_object($result);
        echo "✅ Found {$row->count} active interview invitations<br>";
        
        if ($row->count > 0) {
            // Get a sample invitation
            $sql = "SELECT * FROM tblinterviewinvitations WHERE EXPIRY_DATE > NOW() LIMIT 1";
            $result = mysqli_query($conn, $sql);
            $invitation = mysqli_fetch_object($result);
            echo "Sample token: " . substr($invitation->TOKEN, 0, 10) . "...<br>";
        }
    } else {
        echo "❌ Error querying invitations: " . mysqli_error($conn) . "<br>";
    }
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Check upload directory
echo "<h2>3. Upload Directory Test</h2>";
$upload_dir = 'uploads/interviews';
if (file_exists($upload_dir)) {
    echo "✅ Upload directory exists<br>";
    if (is_writable($upload_dir)) {
        echo "✅ Upload directory is writable<br>";
    } else {
        echo "❌ Upload directory is not writable<br>";
    }
} else {
    echo "❌ Upload directory missing<br>";
}

// Test 4: Test simple upload endpoint
echo "<h2>4. Simple Upload Endpoint Test</h2>";
$test_url = "http://localhost/eris/simple-upload-fixed.php";
$response = @file_get_contents($test_url);
if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        if ($data['success']) {
            echo "✅ Simple upload endpoint is responding correctly<br>";
        } else {
            echo "⚠️ Simple upload endpoint returned error: " . $data['error'] . "<br>";
        }
    } else {
        echo "❌ Simple upload endpoint returned invalid JSON<br>";
    }
} else {
    echo "❌ Simple upload endpoint not accessible<br>";
}

// Test 5: Check for error logs
echo "<h2>5. Error Logs Check</h2>";
$log_files = [
    'simple_upload_errors.log',
    'test_upload_errors.log',
    'debug_upload_errors.log'
];

foreach ($log_files as $log_file) {
    if (file_exists($log_file)) {
        $size = filesize($log_file);
        echo "📄 $log_file exists (${size} bytes)<br>";
        if ($size > 0) {
            $content = file_get_contents($log_file);
            $lines = explode("\n", $content);
            $recent_lines = array_slice($lines, -5);
            echo "<pre>Recent entries:\n" . implode("\n", $recent_lines) . "</pre>";
        }
    } else {
        echo "📄 $log_file does not exist<br>";
    }
}

echo "<h2>Test Complete</h2>";
echo "<p>If you see mostly ✅ marks, the upload system should be working.</p>";
echo "<p>To test the full flow:</p>";
echo "<ol>";
echo "<li>Start an interview</li>";
echo "<li>Record a response</li>";
echo "<li>Check the browser console for upload logs</li>";
echo "<li>Check the error logs if issues occur</li>";
echo "</ol>";
?> 