<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>MySQL Root Password Reset Tool</h2>";

// Check if MySQL is running
$conn = @mysqli_connect('localhost', 'root', '', null, 3306);
if ($conn) {
    echo "<p style='color: green;'>✅ SUCCESS: MySQL is accessible without password</p>";
    mysqli_close($conn);
    exit;
}

echo "<p style='color: orange;'>⚠️ MySQL requires authentication. Attempting to reset password...</p>";

// Try common XAMPP passwords
$passwords = ['', 'xampp', 'root', 'password', 'admin'];
$connected = false;

foreach ($passwords as $password) {
    echo "<p>Trying password: " . ($password === '' ? '(empty)' : $password) . "</p>";
    $conn = @mysqli_connect('localhost', 'root', $password, null, 3306);
    if ($conn) {
        echo "<p style='color: green;'>✅ SUCCESS: Connected with password: " . ($password === '' ? '(empty)' : $password) . "</p>";
        
        // Reset to no password
        $sql = "ALTER USER 'root'@'localhost' IDENTIFIED BY ''";
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>✅ SUCCESS: Root password reset to no password</p>";
            
            // Update config.php
            $configFile = 'include/config.php';
            $configContent = file_get_contents($configFile);
            $configContent = preg_replace(
                "/defined\('pass'\) \? null : define\(\"pass\", \"[^\"]*\"\);/",
                "defined('pass') ? null : define(\"pass\", \"\");",
                $configContent
            );
            
            if (file_put_contents($configFile, $configContent)) {
                echo "<p style='color: green;'>✅ SUCCESS: Updated config.php with empty password</p>";
            } else {
                echo "<p style='color: red;'>❌ FAILED: Could not update config.php</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ FAILED: Could not reset password: " . mysqli_error($conn) . "</p>";
        }
        
        mysqli_close($conn);
        $connected = true;
        break;
    } else {
        echo "<p style='color: red;'>❌ FAILED: " . mysqli_connect_error() . "</p>";
    }
}

if (!$connected) {
    echo "<h3 style='color: red;'>❌ All password attempts failed</h3>";
    echo "<p>You may need to:</p>";
    echo "<ol>";
    echo "<li>Stop MySQL service in XAMPP Control Panel</li>";
    echo "<li>Start MySQL in safe mode</li>";
    echo "<li>Reset the password manually</li>";
    echo "<li>Restart MySQL normally</li>";
    echo "</ol>";
    
    echo "<h3>Alternative: Manual Reset Steps</h3>";
    echo "<ol>";
    echo "<li>Stop MySQL in XAMPP Control Panel</li>";
    echo "<li>Open Command Prompt as Administrator</li>";
    echo "<li>Navigate to: C:\\xampp\\mysql\\bin</li>";
    echo "<li>Run: mysqld --skip-grant-tables --user=mysql</li>";
    echo "<li>Open another Command Prompt and run: mysql -u root</li>";
    echo "<li>In MySQL, run: UPDATE mysql.user SET authentication_string='' WHERE User='root';</li>";
    echo "<li>Run: FLUSH PRIVILEGES; EXIT;</li>";
    echo "<li>Stop the first MySQL process and restart normally in XAMPP</li>";
    echo "</ol>";
}
?> 