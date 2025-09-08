<?php
echo "<h2>🔧 Quick Admin System Test</h2>";

// Test 1: Check if admin files exist
echo "<h3>1. Admin Files Check</h3>";
$adminFiles = [
    'admin/index.php' => 'Admin Index',
    'admin/working_dashboard.php' => 'Working Dashboard',
    'admin/login.php' => 'Admin Login'
];

foreach ($adminFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ {$description} exists</p>";
    } else {
        echo "<p style='color: red;'>❌ {$description} missing</p>";
    }
}

// Test 2: Check if we can include the admin index
echo "<h3>2. Admin Index Test</h3>";
try {
    // Check if initialize.php exists
    if (file_exists('include/initialize.php')) {
        echo "<p style='color: green;'>✅ include/initialize.php exists</p>";
    } else {
        echo "<p style='color: red;'>❌ include/initialize.php missing</p>";
    }
    
    if (file_exists('include/config.php')) {
        echo "<p style='color: green;'>✅ include/config.php exists</p>";
    } else {
        echo "<p style='color: red;'>❌ include/config.php missing</p>";
    }
    
    if (file_exists('include/database.php')) {
        echo "<p style='color: green;'>✅ include/database.php exists</p>";
    } else {
        echo "<p style='color: red;'>❌ include/database.php missing</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>3. Quick Access Links</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='admin/login.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>🚀 Try Admin Login</a>";
echo "<a href='admin/' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>📊 Try Admin Dashboard</a>";
echo "</div>";

echo "<h3>4. What to Do Next</h3>";
echo "<ol>";
echo "<li><strong>Click the Admin Login button above</strong> - This should take you to the login page</li>";
echo "<li><strong>If login works:</strong> You'll be redirected to the dashboard</li>";
echo "<li><strong>If you get errors:</strong> Tell me exactly what error message you see</li>";
echo "<li><strong>If you get a blank page:</strong> Check your browser's developer console (F12)</li>";
echo "</ol>";

echo "<h3>5. Troubleshooting</h3>";
echo "<p>If you still have issues:</p>";
echo "<ul>";
echo "<li>Check if you see any error messages in the browser</li>";
echo "<li>Press F12 and check the Console tab for JavaScript errors</li>";
echo "<li>Check the Network tab to see if files are loading</li>";
echo "<li>Tell me exactly what you see and any error messages</li>";
echo "</ul>";
?> 