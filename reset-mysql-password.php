<?php
echo "<h1>🔧 MySQL Password Reset - Safe Mode Method</h1>";

echo "<h2>🚨 Current Status</h2>";
echo "<p><strong>MySQL is completely down</strong> - this is actually good news!</p>";
echo "<p>We can now start fresh and reset the password properly.</p>";

echo "<hr>";
echo "<h2>🔧 Step 1: Clean Up Configuration</h2>";
echo "<ol>";
echo "<li><strong>Open XAMPP Control Panel</strong></li>";
echo "<li><strong>Click 'Config' button next to MySQL</strong></li>";
echo "<li><strong>Select 'my.ini'</strong></li>";
echo "<li><strong>Find and REMOVE ALL instances of:</strong></li>";
echo "<li><code>skip-grant-tables</code></li>";
echo "<li><code>skip-networking</code></li>";
echo "<li><strong>Save the file (Ctrl+S)</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🔧 Step 2: Try Normal Start</h2>";
echo "<ol>";
echo "<li><strong>In XAMPP Control Panel, click 'START' button next to MySQL</strong></li>";
echo "<li><strong>Wait for it to turn GREEN</strong></li>";
echo "<li><strong>Check for any error messages</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🔧 Step 3: Safe Mode Password Reset (If Normal Start Fails)</h2>";
echo "<p>If MySQL still won't start normally, use this safe mode method:</p>";

echo "<h3>Method A: Command Line Safe Mode</h3>";
echo "<ol>";
echo "<li><strong>Keep MySQL stopped in XAMPP Control Panel</strong></li>";
echo "<li><strong>Open Command Prompt as Administrator</strong></li>";
echo "<li><strong>Navigate to MySQL bin directory:</strong></li>";
echo "<li><code>cd C:\\xampp\\mysql\\bin</code></li>";
echo "<li><strong>Start MySQL in safe mode:</strong></li>";
echo "<li><code>mysqld --skip-grant-tables --user=mysql</code></li>";
echo "<li><strong>Keep this window open</strong></li>";
echo "<li><strong>Open another Command Prompt window</strong></li>";
echo "<li><strong>Navigate to MySQL bin directory:</strong></li>";
echo "<li><code>cd C:\\xampp\\mysql\\bin</code></li>";
echo "<li><strong>Connect to MySQL:</strong></li>";
echo "<li><code>mysql -u root</code></li>";
echo "<li><strong>You should see: <code>MariaDB [(none)]></code></strong></li>";
echo "<li><strong>Reset password:</strong></li>";
echo "<li><code>USE mysql;</code></li>";
echo "<li><code>UPDATE user SET authentication_string='' WHERE User='root';</code></li>";
echo "<li><code>FLUSH PRIVILEGES;</code></li>";
echo "<li><code>EXIT;</code></li>";
echo "<li><strong>Go back to first Command Prompt window</strong></li>";
echo "<li><strong>Stop MySQL safe mode (Ctrl+C)</strong></li>";
echo "<li><strong>Close both Command Prompt windows</strong></li>";
echo "<li><strong>Start MySQL normally in XAMPP Control Panel</strong></li>";
echo "</ol>";

echo "<h3>Method B: Complete MySQL Reset (Nuclear Option)</h3>";
echo "<p><strong>⚠️ WARNING: This will delete ALL MySQL data!</strong></p>";
echo "<ol>";
echo "<li><strong>Stop MySQL in XAMPP Control Panel</strong></li>";
echo "<li><strong>Navigate to: C:\\xampp\\mysql\\data\\</strong></li>";
echo "<li><strong>Make a backup copy of the entire 'data' folder</strong></li>";
echo "<li><strong>Delete the 'data' folder contents</strong></li>";
echo "<li><strong>Start MySQL in XAMPP Control Panel</strong></li>";
echo "<li><strong>This will recreate MySQL with no root password</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🔧 Step 4: Test Connection</h2>";
echo "<p>After successful password reset:</p>";
echo "<ol>";
echo "<li><strong>Test MySQL connection: <a href='test-mysql-direct.php'>Click here</a></strong></li>";
echo "<li><strong>If successful, set up database: <a href='setup-optimized-database.php'>Click here</a></strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📋 Current Status:</h2>";
echo "<ul>";
echo "<li>❌ MySQL completely stopped</li>";
echo "<li>❌ Configuration causing crashes</li>";
echo "<li>✅ We can start fresh</li>";
echo "<li>✅ Safe mode method available</li>";
echo "<li>✅ Complete reset option available</li>";
echo "</ul>";

echo "<h2>🎯 Recommended Action:</h2>";
echo "<p><strong>1. Clean up my.ini first</strong></p>";
echo "<p><strong>2. Try normal start</strong></p>";
echo "<p><strong>3. If that fails, use Method A (Safe Mode)</strong></p>";
echo "<p><strong>4. Only use Method B if everything else fails</strong></p>";

echo "<hr>";
echo "<h2>📞 Need Help?</h2>";
echo "<p>If you need assistance:</p>";
echo "<ul>";
echo "<li>Make sure you're running Command Prompt as Administrator</li>";
echo "<li>Check that all paths are correct</li>";
echo "<li>Verify MySQL is completely stopped before starting safe mode</li>";
echo "<li>Make backups before making any changes</li>";
echo "</ul>";

echo "<p><strong>Remember:</strong> Since MySQL is completely down, we have a clean slate to work with!</p>";
?>
