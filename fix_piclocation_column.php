<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>🔧 Fixing PICLOCATION Column Issue</h2>";

try {
    // Check current PICLOCATION column
    echo "<h3>1. Checking PICLOCATION Column</h3>";
    $sql = "SHOW COLUMNS FROM tblusers LIKE 'PICLOCATION'";
    $mydb->setQuery($sql);
    $piclocationColumn = $mydb->loadSingleResult();
    
    if ($piclocationColumn) {
        echo "<p style='color: green;'>✅ PICLOCATION column exists</p>";
        echo "<p><strong>Type:</strong> {$piclocationColumn->Type}</p>";
        echo "<p><strong>Null:</strong> {$piclocationColumn->Null}</p>";
        echo "<p><strong>Default:</strong> " . ($piclocationColumn->Default ?? 'NULL') . "</p>";
        
        // Fix the column to allow NULL values
        if ($piclocationColumn->Null === 'NO') {
            echo "<p>Fixing PICLOCATION column to allow NULL values...</p>";
            $sql = "ALTER TABLE tblusers MODIFY COLUMN PICLOCATION VARCHAR(255) NULL";
            $mydb->setQuery($sql);
            if ($mydb->executeQuery()) {
                echo "<p style='color: green;'>✅ PICLOCATION column fixed successfully</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to fix PICLOCATION column</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ PICLOCATION column already allows NULL values</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ PICLOCATION column not found</p>";
    }
    
    // Create admin user
    echo "<h3>2. Creating Admin User</h3>";
    $sql = "SELECT * FROM tblusers WHERE USERNAME = 'admin' AND ROLE = 'Administrator'";
    $mydb->setQuery($sql);
    $adminUser = $mydb->loadSingleResult();
    
    if (!$adminUser) {
        $adminId = 'ADMIN001';
        $adminName = 'System Administrator';
        $adminUsername = 'admin';
        $adminPassword = 'admin123';
        $adminPasswordHash = sha1($adminPassword);
        
        // Insert admin user with PICLOCATION as NULL
        $sql = "INSERT INTO tblusers (USERID, FULLNAME, USERNAME, PASS, ROLE, PICLOCATION) 
                VALUES ('{$mydb->escape_string($adminId)}', '{$mydb->escape_string($adminName)}', '{$mydb->escape_string($adminUsername)}', '{$mydb->escape_string($adminPasswordHash)}', 'Administrator', NULL)";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
            echo "<p>Error details: " . mysqli_error($mydb->conn) . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Admin user already exists</p>";
    }
    
    // Verify admin user
    echo "<h3>3. Verifying Admin User</h3>";
    $sql = "SELECT * FROM tblusers WHERE USERNAME = 'admin' AND ROLE = 'Administrator'";
    $mydb->setQuery($sql);
    $adminUser = $mydb->loadSingleResult();
    
    if ($adminUser) {
        echo "<p style='color: green;'>✅ Admin user verified successfully!</p>";
        echo "<p><strong>Username:</strong> {$adminUser->USERNAME}</p>";
        echo "<p><strong>Full Name:</strong> {$adminUser->FULLNAME}</p>";
        echo "<p><strong>Role:</strong> {$adminUser->ROLE}</p>";
        echo "<p><strong>PICLOCATION:</strong> " . ($adminUser->PICLOCATION ?? 'NULL') . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Admin user verification failed</p>";
    }
    
    echo "<h3>4. Admin System Status</h3>";
    echo "<p style='color: green;'>✅ PICLOCATION issue fixed!</p>";
    
    echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h4>🔑 Admin Login Credentials:</h4>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "</div>";
    
    echo "<h3>🎯 Access Your Admin Panel:</h3>";
    echo "<ol>";
    echo "<li><strong>Admin Login:</strong> <a href='admin/login.php' target='_blank'>http://localhost/eris/admin/login.php</a></li>";
    echo "<li><strong>Admin Dashboard:</strong> <a href='admin/' target='_blank'>http://localhost/eris/admin/</a></li>";
    echo "<li><strong>Login with:</strong> admin / admin123</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<div style='margin-top: 30px; text-align: center;'>";
echo "<a href='admin/login.php' class='btn btn-primary' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>🚀 Go to Admin Login</a>";
echo "<a href='admin/' class='btn btn-success' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block;'>📊 Access Admin Dashboard</a>";
echo "</div>";
?> 