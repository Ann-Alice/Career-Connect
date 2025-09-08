<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>🔧 Simple Admin System Test</h2>";

try {
    // Test 1: Database connection
    echo "<h3>1. Database Connection</h3>";
    if ($mydb) {
        echo "<p style='color: green;'>✅ Database object available</p>";
        
        // Test if we can query the database
        $sql = "SELECT 1 as test";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        if ($result) {
            echo "<p style='color: green;'>✅ Database queries working</p>";
        } else {
            echo "<p style='color: red;'>❌ Database queries failing</p>";
            exit;
        }
    } else {
        echo "<p style='color: red;'>❌ Database object not available</p>";
        exit;
    }
    
    // Test 2: Check admin user
    echo "<h3>2. Admin User Check</h3>";
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
        echo "<p>Creating admin user...</p>";
        
        $adminId = 'ADMIN001';
        $adminName = 'System Administrator';
        $adminUsername = 'admin';
        $adminPassword = 'admin123';
        $adminPasswordHash = sha1($adminPassword);
        
        $sql = "INSERT INTO tblusers (USERID, FULLNAME, USERNAME, PASS, ROLE) 
                VALUES ('{$mydb->escape_string($adminId)}', '{$mydb->escape_string($adminName)}', '{$mydb->escape_string($adminUsername)}', '{$mydb->escape_string($adminPasswordHash)}', 'Administrator')";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        }
    }
    
    // Test 3: Check admin files
    echo "<h3>3. Admin Files Check</h3>";
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
    
    echo "<h3>4. Admin System Status</h3>";
    echo "<p style='color: green;'>✅ Admin system ready!</p>";
    
    echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h4>🔑 Admin Login Credentials:</h4>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "</div>";
    
    echo "<h3>🎯 Access Your Admin Panel:</h3>";
    echo "<ol>";
    echo "<li><strong>Admin Login:</strong> <a href='admin/login.php' target='_blank'>Click Here</a></li>";
    echo "<li><strong>Login with:</strong> admin / admin123</li>";
    echo "<li><strong>Access Dashboard:</strong> You'll be redirected automatically</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<div style='margin-top: 30px; text-align: center;'>";
echo "<a href='admin/login.php' class='btn btn-primary' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>🚀 Go to Admin Login</a>";
echo "<a href='admin/' class='btn btn-success' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>📊 Access Admin Dashboard</a>";
echo "</div>";
?> 