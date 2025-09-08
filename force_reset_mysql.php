<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔄 Force MySQL Password Reset</h2>";

echo "<h3>Step 1: Check if MySQL is running</h3>";
$port_check = @fsockopen('localhost', 3306, $errno, $errstr, 5);
if ($port_check) {
    echo "<p style='color: orange;'>⚠️ MySQL is still running on port 3306</p>";
    echo "<p><strong>ACTION REQUIRED:</strong> You must stop MySQL in XAMPP Control Panel first!</p>";
    echo "<ol>";
    echo "<li>Open XAMPP Control Panel</li>";
    echo "<li>Click 'Stop' next to MySQL</li>";
    echo "<li>Wait for it to completely stop</li>";
    echo "<li>Refresh this page</li>";
    echo "</ol>";
    fclose($port_check);
    exit;
} else {
    echo "<p style='color: green;'>✅ MySQL is not running - good!</p>";
}

echo "<h3>Step 2: Attempting to reset MySQL password</h3>";

// Method 1: Try to run resetroot.bat
echo "<h4>Method 1: Running resetroot.bat</h4>";
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
    } else {
        echo "<p style='color: red;'>❌ resetroot.bat failed with return code: $return_var</p>";
        echo "<p>Output:</p><pre>" . implode("\n", $output) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ resetroot.bat not found at: $reset_script</p>";
}

echo "<h3>Step 3: Manual Reset Instructions</h3>";
echo "<p>If the automatic reset didn't work, follow these manual steps:</p>";

echo "<h4>Option A: Using XAMPP Control Panel Shell</h4>";
echo "<ol>";
echo "<li>In XAMPP Control Panel, click 'Shell'</li>";
echo "<li>Type: <code>cd C:\\xampp\\mysql</code></li>";
echo "<li>Type: <code>resetroot.bat</code></li>";
echo "<li>Wait for completion</li>";
echo "<li>Close shell and start MySQL</li>";
echo "</ol>";

echo "<h4>Option B: Manual SQL Reset</h4>";
echo "<ol>";
echo "<li>Stop MySQL in XAMPP Control Panel</li>";
echo "<li>Open Command Prompt as Administrator</li>";
echo "<li>Navigate to: <code>C:\\xampp\\mysql\\bin</code></li>";
echo "<li>Run: <code>mysqld --skip-grant-tables --user=mysql</code></li>";
echo "<li>Open another Command Prompt and run: <code>mysql -u root</code></li>";
echo "<li>In MySQL, run: <code>UPDATE mysql.user SET authentication_string='' WHERE User='root';</code></li>";
echo "<li>Run: <code>FLUSH PRIVILEGES; EXIT;</code></li>";
echo "<li>Stop the first MySQL process and restart normally in XAMPP</li>";
echo "</ol>";

echo "<h3>Step 4: After Reset</h3>";
echo "<p>Once you've completed the password reset:</p>";
echo "<ol>";
echo "<li>Start MySQL in XAMPP Control Panel</li>";
echo "<li>Go to: <a href='test_connection.php'>test_connection.php</a></li>";
echo "<li>Verify the connection works</li>";
echo "<li>Your main application should now work</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Current Status:</strong> MySQL password needs to be reset manually</p>";
echo "<p><strong>Next Action:</strong> Follow the steps above to reset MySQL password</p>";
?> 