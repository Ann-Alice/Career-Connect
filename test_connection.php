<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔍 Database Connection Test</h2>";

// Test different password combinations
$passwords = ['', 'xampp', 'root', 'password', 'admin'];

foreach ($passwords as $password) {
    echo "<h3>Testing password: " . ($password === '' ? '(empty)' : $password) . "</h3>";
    
    try {
        $conn = mysqli_connect('localhost', 'root', $password, null, 3306);
        if ($conn) {
            echo "<p style='color: green;'>✅ SUCCESS: Connected with password: " . ($password === '' ? '(empty)' : $password) . "</p>";
            
            // Test if we can create/select database
            $db_name = 'erisdb';
            $result = mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db_name`");
            if ($result) {
                echo "<p style='color: green;'>✅ SUCCESS: Can create/access database '$db_name'</p>";
                
                // Try to select the database
                if (mysqli_select_db($conn, $db_name)) {
                    echo "<p style='color: green;'>✅ SUCCESS: Can select database '$db_name'</p>";
                    
                    // Check tables
                    $tables_result = mysqli_query($conn, "SHOW TABLES");
                    if ($tables_result) {
                        $table_count = mysqli_num_rows($tables_result);
                        echo "<p style='color: green;'>✅ SUCCESS: Database has $table_count table(s)</p>";
                        
                        if ($table_count > 0) {
                            echo "<p>Tables:</p><ul>";
                            while ($row = mysqli_fetch_array($tables_result)) {
                                echo "<li>" . $row[0] . "</li>";
                            }
                            echo "</ul>";
                        }
                    }
                } else {
                    echo "<p style='color: orange;'>⚠️ WARNING: Cannot select database '$db_name'</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ FAILED: Cannot create/access database</p>";
            }
            
            mysqli_close($conn);
            
            // Update config.php with working password
            if ($password !== 'xampp') { // Only update if it's different
                $configFile = 'include/config.php';
                $configContent = file_get_contents($configFile);
                $configContent = preg_replace(
                    "/defined\('pass'\) \? null : define\(\"pass\", \"[^\"]*\"\);/",
                    "defined('pass') ? null : define(\"pass\", \"$password\");",
                    $configContent
                );
                
                if (file_put_contents($configFile, $configContent)) {
                    echo "<p style='color: green;'>✅ SUCCESS: Updated config.php with working password</p>";
                }
            }
            
            echo "<hr><p style='color: green; font-weight: bold;'>🎉 CONNECTION SUCCESSFUL! Your application should now work.</p>";
            break;
            
        } else {
            echo "<p style='color: red;'>❌ FAILED: " . mysqli_connect_error() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ EXCEPTION: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

// If no password worked
if (!isset($conn) || !$conn) {
    echo "<h3 style='color: red;'>❌ All password attempts failed</h3>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Stop MySQL in XAMPP Control Panel</li>";
    echo "<li>Run the resetroot.bat script</li>";
    echo "<li>Start MySQL again</li>";
    echo "<li>Test this script again</li>";
    echo "</ol>";
}
?> 