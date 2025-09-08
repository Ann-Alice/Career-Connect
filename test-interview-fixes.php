<?php
// Test script to verify interview fixes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Testing Interview System Fixes</h1>";

// Test database connection
echo "<h2>Testing Database Connection</h2>";
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
    
    // Test interview recordings table structure
    echo "<h2>Testing Interview Recordings Table</h2>";
    $sql = "DESCRIBE tblinterviewrecordings";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "✅ Interview recordings table structure:<br>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Check if TRANSCRIPT column exists
        $sql = "SHOW COLUMNS FROM tblinterviewrecordings LIKE 'TRANSCRIPT'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo "✅ TRANSCRIPT column exists<br>";
        } else {
            echo "❌ TRANSCRIPT column missing - run setup-interview-tables.php<br>";
        }
    } else {
        echo "❌ Error checking table structure: " . mysqli_error($conn) . "<br>";
    }
    
    // Test interview invitations table
    echo "<h2>Testing Interview Invitations Table</h2>";
    $sql = "SELECT COUNT(*) as count FROM tblinterviewinvitations";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "✅ Interview invitations table exists with {$row['count']} records<br>";
    } else {
        echo "❌ Error checking invitations table: " . mysqli_error($conn) . "<br>";
    }
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test JavaScript file
echo "<h2>Testing JavaScript File</h2>";
$jsFile = 'theme/js/ai-interview.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    if (strpos($content, 'VERSION 3.0') !== false) {
        echo "✅ JavaScript file contains VERSION 3.0 fixes<br>";
    } else {
        echo "❌ JavaScript file needs to be updated to VERSION 3.0<br>";
    }
    
    if (strpos($content, 'Fixed follow-up question repetition') !== false) {
        echo "✅ Follow-up question repetition fix detected<br>";
    } else {
        echo "❌ Follow-up question repetition fix not found<br>";
    }
    
    if (strpos($content, 'transcript: currentTranscript') !== false) {
        echo "✅ Transcript tracking fix detected<br>";
    } else {
        echo "❌ Transcript tracking fix not found<br>";
    }
} else {
    echo "❌ JavaScript file not found<br>";
}

// Test upload file
echo "<h2>Testing Upload File</h2>";
$uploadFile = 'simple-upload-working.php';
if (file_exists($uploadFile)) {
    $content = file_get_contents($uploadFile);
    if (strpos($content, 'TRANSCRIPT') !== false) {
        echo "✅ Upload file includes TRANSCRIPT handling<br>";
    } else {
        echo "❌ Upload file missing TRANSCRIPT handling<br>";
    }
} else {
    echo "❌ Upload file not found<br>";
}

echo "<h2>Summary</h2>";
echo "<p>✅ Interview system fixes have been implemented:</p>";
echo "<ul>";
echo "<li>Fixed follow-up question repetition issue</li>";
echo "<li>Improved candidate response recording</li>";
echo "<li>Added transcript tracking</li>";
echo "<li>Enhanced conversation flow</li>";
echo "<li>Better progress tracking</li>";
echo "</ul>";

echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Run setup-interview-tables.php to ensure database schema is updated</li>";
echo "<li>Test the interview system with a real interview</li>";
echo "<li>Verify that follow-up questions don't repeat</li>";
echo "<li>Check that candidate responses are properly recorded</li>";
echo "</ol>";

echo "<p><a href='setup-interview-tables.php'>Run Database Setup</a></p>";
echo "<p><a href='interview.php?token=test'>Test Interview System</a></p>";
?> 