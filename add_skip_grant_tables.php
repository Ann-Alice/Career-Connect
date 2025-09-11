<?php
echo "<h1>Adding skip-grant-tables to my.ini</h1>";

$myIniPath = 'C:/xampp/mysql/bin/my.ini';

// Check if file exists
if (!file_exists($myIniPath)) {
    die("<p style='color: red;'>Error: my.ini file not found at $myIniPath</p>");
}

// Read the file content
$content = file_get_contents($myIniPath);

// Check if skip-grant-tables is already present
if (strpos($content, 'skip-grant-tables') !== false) {
    echo "<p style='color: green;'>skip-grant-tables is already present in my.ini</p>";
} else {
    // Find the [mysqld] section and add skip-grant-tables after it
    $lines = explode("\n", $content);
    $newLines = [];
    $mysqldFound = false;
    
    foreach ($lines as $line) {
        $newLines[] = $line;
        
        // Check if this is the [mysqld] line
        if (trim($line) === '[mysqld]') {
            $mysqldFound = true;
            // Add skip-grant-tables on the next line
            $newLines[] = 'skip-grant-tables';
            echo "<p style='color: green;'>Added skip-grant-tables after [mysqld] section</p>";
        }
    }
    
    if (!$mysqldFound) {
        die("<p style='color: red;'>Error: [mysqld] section not found in my.ini</p>");
    }
    
    // Write the modified content back to the file
    $newContent = implode("\n", $newLines);
    if (file_put_contents($myIniPath, $newContent)) {
        echo "<p style='color: green;'>Successfully updated my.ini file</p>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ol>";
        echo "<li>Stop MySQL in XAMPP Control Panel</li>";
        echo "<li>Start MySQL in XAMPP Control Panel</li>";
        echo "<li>Test the connection</li>";
        echo "</ol>";
    } else {
        echo "<p style='color: red;'>Error: Failed to write to my.ini file</p>";
        echo "<p>Please make sure you have write permissions to the file.</p>";
    }
}
?>