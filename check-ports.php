<?php
echo "<h1>🔍 Port and MySQL Status Check</h1>";

echo "<h2>Step 1: Check What Ports Are Listening</h2>";

if (function_exists('shell_exec')) {
    // Check port 4306 (default MySQL)
    $output_4306 = shell_exec('netstat -an | findstr :4306');
    if ($output_4306) {
        echo "✅ Port 4306 is listening:<br>";
        echo "<code>$output_4306</code><br>";
    } else {
        echo "❌ Port  is NOT listening<br>";
    }
    
    echo "<br>";
    
    // Check port 4306 (your mentioned port)
    $output_4306 = shell_exec('netstat -an | findstr :4306');
    if ($output_4306) {
        echo "✅ Port 4306 is listening:<br>";
        echo "<code>$output_4306</code><br>";
    } else {
        echo "❌ Port 4306 is NOT listening<br>";
    }
    
    echo "<br>";
    
    // Check for any MySQL-related ports
    $output_mysql = shell_exec('netstat -an | findstr mysql');
    if ($output_mysql) {
        echo "🔍 MySQL-related ports found:<br>";
        echo "<code>$output_mysql</code><br>";
    } else {
        echo "ℹ️ No MySQL-related ports found<br>";
    }
    
    echo "<br>";
    
    // Check for any ports in the 3000-5000 range (common for custom MySQL ports)
    $output_range = shell_exec('netstat -an | findstr ":3[0-9][0-9][0-9] :4[0-9][0-9][0-9]"');
    if ($output_range) {
        echo "🔍 Ports in 3000-5000 range:<br>";
        echo "<code>$output_range</code><br>";
    } else {
        echo "ℹ️ No ports found in 3000-5000 range<br>";
    }
    
} else {
    echo "⚠️ Cannot check ports (shell_exec disabled)<br>";
}

echo "<hr>";
echo "<h2>Step 2: Check XAMPP MySQL Status</h2>";
echo "<p><strong>Please check XAMPP Control Panel:</strong></p>";
echo "<ol>";
echo "<li>Is MySQL showing as GREEN (running)?</li>";
echo "<li>Is MySQL showing as RED (stopped)?</li>";
echo "<li>Are there any error messages in the logs?</li>";
echo "<li>What port number is shown in the MySQL row?</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>Step 3: Check MySQL Configuration</h2>";
echo "<p><strong>Check your my.ini file:</strong></p>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Click 'Config' button next to MySQL</li>";
echo "<li>Select 'my.ini'</li>";
echo "<li>Look for a line that says: <code>port=</code></li>";
echo "<li>What port number is shown?</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>Step 4: Test Different Connection Methods</h2>";

// Test connection on port 4306
echo "<h3>Test 1: Port 4306 (Default)</h3>";
try {
    $conn1 = mysqli_connect('localhost', 'root', '', null, 4306);
    if ($conn1) {
        echo "✅ SUCCESS: Connected on port 3306!<br>";
        mysqli_close($conn1);
    } else {
        echo "❌ FAILED on port 3306: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION on port 3306: " . $e->getMessage() . "<br>";
}

// Test connection on port 4306
echo "<h3>Test 2: Port 4306 (Your Mentioned Port)</h3>";
try {
    $conn2 = mysqli_connect('localhost', 'root', '', null, 4306);
    if ($conn2) {
        echo "✅ SUCCESS: Connected on port 4306!<br>";
        mysqli_close($conn2);
    } else {
        echo "❌ FAILED on port 4306: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION on port 4306: " . $e->getMessage() . "<br>";
}

// Test connection without specifying port
echo "<h3>Test 3: No Port Specified (Uses Default)</h3>";
try {
    $conn3 = mysqli_connect('localhost', 'root', '');
    if ($conn3) {
        echo "✅ SUCCESS: Connected without specifying port!<br>";
        mysqli_close($conn3);
    } else {
        echo "❌ FAILED without port: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION without port: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🎯 Next Steps:</h2>";
echo "<p>Based on what you see above:</p>";
echo "<ol>";
echo "<li><strong>If any connection works:</strong> We know the correct port and can proceed</li>";
echo "<li><strong>If no connections work:</strong> MySQL is not running or has configuration issues</li>";
echo "<li><strong>Check XAMPP Control Panel:</strong> This will tell us the real status</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📞 What to Tell Me:</h2>";
echo "<p>After running this script, tell me:</p>";
echo "<ul>";
echo "<li>What ports are listening (from Step 1)</li>";
echo "<li>What XAMPP Control Panel shows for MySQL</li>";
echo "<li>What port number is in my.ini (if any)</li>";
echo "<li>Which connection tests worked (if any)</li>";
echo "</ul>";

echo "<p><strong>This will give us the complete picture of what's actually happening!</strong></p>";
?> 