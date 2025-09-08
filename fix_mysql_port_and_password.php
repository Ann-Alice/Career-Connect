<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔧 Fix MySQL Port and Password Issues</h2>";

echo "<h3>Current Status:</h3>";
echo "<ul>";
echo "<li>✅ MySQL configured for port 4306</li>";
echo "<li>✅ Application configured for port 4306</li>";
echo "<li>❌ MySQL currently running on port 3306</li>";
echo "<li>❌ Password authentication failing</li>";
echo "</ul>";

echo "<h3>Step 1: Check Current Port Usage</h3>";
$output_3306 = shell_exec('netstat -an | findstr :3306');
$output_4306 = shell_exec('netstat -an | findstr :4306');

if ($output_3306) {
    echo "<p style='color: orange;'>⚠️ Port 3306 is listening (wrong port):</p>";
    echo "<pre>$output_3306</pre>";
} else {
    echo "<p style='color: green;'>✅ Port 3306 is not listening</p>";
}

if ($output_4306) {
    echo "<p style='color: green;'>✅ Port 4306 is listening (correct port)</p>";
    echo "<pre>$output_4306</pre>";
} else {
    echo "<p style='color: red;'>❌ Port 4306 is NOT listening</p>";
}

echo "<h3>Step 2: Required Actions</h3>";
echo "<p><strong>You need to:</strong></p>";
echo "<ol>";
echo "<li><strong>Stop MySQL completely</strong> in XAMPP Control Panel</li>";
echo "<li><strong>Wait for it to fully stop</strong> (no green background)</li>";
echo "<li><strong>Run the password reset script</strong></li>";
echo "<li><strong>Start MySQL again</strong> - it should use port 4306</li>";
echo "</ol>";

echo "<h3>Step 3: Password Reset Process</h3>";
echo "<p>Once MySQL is stopped, we'll:</p>";
echo "<ol>";
echo "<li>Run resetroot.bat to clear the password</li>";
echo "<li>Verify MySQL starts on port 4306</li>";
echo "<li>Test the connection</li>";
echo "</ol>";

echo "<h3>Step 4: Manual Steps Required</h3>";
echo "<p><strong>ACTION REQUIRED:</strong> You must manually stop MySQL first!</p>";
echo "<ol>";
echo "<li>Open <strong>XAMPP Control Panel</strong></li>";
echo "<li>Click <strong>'Stop'</strong> next to MySQL</li>";
echo "<li>Wait until status shows <strong>'Stopped'</strong></li>";
echo "<li>Come back here and click <strong>'Proceed with Reset'</strong></li>";
echo "</ol>";

echo "<hr>";

// Check if MySQL is stopped
if (!$output_3306 && !$output_4306) {
    echo "<h3>🎉 MySQL is Stopped - Proceeding with Reset</h3>";
    
    echo "<h4>Running Password Reset...</h4>";
    $reset_script = 'C:\xampp\mysql\resetroot.bat';
    if (file_exists($reset_script)) {
        echo "<p>Found resetroot.bat script</p>";
        
        // Try to execute it
        $output = [];
        $return_var = 0;
        exec('C:\xampp\mysql\resetroot.bat 2>&1', $output, $return_var);
        
        if ($return_var === 0) {
            echo "<p style='color: green;'>✅ resetroot.bat executed successfully</p>";
            echo "<p>Output:</p><pre>" . implode("\n", $output) . "</pre>";
            
            echo "<h4>Next Steps:</h4>";
            echo "<ol>";
            echo "<li>Go back to <strong>XAMPP Control Panel</strong></li>";
            echo "<li>Click <strong>'Start'</strong> next to MySQL</li>";
            echo "<li>Wait for MySQL to start</li>";
            echo "<li>Go to: <a href='test_connection.php'>test_connection.php</a> to verify</li>";
            echo "</ol>";
            
        } else {
            echo "<p style='color: red;'>❌ resetroot.bat failed with return code: $return_var</p>";
            echo "<p>Output:</p><pre>" . implode("\n", $output) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ resetroot.bat not found at: $reset_script</p>";
    }
} else {
    echo "<p style='color: red;'>❌ MySQL is still running. Please stop it in XAMPP Control Panel first!</p>";
    echo "<p><strong>Current Status:</strong></p>";
    if ($output_3306) echo "<p>• Port 3306: <span style='color: orange;'>ACTIVE (wrong port)</span></p>";
    if ($output_4306) echo "<p>• Port 4306: <span style='color: green;'>ACTIVE (correct port)</span></p>";
    
    echo "<p><strong>Action Required:</strong> Stop MySQL in XAMPP Control Panel, then refresh this page.</p>";
}

echo "<hr>";
echo "<p><strong>Summary:</strong></p>";
echo "<ul>";
echo "<li>MySQL needs to run on port 4306 (not 3306)</li>";
echo "<li>Root password needs to be reset to no password</li>";
echo "<li>Stop MySQL first, then run this reset process</li>";
echo "</ul>";
?> 