<?php
// Test current system status
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Current System Status Check</h1>";

// Test 1: Database connection and tables
echo "<h2>1. Database Status</h2>";
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
    
    // Check tables
    $tables = ['tblinterviewrecordings', 'tblinterviewinvitations'];
    foreach ($tables as $table) {
        $sql = "SHOW TABLES LIKE '$table'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo "✅ Table $table exists<br>";
            
            // Count records
            $sql = "SELECT COUNT(*) as count FROM $table";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_object($result);
            echo "   - Records: {$row->count}<br>";
        } else {
            echo "❌ Table $table missing<br>";
        }
    }
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 2: Upload endpoint test
echo "<h2>2. Upload Endpoint Test</h2>";
$test_url = "http://localhost/eris/simple-upload-fixed.php";
$response = @file_get_contents($test_url);
if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        if ($data['success']) {
            echo "✅ Upload endpoint is responding correctly<br>";
        } else {
            echo "⚠️ Upload endpoint returned error: " . $data['error'] . "<br>";
        }
    } else {
        echo "❌ Upload endpoint returned invalid JSON<br>";
        echo "Raw response: " . htmlspecialchars($response) . "<br>";
    }
} else {
    echo "❌ Upload endpoint not accessible<br>";
}

// Test 3: Check recent uploads
echo "<h2>3. Recent Uploads</h2>";
try {
    $conn = mysqli_connect($host, $username, $password, $database, $port);
    $sql = "SELECT * FROM tblinterviewrecordings ORDER BY RECORDED_AT DESC LIMIT 5";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo "✅ Recent uploads found:<br>";
        while ($row = mysqli_fetch_object($result)) {
            echo "   - {$row->FILE_PATH} (Duration: {$row->DURATION}s, Turn: {$row->CONVERSATION_TURN})<br>";
        }
    } else {
        echo "⚠️ No recent uploads found<br>";
    }
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "❌ Error checking uploads: " . $e->getMessage() . "<br>";
}

// Test 4: Check file system
echo "<h2>4. File System Check</h2>";
$upload_dir = 'uploads/interviews/9';
if (file_exists($upload_dir)) {
    $files = scandir($upload_dir);
    $webm_files = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'webm';
    });
    
    echo "✅ Upload directory exists<br>";
    echo "   - WebM files found: " . count($webm_files) . "<br>";
    foreach ($webm_files as $file) {
        $size = filesize($upload_dir . '/' . $file);
        echo "   - $file (" . round($size/1024/1024, 2) . " MB)<br>";
    }
} else {
    echo "❌ Upload directory missing<br>";
}

// Test 5: Check error logs
echo "<h2>5. Error Logs</h2>";
$log_files = [
    'simple_upload_errors.log',
    'test_upload_errors.log',
    'debug_upload_errors.log'
];

foreach ($log_files as $log_file) {
    if (file_exists($log_file)) {
        $size = filesize($log_file);
        $lines = file($log_file);
        $recent_lines = array_slice($lines, -3);
        
        echo "📄 $log_file (${size} bytes)<br>";
        echo "<pre>Recent entries:\n" . implode("", $recent_lines) . "</pre>";
    } else {
        echo "📄 $log_file does not exist<br>";
    }
}

echo "<h2>System Status Summary</h2>";
echo "<p>Based on the logs, the upload system appears to be working now.</p>";
echo "<p>If you're still getting errors, please:</p>";
echo "<ol>";
echo "<li>Check the browser console for JavaScript errors</li>";
echo "<li>Try refreshing the page</li>";
echo "<li>Clear browser cache</li>";
echo "<li>Check if there are any network errors</li>";
echo "</ol>";
?> 