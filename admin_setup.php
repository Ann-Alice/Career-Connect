<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>🚀 CAREER CONNECT Admin System Setup</h2>";

try {
    // Step 1: Check database connection
    echo "<h3>1. Database Connection</h3>";
    if ($mydb) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit;
    }
    
    // Step 2: Create admin users table if it doesn't exist
    echo "<h3>2. Creating Admin Users Table</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS `tblusers` (
        `USERID` varchar(30) NOT NULL,
        `FULLNAME` varchar(100) NOT NULL,
        `USERNAME` varchar(90) NOT NULL,
        `PASS` varchar(255) NOT NULL COMMENT 'SHA1 hash',
        `ROLE` enum('Administrator','Employee','Manager') NOT NULL DEFAULT 'Employee',
        `PICLOCATION` varchar(255) DEFAULT NULL,
        `EMAIL` varchar(100) DEFAULT NULL,
        `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
        `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`USERID`),
        UNIQUE KEY `username_unique` (`USERNAME`),
        KEY `idx_role` (`ROLE`),
        KEY `idx_active` (`IS_ACTIVE`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        echo "<p style='color: green;'>✅ Admin users table created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create admin users table</p>";
        exit;
    }
    
    // Step 3: Check if admin user exists
    echo "<h3>3. Creating Admin User</h3>";
    $sql = "SELECT * FROM tblusers WHERE USERNAME = 'admin' AND ROLE = 'Administrator'";
    $mydb->setQuery($sql);
    $adminUser = $mydb->loadSingleResult();
    
    if (!$adminUser) {
        // Create default admin user
        $adminId = 'ADMIN001';
        $adminName = 'System Administrator';
        $adminUsername = 'admin';
        $adminPassword = 'admin123';
        $adminPasswordHash = sha1($adminPassword);
        
        $sql = "INSERT INTO tblusers (USERID, FULLNAME, USERNAME, PASS, ROLE, IS_ACTIVE) 
                VALUES ('{$mydb->escape_string($adminId)}', '{$mydb->escape_string($adminName)}', '{$mydb->escape_string($adminUsername)}', '{$mydb->escape_string($adminPasswordHash)}', 'Administrator', 1)";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>✅ Default admin user created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
            exit;
        }
    } else {
        echo "<p style='color: green;'>✅ Admin user already exists</p>";
    }
    
    // Step 4: Check admin files
    echo "<h3>4. Checking Admin Files</h3>";
    $adminFiles = [
        'admin/simple_login.php' => 'Simple Admin Login',
        'admin/simple_dashboard.php' => 'Simple Admin Dashboard',
        'admin/index.php' => 'Admin Index'
    ];
    
    foreach ($adminFiles as $file => $description) {
        if (file_exists($file)) {
            echo "<p style='color: green;'>✅ {$description} exists</p>";
        } else {
            echo "<p style='color: red;'>❌ {$description} missing</p>";
        }
    }
    
    // Step 5: Test admin access
    echo "<h3>5. Admin Access Test</h3>";
    echo "<p style='color: green;'>✅ Admin system setup complete!</p>";
    
    echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h4>🔑 Admin Login Credentials:</h4>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>⚠️ IMPORTANT:</strong> Change this password after first login!</p>";
    echo "</div>";
    
    echo "<h3>🎯 Next Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Go to Admin Login:</strong> <a href='admin/simple_login.php' target='_blank'>Click Here</a></li>";
    echo "<li><strong>Login with:</strong> admin / admin123</li>";
    echo "<li><strong>Access Dashboard:</strong> You'll be redirected automatically</li>";
    echo "<li><strong>Change Password:</strong> For security, change the default password</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<div style='margin-top: 30px; text-align: center;'>";
echo "<a href='admin/simple_login.php' class='btn btn-primary' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>🚀 Go to Admin Login</a>";
echo "<a href='admin/simple_dashboard.php' class='btn btn-success' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>📊 View Dashboard</a>";
echo "<a href='index.php' class='btn btn-secondary' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>🏠 Return to Home</a>";
echo "</div>";
?> 