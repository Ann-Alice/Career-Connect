<?php
echo "<h1>🔧 Force MySQL Restart Helper</h1>";

echo "<h2>Current MySQL Status</h2>";

// Check if MySQL is running
if (function_exists('shell_exec')) {
    $output = shell_exec('netstat -an | findstr :4306');
    if ($output) {
        echo "✅ MySQL port 4306 is listening<br>";
        echo "Port info: <code>$output</code><br>";
    } else {
        echo "❌ MySQL port 4306 is NOT listening<br>";
    }
}

echo "<hr>";
echo "<h2>🚨 CRITICAL: MySQL Restart Required</h2>";
echo "<p><strong>Problem:</strong> skip-grant-tables is in my.ini but MySQL is still requiring authentication.</p>";
echo "<p><strong>Solution:</strong> MySQL must be completely restarted for the configuration to take effect.</p>";

echo "<h3>🔧 Step-by-Step Restart Instructions:</h3>";
echo "<ol>";
echo "<li><strong>Open XAMPP Control Panel</strong></li>";
echo "<li><strong>Click 'STOP' button next to MySQL</strong></li>";
echo "<li><strong>Wait until the button turns RED</strong></li>";
echo "<li><strong>Wait 30 seconds (this is critical!)</strong></li>";
echo "<li><strong>Click 'START' button next to MySQL</strong></li>";
echo "<li><strong>Wait until the button turns GREEN</strong></li>";
echo "<li><strong>Test connection with: <code>mysql -u root</code> in XAMPP Shell</strong></li>";
echo "</ol>";

echo "<h3>⚠️ If Still Not Working:</h3>";
echo "<p>Sometimes MySQL processes don't stop completely. Try this:</p>";
echo "<ol>";
echo "<li><strong>Close XAMPP Control Panel completely</strong></li>";
echo "<li><strong>Wait 1 minute</strong></li>";
echo "<li><strong>Open XAMPP Control Panel again</strong></li>";
echo "<li><strong>Start MySQL</strong></li>";
echo "<li><strong>Test connection</strong></li>";
echo "</ol>";

echo "<h3>🔍 Verify Configuration:</h3>";
echo "<p>After restart, verify skip-grant-tables is active:</p>";
echo "<ol>";
echo "<li><strong>Click 'Shell' button next to MySQL</strong></li>";
echo "<li><strong>Type: <code>mysql -u root</code></strong></li>";
echo "<li><strong>You should see: <code>MariaDB [(none)]></code></strong></li>";
echo "<li><strong>If you see this, skip-grant-tables is working!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📋 Troubleshooting Checklist:</h2>";
echo "<ul>";
echo "<li>✅ skip-grant-tables line is in my.ini</li>";
echo "<li>✅ Line is under [mysqld] section</li>";
echo "<li>✅ File was saved</li>";
echo "<li>❌ MySQL was NOT properly restarted</li>";
echo "<li>❌ Configuration change not active</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>🎯 After Successful Restart:</h2>";
echo "<p>Once you get <code>MariaDB [(none)]></code> in the shell:</p>";
echo "<ol>";
echo "<li><strong>Test the connection again: <a href='test-mysql-direct.php'>Click here</a></strong></li>";
echo "<li><strong>If successful, run database setup: <a href='setup-optimized-database.php'>Click here</a></strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📞 Need More Help?</h2>";
echo "<p>If MySQL still requires authentication after restart:</p>";
echo "<ul>";
echo "<li>Check XAMPP Control Panel for error messages</li>";
echo "<li>Verify you're editing the correct my.ini file</li>";
echo "<li>Make sure there are no typos in skip-grant-tables</li>";
echo "<li>Try completely closing and reopening XAMPP</li>";
echo "</ul>";

echo "<p><strong>Remember:</strong> The key is that MySQL MUST be completely restarted for configuration changes to take effect!</p>";
?> 