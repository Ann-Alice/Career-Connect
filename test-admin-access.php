<?php
// Test admin access
echo "<h1>Admin Access Test</h1>";
echo "<p>If you can see this, the basic PHP is working.</p>";

// Check if include files exist
$include_files = [
    'include/initialize.php',
    'include/config.php',
    'include/database.php',
    'include/db_object.php',
    'include/session.php',
    'include/functions.php'
];

echo "<h2>Checking Required Files:</h2>";
foreach ($include_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file missing</p>";
    }
}

// Check if admin files exist
$admin_files = [
    'admin/index.php',
    'admin/login.php',
    'admin/home.php',
    'admin/theme/templates.php'
];

echo "<h2>Checking Admin Files:</h2>";
foreach ($admin_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file missing</p>";
    }
}

// Test web_root constant
echo "<h2>Testing Configuration:</h2>";
if (defined('web_root')) {
    echo "<p style='color: green;'>✓ web_root is defined: " . web_root . "</p>";
} else {
    echo "<p style='color: red;'>✗ web_root is not defined</p>";
}

echo "<h2>Quick Links:</h2>";
echo "<p><a href='index.php'>Main Site</a></p>";
echo "<p><a href='admin/login.php'>Admin Login</a></p>";
echo "<p><a href='admin/index.php'>Admin Dashboard</a></p>";
?> 