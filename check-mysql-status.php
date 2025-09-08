<?php
echo "<h1>🔍 Current MySQL Status Check</h1>";

echo "<h2>Step 1: Check if MySQL Port is Listening</h2>";

if (function_exists('shell_exec')) {
    // Check port 4306
    $output_4306 = shell_exec('netstat -an | findstr :4306');
    if ($output_4306) {
        echo "✅ Port 4306 is listening:<br>";
        echo "<code>$output_4306</code><br>";
    } else {
        echo "❌ Port 3306 is NOT listening<br>";
        echo "<strong>This means MySQL is not running!</strong><br>";
    }
    
    echo "<br>";
    
    // Check for any MySQL processes
    $output_processes = shell_exec('tasklist | findstr mysql');
    if ($output_processes) {
        echo "✅ MySQL processes found:<br>";
        echo "<code>$output_processes</code><br>";
    } else {
        echo "❌ No MySQL processes found<br>";
        echo "<strong>MySQL is not running!</strong><br>";
    }
    
} else {
    echo "⚠️ Cannot check system status (shell_exec disabled)<br>";
}

echo "<hr>";
echo "<h2>Step 2: Test Direct MySQL Connection</h2>";

// Test connection on port 3306
echo "<h3>Test 1: Port 3306</h3>";
try {
    $conn1 = mysqli_connect('localhost', 'root', '', null, 3306);
    if ($conn1) {
        echo "✅ SUCCESS: Connected on port 3306!<br>";
        echo "Server Info: " . mysqli_get_server_info($conn1) . "<br>";
        mysqli_close($conn1);
    } else {
        echo "❌ FAILED on port 3306: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION on port 3306: " . $e->getMessage() . "<br>";
}

// Test connection without specifying port
echo "<h3>Test 2: No Port Specified</h3>";
try {
    $conn2 = mysqli_connect('localhost', 'root', '');
    if ($conn2) {
        echo "✅ SUCCESS: Connected without specifying port!<br>";
        mysqli_close($conn2);
    } else {
        echo "❌ FAILED without port: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION without port: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>Step 3: Check XAMPP Configuration</h2>";
echo "<p><strong>Please check XAMPP Control Panel:</strong></p>";
echo "<ol>";
echo "<li>Is MySQL showing as GREEN (running)?</li>";
echo "<li>Is MySQL showing as RED (stopped)?</li>";
echo "<li>Are there any error messages?</li>";
echo "<li>What happens when you click START/STOP?</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>Step 4: Check phpMyAdmin Configuration</h2>";
echo "<p>The issue might be in phpMyAdmin's configuration:</p>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Click 'Config' button next to Apache</li>";
echo "<li>Select 'phpMyAdmin'</li>";
echo "<li>Look for MySQL connection settings</li>";
echo "<li>Check if it's trying to connect to the right port</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🎯 Possible Issues:</h2>";
echo "<ul>";
echo "<li><strong>MySQL stopped running:</strong> Check XAMPP Control Panel</li>";
echo "<li><strong>Port conflict:</strong> Another service might be using port 3306</li>";
echo "<li><strong>phpMyAdmin config:</strong> Wrong connection settings</li>";
echo "<li><strong>Firewall/antivirus:</strong> Blocking connections</li>";
echo "</ul>";

echo "<h2>🔧 Immediate Actions:</h2>";
echo "<ol>";
echo "<li><strong>Check XAMPP Control Panel MySQL status</strong></li>";
echo "<li><strong>If RED, click START</strong></li>";
echo "<li><strong>If GREEN but still not working, check logs</strong></li>";
echo "<li><strong>Try accessing phpMyAdmin again</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📞 What to Tell Me:</h2>";
echo "<p>After checking XAMPP Control Panel, tell me:</p>";
echo "<ul>";
echo "<li>What color is MySQL showing?</li>";
echo "<li>Are there any error messages?</li>";
echo "<li>What happens when you try to start/stop MySQL?</li>";
echo "<li>What do the MySQL logs show?</li>";
echo "</ul>";

echo "<p><strong>This will tell us exactly what's wrong with MySQL!</strong></p>";
?> 