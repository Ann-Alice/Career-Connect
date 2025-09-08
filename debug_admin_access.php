<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>🔍 Debugging Admin Dashboard Access</h2>";

// Test 1: Check if we can access the admin directory
echo "<h3>1. File Access Test</h3>";
$adminFiles = [
    'admin/index.php' => 'Admin Index',
    'admin/login.php' => 'Admin Login',
    'admin/simple_working_dashboard.php' => 'Simple Dashboard',
    'admin/process.php' => 'Admin Process'
];

foreach ($adminFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ {$description} exists</p>";
        
        // Check if file is readable
        if (is_readable($file)) {
            echo "<p style='color: green;'>   └─ File is readable</p>";
        } else {
            echo "<p style='color: red;'>   └─ File is NOT readable</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ {$description} missing</p>";
    }
}

// Test 2: Check database connection
echo "<h3>2. Database Connection Test</h3>";
if ($mydb && $mydb->conn) {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Test if we can query the database
    $sql = "SELECT 1 as test";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    if ($result) {
        echo "<p style='color: green;'>✅ Database queries working</p>";
    } else {
        echo "<p style='color: red;'>❌ Database queries failing</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
}

// Test 3: Check admin user
echo "<h3>3. Admin User Test</h3>";
if ($mydb && $mydb->conn) {
    $sql = "SELECT * FROM tblusers WHERE USERNAME = 'admin' AND ROLE = 'Administrator'";
    $mydb->setQuery($sql);
    $adminUser = $mydb->loadSingleResult();
    
    if ($adminUser) {
        echo "<p style='color: green;'>✅ Admin user exists</p>";
        echo "<p><strong>Username:</strong> {$adminUser->USERNAME}</p>";
        echo "<p><strong>Full Name:</strong> {$adminUser->FULLNAME}</p>";
        echo "<p><strong>Role:</strong> {$adminUser->ROLE}</p>";
    } else {
        echo "<p style='color: red;'>❌ Admin user not found</p>";
    }
}

// Test 4: Check session status
echo "<h3>4. Session Status Test</h3>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✅ Session is active</p>";
    echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
    
    if (isset($_SESSION['ADMIN_USERID'])) {
        echo "<p style='color: green;'>✅ Admin session found</p>";
        echo "<p><strong>Admin User ID:</strong> " . $_SESSION['ADMIN_USERID'] . "</p>";
        echo "<p><strong>Admin Username:</strong> " . ($_SESSION['ADMIN_USERNAME'] ?? 'Not set') . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No admin session found</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Session is not active</p>";
}

// Test 5: Check web_root constant
echo "<h3>5. Configuration Test</h3>";
if (defined('web_root')) {
    echo "<p style='color: green;'>✅ web_root constant defined: " . web_root . "</p>";
} else {
    echo "<p style='color: red;'>❌ web_root constant not defined</p>";
}

// Test 6: Try to include admin files
echo "<h3>6. File Inclusion Test</h3>";
try {
    // Test if we can include the admin index
    ob_start();
    include('admin/index.php');
    $output = ob_get_clean();
    
    if ($output) {
        echo "<p style='color: green;'>✅ Admin index.php loads successfully</p>";
        echo "<p><strong>Output length:</strong> " . strlen($output) . " characters</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Admin index.php loads but produces no output</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error including admin index.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal error including admin index.php: " . $e->getMessage() . "</p>";
}

// Test 7: Check for any PHP errors
echo "<h3>7. PHP Error Check</h3>";
$error_log = error_get_last();
if ($error_log) {
    echo "<p style='color: red;'>❌ PHP Error detected:</p>";
    echo "<pre>" . print_r($error_log, true) . "</pre>";
} else {
    echo "<p style='color: green;'>✅ No PHP errors detected</p>";
}

echo "<h3>8. Quick Access Links</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='admin/login.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>🚀 Try Admin Login</a>";
echo "<a href='admin/' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>📊 Try Admin Dashboard</a>";
echo "<a href='admin/index.php' style='background: #ffc107; color: #333; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>🔧 Direct Admin Index</a>";
echo "</div>";

echo "<h3>9. Troubleshooting Steps</h3>";
echo "<ol>";
echo "<li><strong>Check the error messages above</strong> - Look for any ❌ red errors</li>";
echo "<li><strong>Try the direct links above</strong> - Click each button to see what happens</li>";
echo "<li><strong>Check your browser console</strong> - Press F12 and look for JavaScript errors</li>";
echo "<li><strong>Check your server error logs</strong> - Look for PHP/Apache errors</li>";
echo "</ol>";

echo "<h3>10. What to Tell Me</h3>";
echo "<p>If you still can't access the admin dashboard, please tell me:</p>";
echo "<ul>";
echo "<li>What happens when you click the buttons above?</li>";
echo "<li>Do you see any error messages?</li>";
echo "<li>Does the page load but show nothing?</li>";
echo "<li>Do you get a blank page?</li>";
echo "<li>Any specific error messages in the browser?</li>";
echo "</ul>";
?> 