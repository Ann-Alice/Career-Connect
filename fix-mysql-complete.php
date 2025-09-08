<?php
echo "<h1>🔧 Complete MySQL & Database Fix</h1>";

// Step 1: Check current MySQL status
echo "<h2>Step 1: Current MySQL Status</h2>";
if (function_exists('shell_exec')) {
    $output = shell_exec('netstat -an | findstr :4306');
    if ($output) {
        echo "✅ MySQL is running on port 4306<br>";
    } else {
        echo "❌ MySQL is NOT running<br>";
    }
}

// Step 2: Try to connect with different methods
echo "<h2>Step 2: Connection Testing</h2>";

// Method 1: Try with socket
echo "<h3>Method 1: Socket Connection</h3>";
try {
    $conn1 = mysqli_connect('localhost', 'root', '', '', 4306, '/tmp/mysql.sock');
    if ($conn1) {
        echo "✅ SUCCESS: Socket connection<br>";
        mysqli_close($conn1);
    } else {
        echo "❌ FAILED: Socket connection - " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: Socket connection - " . $e->getMessage() . "<br>";
}

// Method 2: Try with TCP/IP
echo "<h3>Method 2: TCP/IP Connection</h3>";
try {
    $conn2 = mysqli_connect('127.0.0.1', 'root', '', '', 4306);
    if ($conn2) {
        echo "✅ SUCCESS: TCP/IP connection<br>";
        mysqli_close($conn2);
    } else {
        echo "❌ FAILED: TCP/IP connection - " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: TCP/IP connection - " . $e->getMessage() . "<br>";
}

// Method 3: Try with localhost
echo "<h3>Method 3: Localhost Connection</h3>";
try {
    $conn3 = mysqli_connect('localhost', 'root', '', '', 4306);
    if ($conn3) {
        echo "✅ SUCCESS: Localhost connection<br>";
        mysqli_close($conn3);
    } else {
        echo "❌ FAILED: Localhost connection - " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: Localhost connection - " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🔧 IMMEDIATE FIX REQUIRED:</h2>";
echo "<h3>You need to do this RIGHT NOW:</h3>";
echo "1. <strong>Open XAMPP Control Panel</strong><br>";
echo "2. <strong>Click 'Config' button next to MySQL</strong><br>";
echo "3. <strong>Select 'my.ini'</strong><br>";
echo "4. <strong>Find the [mysqld] section</strong><br>";
echo "5. <strong>Add this line below it:</strong><br>";
echo "<code>skip-grant-tables</code><br>";
echo "6. <strong>Save the file (Ctrl+S)</strong><br>";
echo "7. <strong>Click 'STOP' button next to MySQL</strong><br>";
echo "8. <strong>Wait until it turns RED</strong><br>";
echo "9. <strong>Click 'START' button next to MySQL</strong><br>";
echo "10. <strong>Wait until it turns GREEN</strong><br>";

echo "<hr>";
echo "<h2>📋 After MySQL Restart:</h2>";
echo "1. Test connection with: <code>mysql -u root</code> in XAMPP Shell<br>";
echo "2. If successful, run the SQL commands to reset password<br>";
echo "3. Remove skip-grant-tables and restart again<br>";

echo "<hr>";
echo "<h2>🚨 CRITICAL:</h2>";
echo "The 'skip-grant-tables' line MUST be in your my.ini file<br>";
echo "MySQL MUST be restarted after adding it<br>";
echo "This is the ONLY way to fix the current authentication issue<br>";
?> 