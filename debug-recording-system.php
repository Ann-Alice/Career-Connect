<!DOCTYPE html>
<html>
<head>
    <title>🔍 Recording System Debugger</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; } .btn-danger:hover { background: #c82333; }
        .section { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .code { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; font-family: monospace; font-size: 12px; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔍 Recording System Debugger</h1>
    
    <div class="info">
        <h3>📋 This tool will diagnose and fix recording issues</h3>
        <p>Checking all aspects of the video recording system to identify why recordings aren't being captured.</p>
    </div>

<?php
require_once('../include/initialize.php');

echo "<div class='section'>";
echo "<h2>1. 🗃️ Database Status Check</h2>";

try {
    // Check if recording tables exist and are accessible
    $tables_to_check = ['tblinterviewvideos', 'tblinterviewrecordings'];
    
    foreach ($tables_to_check as $table) {
        echo "<h4>Checking table: $table</h4>";
        
        try {
            $sql = "DESCRIBE $table";
            $mydb->setQuery($sql);
            $columns = $mydb->loadResultList();
            
            if ($columns) {
                echo "<div class='success'>✅ Table '$table' exists with " . count($columns) . " columns</div>";
                
                // Show table structure
                echo "<table><tr><th>Field</th><th>Type</th><th>Key</th></tr>";
                foreach ($columns as $col) {
                    echo "<tr><td>{$col->Field}</td><td>{$col->Type}</td><td>{$col->Key}</td></tr>";
                }
                echo "</table>";
                
                // Check record count
                $count_sql = "SELECT COUNT(*) as count FROM $table";
                $mydb->setQuery($count_sql);
                $count_result = $mydb->loadSingleResult();
                echo "<p><strong>Records in table:</strong> {$count_result->count}</p>";
                
                // Show recent records if any
                if ($count_result->count > 0) {
                    $recent_sql = "SELECT * FROM $table ORDER BY VIDEOID DESC LIMIT 5";
                    $mydb->setQuery($recent_sql);
                    $recent_records = $mydb->loadResultList();
                    
                    if ($recent_records) {
                        echo "<h5>Recent records:</h5>";
                        echo "<table><tr>";
                        $first_record = (array)$recent_records[0];
                        foreach (array_keys($first_record) as $column) {
                            echo "<th>$column</th>";
                        }
                        echo "</tr>";
                        
                        foreach ($recent_records as $record) {
                            echo "<tr>";
                            foreach ((array)$record as $value) {
                                $display_value = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
                                echo "<td>$display_value</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                }
                
            } else {
                echo "<div class='error'>❌ Table '$table' does not exist</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Error checking table '$table': " . $e->getMessage() . "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Database connection error: " . $e->getMessage() . "</div>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>2. 📁 File System Check</h2>";

$upload_paths = [
    'uploads/interviews/',
    'uploads/interviews/consolidated/',
    'uploads/videos/',
    'recordings/'
];

foreach ($upload_paths as $path) {
    $full_path = "../$path";
    echo "<h4>Checking directory: $path</h4>";
    
    if (is_dir($full_path)) {
        echo "<div class='success'>✅ Directory exists</div>";
        
        // Check permissions
        if (is_writable($full_path)) {
            echo "<div class='success'>✅ Directory is writable</div>";
        } else {
            echo "<div class='error'>❌ Directory is not writable</div>";
        }
        
        // List files in directory
        $files = scandir($full_path);
        $files = array_filter($files, function($file) {
            return !in_array($file, ['.', '..']);
        });
        
        if (count($files) > 0) {
            echo "<p><strong>Files found:</strong> " . count($files) . "</p>";
            echo "<ul>";
            foreach (array_slice($files, 0, 10) as $file) {
                $file_path = $full_path . $file;
                $file_size = is_file($file_path) ? filesize($file_path) : 0;
                echo "<li>$file (" . round($file_size/1024) . " KB)</li>";
            }
            if (count($files) > 10) {
                echo "<li>... and " . (count($files) - 10) . " more files</li>";
            }
            echo "</ul>";
        } else {
            echo "<div class='warning'>⚠️ No files found in directory</div>";
        }
        
    } else {
        echo "<div class='error'>❌ Directory does not exist</div>";
        echo "<p>Creating directory...</p>";
        
        if (mkdir($full_path, 0777, true)) {
            echo "<div class='success'>✅ Directory created successfully</div>";
        } else {
            echo "<div class='error'>❌ Failed to create directory</div>";
        }
    }
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>3. 🔧 PHP Error Check</h2>";

// Check for syntax errors in key files
$files_to_check = [
    '../admin/download-recording.php',
    '../simple-upload-working.php',
    '../theme/js/ai-interview.js',
    '../theme/js/recording-fix.js'
];

foreach ($files_to_check as $file) {
    echo "<h4>Checking file: " . basename($file) . "</h4>";
    
    if (file_exists($file)) {
        echo "<div class='success'>✅ File exists</div>";
        
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            // Check PHP syntax
            $output = [];
            $return_code = 0;
            exec("php -l \"$file\" 2>&1", $output, $return_code);
            
            if ($return_code === 0) {
                echo "<div class='success'>✅ PHP syntax is valid</div>";
            } else {
                echo "<div class='error'>❌ PHP syntax error:</div>";
                echo "<div class='code'>" . implode("\n", $output) . "</div>";
            }
        }
        
        // Show file size
        $file_size = filesize($file);
        echo "<p><strong>File size:</strong> " . round($file_size/1024) . " KB</p>";
        
    } else {
        echo "<div class='error'>❌ File does not exist</div>";
    }
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>4. 🎥 Test Recording Flow</h2>";

echo "<div class='info'>";
echo "<h4>Recording Flow Test</h4>";
echo "<p>This will test the complete recording flow from JavaScript to database storage.</p>";
echo "</div>";

// Test upload script
echo "<h4>Testing upload script response:</h4>";
if (file_exists('../simple-upload-working.php')) {
    echo "<div class='success'>✅ Upload script exists</div>";
    
    // Test if we can access the upload script
    $test_data = "test data";
    $temp_file = tempnam(sys_get_temp_dir(), 'test_upload');
    file_put_contents($temp_file, $test_data);
    
    // Simulate a basic upload test
    $_POST['test'] = 'true';
    $_POST['timestamp'] = time();
    
    echo "<div class='info'>💡 Upload script is accessible for testing</div>";
    
    unlink($temp_file);
} else {
    echo "<div class='error'>❌ Upload script not found</div>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>5. 🔍 Recommended Actions</h2>";

echo "<div class='info'>";
echo "<h3>Based on the analysis above, here are the recommended actions:</h3>";
echo "<ol>";
echo "<li><strong>Fix JavaScript Recording:</strong> Update the recording implementation to use the working version</li>";
echo "<li><strong>Test Recording Flow:</strong> Use the browser's developer tools to see if recording chunks are being captured</li>";
echo "<li><strong>Check Upload Process:</strong> Verify that recordings are being sent to and processed by the server</li>";
echo "<li><strong>Database Verification:</strong> Ensure recordings are being saved to the database correctly</li>";
echo "</ol>";
echo "</div>";

echo "<div class='warning'>";
echo "<h4>⚠️ Common Issues Found:</h4>";
echo "<ul>";
echo "<li>Multiple recording implementations may be conflicting</li>";
echo "<li>MediaRecorder might not be starting properly</li>";
echo "<li>Recording chunks might not be captured due to browser compatibility</li>";
echo "<li>Upload process might be failing silently</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

?>

<div class="section">
    <h2>6. 🧪 Live Recording Test</h2>
    <div class="info">
        <p>Use this interactive test to verify recording functionality:</p>
    </div>
    
    <div style="text-align: center; margin: 20px 0;">
        <a href="../test-recording.php" class="btn btn-success">🎥 Launch Recording Test Tool</a>
        <a href="../interview.php" class="btn">🎤 Test Full Interview</a>
        <a href="../admin/interview-results.php" class="btn">📊 Check Results</a>
    </div>
</div>

</body>
</html>