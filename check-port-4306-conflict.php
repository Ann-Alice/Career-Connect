<?php
echo "<h1>🔍 Port 4306 Conflict Check</h1>";

echo "<h2>Step 1: Check What's Using Port 4306</h2>";

if (function_exists('shell_exec')) {
    // Check if port 4306 is already in use
    $output_4306 = shell_exec('netstat -an | findstr :4306');
    if ($output_4306) {
        echo "❌ Port 4306 is already in use by another service:<br>";
        echo "<code>$output_4306</code><br>";
        echo "<strong>This is why MySQL can't start on port 4306!</strong><br>";
    } else {
        echo "✅ Port 4306 is available (not in use)<br>";
    }
    
    echo "<br>";
    
    // Check what's using port 3306
    $output_3306 = shell_exec('netstat -an | findstr :3306');
    if ($output_3306) {
        echo "⚠️ Port 3306 is still in use:<br>";
        echo "<code>$output_3306</code><br>";
    } else {
        echo "✅ Port 3306 is available<br>";
    }
    
    echo "<br>";
    
    // Check for any services using ports in the 4300-4400 range
    $output_range = shell_exec('netstat -an | findstr ":43[0-9][0-9]"');
    if ($output_range) {
        echo "🔍 Services using ports 4300-4399:<br>";
        echo "<code>$output_range</code><br>";
    } else {
        echo "ℹ️ No services found using ports 4300-4399<br>";
    }
    
} else {
    echo "⚠️ Cannot check ports (shell_exec disabled)<br>";
}

echo "<hr>";
echo "<h2>Step 2: Check my.ini Configuration</h2>";
echo "<p><strong>Please check your my.ini file:</strong></p>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Click 'Config' button next to MySQL</li>";
echo "<li>Select 'my.ini'</li>";
echo "<li>Look for the line: <code>port=</code></li>";
echo "<li>What port number is shown?</li>";
echo "<li>Are there any syntax errors?</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>Step 3: Solutions</h2>";

echo "<h3>Option 1: Use a Different Available Port</h3>";
echo "<p>If port 4306 is already in use, try these alternatives:</p>";
echo "<ul>";
echo "<li><strong>Port 3307</strong> - Common alternative to 3306</li>";
echo "<li><strong>Port 3308</strong> - Another safe alternative</li>";
echo "<li><strong>Port 3309</strong> - Safe alternative</li>";
echo "<li><strong>Port 3310</strong> - Safe alternative</li>";
echo "</ul>";

echo "<h3>Option 2: Revert to Port 3306 (Recommended)</h3>";
echo "<p>Since port 3306 was working, let's go back to it:</p>";
echo "<ol>";
echo "<li>Open my.ini</li>";
echo "<li>Change <code>port=4306</code> back to <code>port=3306</code></li>";
echo "<li>Save the file</li>";
echo "<li>Start MySQL</li>";
echo "<li>Update application to use port 3306</li>";
echo "</ol>";

echo "<h3>Option 3: Find and Stop the Service Using Port 4306</h3>";
echo "<p>If you really want port 4306:</p>";
echo "<ol>";
echo "<li>Find what service is using port 4306</li>";
echo "<li>Stop that service</li>";
echo "<li>Then start MySQL on port 4306</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🎯 Recommended Action:</h2>";
echo "<p><strong>Use Option 2 (revert to port 3306)</strong> - it's the safest and most reliable.</p>";
echo "<p>Port 3306 is the standard MySQL port and was working before.</p>";

echo "<hr>";
echo "<h2>📞 What to Tell Me:</h2>";
echo "<p>After checking my.ini, tell me:</p>";
echo "<ul>";
echo "<li>What port number is shown in my.ini?</li>";
echo "<li>Are there any syntax errors?</li>";
echo "<li>What ports are already in use (from Step 1)?</li>";
echo "<li>Which option would you prefer?</li>";
echo "</ul>";

echo "<p><strong>Let's get MySQL running again first, then we can optimize the configuration!</strong></p>";
?> 