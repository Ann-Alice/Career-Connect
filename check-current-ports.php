<?php
echo "<h1>🔍 Current Port Status Check</h1>";

echo "<h2>Step 1: Check All MySQL-Related Ports</h2>";

if (function_exists('shell_exec')) {
    // Check port 3306 (standard MySQL)
    $output_3306 = shell_exec('netstat -an | findstr :3306');
    if ($output_3306) {
        echo "✅ Port 3306 is listening:<br>";
        echo "<code>$output_3306</code><br>";
    } else {
        echo "❌ Port 3306 is NOT listening<br>";
    }
    
    echo "<br>";
    
    // Check port 3307 (our target port)
    $output_3307 = shell_exec('netstat -an | findstr :3307');
    if ($output_3307) {
        echo "✅ Port 3307 is listening:<br>";
        echo "<code>$output_3307</code><br>";
    } else {
        echo "❌ Port 3307 is NOT listening<br>";
        echo "<strong>This is why you're getting 'actively refused' error!</strong><br>";
    }
    
    echo "<br>";
    
    // Check port 4306 (old port)
    $output_4306 = shell_exec('netstat -an | findstr :4306');
    if ($output_4306) {
        echo "⚠️ Port 4306 is still listening:<br>";
        echo "<code>$output_4306</code><br>";
    } else {
        echo "✅ Port 4306 is no longer listening<br>";
    }
    
    echo "<br>";
    
    // Check for any MySQL services
    $output_mysql = shell_exec('netstat -an | findstr :330');
    if ($output_mysql) {
        echo "🔍 MySQL-related ports found:<br>";
        echo "<code>$output_mysql</code><br>";
    } else {
        echo "ℹ️ No MySQL ports found in 3300 range<br>";
    }
    
} else {
    echo "⚠️ Cannot check ports (shell_exec disabled)<br>";
}

echo "<hr>";
echo "<h2>Step 2: Check MySQL Service Status</h2>";

// Check if MySQL process is running
if (function_exists('shell_exec')) {
    $output_process = shell_exec('tasklist | findstr mysqld');
    if ($output_process) {
        echo "✅ MySQL process is running:<br>";
        echo "<code>$output_process</code><br>";
    } else {
        echo "❌ MySQL process is NOT running<br>";
        echo "<strong>This explains why no ports are listening!</strong><br>";
    }
} else {
    echo "⚠️ Cannot check processes (shell_exec disabled)<br>";
}

echo "<hr>";
echo "<h2>Step 3: Current Application Configuration</h2>";

echo "<ul>";
echo "<li><strong>Config Port:</strong> " . (defined('mysql_port') ? mysql_port : 'NOT SET') . "</li>";
echo "<li><strong>Server:</strong> " . (defined('server') ? server : 'NOT SET') . "</li>";
echo "<li><strong>Username:</strong> " . (defined('user') ? user : 'NOT SET') . "</li>";
echo "<li><strong>Password:</strong> " . (defined('pass') ? (pass ? 'YES' : 'NO') : 'NOT SET') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>🎯 Problem Identified:</h2>";

if (defined('mysql_port') && mysql_port == 3307) {
    echo "<p><strong>🚨 ISSUE:</strong> Application is configured for port 3307, but MySQL is not running on that port.</p>";
    echo "<p><strong>CAUSE:</strong> Either:</p>";
    echo "<ul>";
    echo "<li>MySQL is not started</li>";
    echo "<li>my.ini still shows wrong port</li>";
    echo "<li>MySQL crashed when trying to start on port 3307</li>";
    echo "</ul>";
} else {
    echo "<p><strong>⚠️ WARNING:</strong> mysql_port constant is not set to 3307</p>";
}

echo "<hr>";
echo "<h2>🔧 Solutions:</h2>";

echo "<h3>Option 1: Use Port 3306 (Recommended)</h3>";
echo "<p>Since port 3306 is working, let's use that:</p>";
echo "<ol>";
echo "<li>Update include/config.php to use port 3306</li>";
echo "<li>Make sure my.ini shows port=3306</li>";
echo "<li>Start MySQL</li>";
echo "</ol>";

echo "<h3>Option 2: Fix Port 3307</h3>";
echo "<p>If you really want port 3307:</p>";
echo "<ol>";
echo "<li>Check my.ini shows port=3307</li>";
echo "<li>Make sure no other service uses port 3307</li>";
echo "<li>Start MySQL</li>";
echo "</ol>";

echo "<h3>Option 3: Check my.ini File</h3>";
echo "<p>Verify your my.ini configuration:</p>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Click 'Config' button next to MySQL</li>";
echo "<li>Select 'my.ini'</li>";
echo "<li>Look for the line: <code>port=</code></li>";
echo "<li>What port number is shown?</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📞 What to Tell Me:</h2>";
echo "<p>After checking my.ini, tell me:</p>";
echo "<ul>";
echo "<li>What port number is shown in my.ini?</li>";
echo "<li>Is MySQL running (green in XAMPP Control Panel)?</li>";
echo "<li>Which option would you prefer?</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>🎯 Recommended Action:</h2>";
echo "<p><strong>Use Option 1 (port 3306)</strong> - it's the most reliable and was working before.</p>";
echo "<p>Port 3306 is the standard MySQL port and rarely has conflicts.</p>";

echo "<p><strong>Let's get MySQL running first, then we can optimize the configuration!</strong></p>";
?> 