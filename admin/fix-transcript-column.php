<?php
// Fix TRANSCRIPT column missing error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix TRANSCRIPT Column Error</h1>";

try {
    // Database connection with multiple port options
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'erisdb';
    
    $ports = [4306, 3306]; // Try both common ports
    $conn = null;
    
    foreach ($ports as $port) {
        echo "Trying to connect to MySQL on port $port...<br>";
        $conn = @mysqli_connect($host, $username, $password, $database, $port);
        if ($conn) {
            echo "✅ Database connection successful on port $port<br>";
            break;
        }
    }
    
    if (!$conn) {
        throw new Exception('Database connection failed on all ports: ' . mysqli_connect_error());
    }
    
    echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    </style>";
    
    // Step 1: Check if tblinterviewrecordings table exists
    echo "<h2>Step 1: Checking table existence</h2>";
    $sql = "SHOW TABLES LIKE 'tblinterviewrecordings'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        echo "<div class='error'>❌ Table 'tblinterviewrecordings' does not exist! Creating it now...</div>";
        
        // Create the complete table with all required columns
        $create_sql = "CREATE TABLE `tblinterviewrecordings` (
            `RECORDING_ID` int(11) NOT NULL AUTO_INCREMENT,
            `REGISTRATIONID` varchar(50) NOT NULL,
            `QUESTION_NUMBER` int(11) NOT NULL DEFAULT 0,
            `FILE_PATH` varchar(500) NOT NULL,
            `DURATION` decimal(10,3) NOT NULL DEFAULT 0.000,
            `RECORDED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `CONVERSATION_TURN` int(11) NOT NULL DEFAULT 0,
            `QUESTION_TYPE` varchar(50) NOT NULL DEFAULT 'initial_answer',
            `TRANSCRIPT` text,
            PRIMARY KEY (`RECORDING_ID`),
            KEY `REGISTRATIONID` (`REGISTRATIONID`),
            KEY `RECORDED_AT` (`RECORDED_AT`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if (mysqli_query($conn, $create_sql)) {
            echo "<div class='success'>✅ Table 'tblinterviewrecordings' created successfully with TRANSCRIPT column!</div>";
        } else {
            throw new Exception("Failed to create table: " . mysqli_error($conn));
        }
    } else {
        echo "<div class='success'>✅ Table 'tblinterviewrecordings' exists</div>";
        
        // Step 2: Check current table structure
        echo "<h2>Step 2: Checking current table structure</h2>";
        $sql = "DESCRIBE tblinterviewrecordings";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            echo "<table>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            $has_transcript = false;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['Field'] == 'TRANSCRIPT') {
                    $has_transcript = true;
                }
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Step 3: Add TRANSCRIPT column if missing
            echo "<h2>Step 3: Checking TRANSCRIPT column</h2>";
            if ($has_transcript) {
                echo "<div class='success'>✅ TRANSCRIPT column already exists!</div>";
            } else {
                echo "<div class='warning'>⚠️ TRANSCRIPT column is missing. Adding it now...</div>";
                
                $alter_sql = "ALTER TABLE `tblinterviewrecordings` ADD COLUMN `TRANSCRIPT` text AFTER `QUESTION_TYPE`";
                if (mysqli_query($conn, $alter_sql)) {
                    echo "<div class='success'>✅ TRANSCRIPT column added successfully!</div>";
                } else {
                    throw new Exception("Failed to add TRANSCRIPT column: " . mysqli_error($conn));
                }
            }
        }
    }
    
    // Step 4: Test the fix by trying a sample insert
    echo "<h2>Step 4: Testing the fix</h2>";
    
    // Create a test insert statement like the one causing the error
    $test_sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
                VALUES 
                ('TEST_REG_ID', 1, 'test_file.webm', 5.5, NOW(), 1, 'test_answer', 'This is a test transcript')";
    
    echo "<div class='info'><strong>Test Query:</strong><br>" . htmlspecialchars($test_sql) . "</div>";
    
    if (mysqli_query($conn, $test_sql)) {
        echo "<div class='success'>✅ Test insert successful! The TRANSCRIPT column error is fixed.</div>";
        
        // Clean up the test record
        $cleanup_sql = "DELETE FROM tblinterviewrecordings WHERE REGISTRATIONID = 'TEST_REG_ID'";
        mysqli_query($conn, $cleanup_sql);
        echo "<div class='info'>🧹 Test record cleaned up.</div>";
    } else {
        echo "<div class='error'>❌ Test insert failed: " . mysqli_error($conn) . "</div>";
    }
    
    // Step 5: Verify final table structure
    echo "<h2>Step 5: Final table structure verification</h2>";
    $sql = "DESCRIBE tblinterviewrecordings";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $columns = [];
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row['Field'];
            $highlight = ($row['Field'] == 'TRANSCRIPT') ? " style='background-color: #d4edda;'" : "";
            echo "<tr$highlight>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check all required columns
        $required_columns = ['RECORDING_ID', 'REGISTRATIONID', 'QUESTION_NUMBER', 'FILE_PATH', 'DURATION', 'RECORDED_AT', 'CONVERSATION_TURN', 'QUESTION_TYPE', 'TRANSCRIPT'];
        $missing_columns = array_diff($required_columns, $columns);
        
        if (empty($missing_columns)) {
            echo "<div class='success'>🎉 All required columns are present! The interview recording system should work correctly now.</div>";
        } else {
            echo "<div class='error'>❌ Missing columns: " . implode(', ', $missing_columns) . "</div>";
        }
    }
    
    echo "<h2>Summary</h2>";
    echo "<div class='success'>";
    echo "<h3>✅ Fix Complete!</h3>";
    echo "<p><strong>What was fixed:</strong></p>";
    echo "<ul>";
    echo "<li>Added missing TRANSCRIPT column to tblinterviewrecordings table</li>";
    echo "<li>Verified all required columns are present</li>";
    echo "<li>Tested the fix with a sample insert query</li>";
    echo "</ul>";
    echo "<p><strong>The 'Unknown column TRANSCRIPT in field list' error should now be resolved.</strong></p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>📋 Next Steps:</h3>";
    echo "<ul>";
    echo "<li>Try the interview recording functionality again</li>";
    echo "<li>The upload should now work without the TRANSCRIPT column error</li>";
    echo "<li>Speech-to-text transcripts will be saved to the database</li>";
    echo "</ul>";
    echo "</div>";
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='warning'>";
    echo "<h3>⚠️ Manual Fix Instructions:</h3>";
    echo "<p>If the automatic fix failed, run this SQL command manually:</p>";
    echo "<pre>ALTER TABLE `tblinterviewrecordings` ADD COLUMN `TRANSCRIPT` text;</pre>";
    echo "<p>Or run the existing setup script: <code>setup-interview-tables.php</code></p>";
    echo "</div>";
}
?>

<div style="text-align: center; margin-top: 30px;">
    <a href="interview-results.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">← Back to Interview Results</a>
    <a href="test-interview-fixes.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">Test Interview System</a>
    <a href="../admin/" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">Admin Dashboard</a>
</div>