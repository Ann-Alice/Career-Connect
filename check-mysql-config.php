<?php
echo "<h1>🔍 MySQL Configuration Check</h1>";

// Check if we can find my.ini
$possible_paths = [
    'C:/xampp/mysql/bin/my.ini',
    'C:/xampp/mysql/my.ini',
    'C:/xampp/mysql/conf/my.ini',
    'C:/xampp/mysql/data/my.ini'
];

echo "<h2>Step 1: Looking for my.ini file</h2>";
$myini_found = false;
$myini_path = '';

foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        echo "✅ Found my.ini at: $path<br>";
        $myini_found = true;
        $myini_path = $path;
        break;
    } else {
        echo "❌ Not found: $path<br>";
    }
}

if (!$myini_found) {
    echo "<br><strong>🚨 CRITICAL:</strong> Could not find my.ini file!<br>";
    echo "This means MySQL configuration is not where expected.<br>";
    echo "<br><strong>SOLUTION:</strong><br>";
    echo "1. Open XAMPP Control Panel<br>";
    echo "2. Click 'Config' button next to MySQL<br>";
    echo "3. Note the exact path shown in the dropdown<br>";
    echo "4. Manually navigate to that file<br>";
    exit;
}

echo "<br><strong>✅ Using my.ini at:</strong> $myini_path<br>";

// Read and analyze my.ini
echo "<h2>Step 2: Analyzing my.ini content</h2>";

$content = file_get_contents($myini_path);
$lines = explode("\n", $content);

$mysqld_section = false;
$skip_grant_tables_found = false;
$line_number = 0;

foreach ($lines as $line) {
    $line_number++;
    $line = trim($line);
    
    if (strpos($line, '[mysqld]') === 0) {
        $mysqld_section = true;
        echo "✅ Found [mysqld] section at line $line_number<br>";
        continue;
    }
    
    if ($mysqld_section && strpos($line, 'skip-grant-tables') === 0) {
        $skip_grant_tables_found = true;
        echo "✅ Found skip-grant-tables at line $line_number: <code>$line</code><br>";
    }
    
    if ($mysqld_section && strpos($line, '[') === 0 && strpos($line, '[mysqld]') !== 0) {
        $mysqld_section = false;
        echo "ℹ️ [mysqld] section ends at line $line_number<br>";
    }
}

if (!$mysqld_section) {
    echo "❌ <strong>CRITICAL:</strong> [mysqld] section not found!<br>";
    echo "This file may not be a valid MySQL configuration file.<br>";
} else {
    if (!$skip_grant_tables_found) {
        echo "<br>❌ <strong>PROBLEM:</strong> skip-grant-tables line NOT found in [mysqld] section!<br>";
        echo "<br><strong>SOLUTION:</strong><br>";
        echo "1. Open the file: <code>$myini_path</code><br>";
        echo "2. Find the line: <code>[mysqld]</code><br>";
        echo "3. Add this line below it: <code>skip-grant-tables</code><br>";
        echo "4. Save the file (Ctrl+S)<br>";
        echo "5. Restart MySQL in XAMPP Control Panel<br>";
    } else {
        echo "<br>✅ <strong>Configuration looks correct!</strong><br>";
        echo "<br><strong>NEXT STEP:</strong><br>";
        echo "1. Go to XAMPP Control Panel<br>";
        echo "2. Click 'STOP' button next to MySQL<br>";
        echo "3. Wait until it turns RED<br>";
        echo "4. Click 'START' button next to MySQL<br>";
        echo "5. Wait until it turns GREEN<br>";
        echo "6. Test connection with: <code>mysql -u root</code><br>";
    }
}

// Check MySQL service status
echo "<h2>Step 3: MySQL Service Status</h2>";
if (function_exists('shell_exec')) {
    $output = shell_exec('netstat -an | findstr :4306');
    if ($output) {
        echo "✅ MySQL port 4306 is listening<br>";
        echo "Port info: <code>$output</code><br>";
    } else {
        echo "❌ MySQL port 4306 is NOT listening<br>";
        echo "MySQL service may not be running.<br>";
    }
} else {
    echo "⚠️ Cannot check service status (shell_exec disabled)<br>";
}

echo "<hr>";
echo "<h2>🔧 Manual Fix Instructions</h2>";
echo "<ol>";
echo "<li><strong>Open XAMPP Control Panel</strong></li>";
echo "<li><strong>Click 'Config' button next to MySQL</strong></li>";
echo "<li><strong>Select 'my.ini'</strong></li>";
echo "<li><strong>Find the line: <code>[mysqld]</code></strong></li>";
echo "<li><strong>Add this line below it: <code>skip-grant-tables</code></strong></li>";
echo "<li><strong>Save the file (Ctrl+S)</strong></li>";
echo "<li><strong>Go back to XAMPP Control Panel</strong></li>";
echo "<li><strong>Click 'STOP' button next to MySQL</strong></li>";
echo "<li><strong>Wait until it turns RED</strong></li>";
echo "<li><strong>Click 'START' button next to MySQL</strong></li>";
echo "<li><strong>Wait until it turns GREEN</strong></li>";
echo "<li><strong>Test with: <code>mysql -u root</code> in XAMPP Shell</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📞 Need Help?</h2>";
echo "<p>If you're still having issues:</p>";
echo "<ol>";
echo "<li>Make sure you're editing the correct my.ini file</li>";
echo "<li>Verify the line is added under [mysqld] section</li>";
echo "<li>Ensure you saved the file before closing</li>";
echo "<li>Confirm MySQL was completely restarted</li>";
echo "<li>Check XAMPP Control Panel for any error messages</li>";
echo "</ol>";
?> 