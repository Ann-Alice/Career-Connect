<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>🔧 Ensuring Admin User Exists</h2>";

try {
    // Check if admin user exists
    $sql = "SELECT * FROM tblusers WHERE USERNAME = 'admin' AND ROLE = 'Administrator'";
    $mydb->setQuery($sql);
    $adminUser = $mydb->loadSingleResult();
    
    if (!$adminUser) {
        // Create admin user
        $adminId = 'ADMIN001';
        $adminName = 'System Administrator';
        $adminUsername = 'admin';
        $adminPassword = 'admin123';
        $adminPasswordHash = sha1($adminPassword);
        
        $sql = "INSERT INTO tblusers (USERID, FULLNAME, USERNAME, PASS, ROLE, IS_ACTIVE) 
                VALUES ('{$mydb->escape_string($adminId)}', '{$mydb->escape_string($adminName)}', '{$mydb->escape_string($adminUsername)}', '{$mydb->escape_string($adminPasswordHash)}', 'Administrator', 1)";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Admin user already exists</p>";
    }
    
    echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h4>🔑 Admin Login Credentials:</h4>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "</div>";
    
    echo "<p><a href='admin/' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block;'>🚀 Go to Admin Panel</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 