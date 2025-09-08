<?php
echo "<h2>🔍 MySQL Connection Test</h2>";

// Test 1: Try with empty password
echo "<h3>Test 1: Empty Password</h3>";
try {
    $conn1 = mysqli_connect('localhost', 'root', '');
    if ($conn1) {
        echo "✅ SUCCESS: Connected with empty password<br>";
        mysqli_close($conn1);
    } else {
        echo "❌ FAILED: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

// Test 2: Try with no password parameter
echo "<h3>Test 2: No Password Parameter</h3>";
try {
    $conn2 = mysqli_connect('localhost', 'root');
    if ($conn2) {
        echo "✅ SUCCESS: Connected with no password parameter<br>";
        mysqli_close($conn2);
    } else {
        echo "❌ FAILED: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

// Test 3: Check if MySQL service is running
echo "<h3>Test 3: Service Status</h3>";
if (function_exists('shell_exec')) {
    $output = shell_exec('netstat -an | findstr 4306');
    if ($output) {
        echo "✅ MySQL port 4306 is listening<br>";
        echo "Port info: " . $output . "<br>";
    } else {
        echo "❌ MySQL port 4306 is NOT listening<br>";
    }
} else {
    echo "⚠️ Cannot check service status (shell_exec disabled)<br>";
}

echo "<hr>";
echo "<h3>🔧 Next Steps:</h3>";
echo "1. Make sure 'skip-grant-tables' is in your my.ini file<br>";
echo "2. Restart MySQL in XAMPP Control Panel<br>";
echo "3. Try connecting with 'mysql -u root' in XAMPP Shell<br>";
?> 