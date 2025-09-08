<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔍 Debug Configuration Loading</h2>";

echo "<h3>Step 1: Check if config.php exists</h3>";
$config_file = 'include/config.php';
if (file_exists($config_file)) {
    echo "<p style='color: green;'>✅ Config file exists: $config_file</p>";
} else {
    echo "<p style='color: red;'>❌ Config file not found: $config_file</p>";
    exit;
}

echo "<h3>Step 2: Load config.php and check values</h3>";
require_once($config_file);

echo "<p><strong>Configuration values:</strong></p>";
echo "<ul>";
echo "<li><strong>server:</strong> " . (defined('server') ? server : 'NOT DEFINED') . "</li>";
echo "<li><strong>user:</strong> " . (defined('user') ? user : 'NOT DEFINED') . "</li>";
echo "<li><strong>pass:</strong> " . (defined('pass') ? (pass === '' ? '(empty)' : pass) : 'NOT DEFINED') . "</li>";
echo "<li><strong>database_name:</strong> " . (defined('database_name') ? database_name : 'NOT DEFINED') . "</li>";
echo "<li><strong>mysql_port:</strong> " . (defined('mysql_port') ? mysql_port : 'NOT DEFINED') . "</li>";
echo "</ul>";

echo "<h3>Step 3: Test database connection with loaded config</h3>";
if (defined('server') && defined('user') && defined('pass') && defined('mysql_port')) {
    echo "<p>Attempting connection with loaded config...</p>";
    
    try {
        $conn = mysqli_connect(server, user, pass, null, mysql_port);
        if ($conn) {
            echo "<p style='color: green;'>✅ SUCCESS: Connected using config values!</p>";
            echo "<p>Connection details:</p>";
            echo "<ul>";
            echo "<li>Server: " . server . "</li>";
            echo "<li>User: " . user . "</li>";
            echo "<li>Password: " . (pass === '' ? '(empty)' : pass) . "</li>";
            echo "<li>Port: " . mysql_port . "</li>";
            echo "</ul>";
            mysqli_close($conn);
        } else {
            echo "<p style='color: red;'>❌ FAILED: " . mysqli_connect_error() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ EXCEPTION: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ ERROR: Not all required constants are defined!</p>";
}

echo "<h3>Step 4: Check if database.php is loading config correctly</h3>";
if (file_exists('include/database.php')) {
    echo "<p>✅ database.php exists</p>";
    
    // Check if database.php has any hardcoded values
    $db_content = file_get_contents('include/database.php');
    if (strpos($db_content, '3306') !== false) {
        echo "<p style='color: orange;'>⚠️ WARNING: database.php contains hardcoded port 3306!</p>";
        echo "<p>This will override your config.php settings.</p>";
    } else {
        echo "<p style='color: green;'>✅ database.php doesn't contain hardcoded port 3306</p>";
    }
} else {
    echo "<p style='color: red;'>❌ database.php not found</p>";
}

echo "<h3>Step 5: Check current port usage</h3>";
$output_3306 = shell_exec('netstat -an | findstr :3306');
$output_4306 = shell_exec('netstat -an | findstr :4306');

if ($output_3306) {
    echo "<p style='color: orange;'>⚠️ Port 3306 is listening:</p>";
    echo "<pre>$output_3306</pre>";
} else {
    echo "<p style='color: green;'>✅ Port 3306 is not listening</p>";
}

if ($output_4306) {
    echo "<p style='color: green;'>✅ Port 4306 is listening:</p>";
    echo "<pre>$output_4306</pre>";
} else {
    echo "<p style='color: red;'>❌ Port 4306 is NOT listening</p>";
}

echo "<hr>";
echo "<p><strong>Summary:</strong></p>";
echo "<ul>";
echo "<li>Config file: " . (file_exists($config_file) ? 'EXISTS' : 'MISSING') . "</li>";
echo "<li>All constants defined: " . (defined('server') && defined('user') && defined('pass') && defined('mysql_name') && defined('mysql_port') ? 'YES' : 'NO') . "</li>";
echo "<li>Port 3306 active: " . ($output_3306 ? 'YES' : 'NO') . "</li>";
echo "<li>Port 4306 active: " . ($output_4306 ? 'YES' : 'NO') . "</li>";
echo "</ul>";
?> 