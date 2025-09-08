<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>🔧 Fixing Users Table Schema</h2>";

try {
    // Check current table structure
    echo "<h3>1. Checking Current Table Structure</h3>";
    $sql = "DESCRIBE tblusers";
    $mydb->setQuery($sql);
    $result = $mydb->loadResultList();
    
    if ($result) {
        echo "<p style='color: green;'>✅ Table structure retrieved</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>{$row->Field}</td>";
            echo "<td>{$row->Type}</td>";
            echo "<td>{$row->Null}</td>";
            echo "<td>{$row->Key}</td>";
            echo "<td>{$row->Default}</td>";
            echo "<td>{$row->Extra}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Could not retrieve table structure</p>";
    }
    
    // Add missing columns
    echo "<h3>2. Adding Missing Columns</h3>";
    
    $missingColumns = [
        'IS_ACTIVE' => "ALTER TABLE tblusers ADD COLUMN IS_ACTIVE TINYINT(1) NOT NULL DEFAULT 1",
        'CREATED_AT' => "ALTER TABLE tblusers ADD COLUMN CREATED_AT TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
        'UPDATED_AT' => "ALTER TABLE tblusers ADD COLUMN UPDATED_AT TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    foreach ($missingColumns as $column => $sql) {
        // Check if column exists
        $checkSql = "SHOW COLUMNS FROM tblusers LIKE '{$column}'";
        $mydb->setQuery($checkSql);
        $exists = $mydb->loadSingleResult();
        
        if (!$exists) {
            echo "<p>Adding column: <strong>{$column}</strong></p>";
            $mydb->setQuery($sql);
            if ($mydb->executeQuery()) {
                echo "<p style='color: green;'>✅ Column {$column} added successfully</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to add column {$column}</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ Column {$column} already exists</p>";
        }
    }
    
    // Create admin user
    echo "<h3>3. Creating Admin User</h3>";
    $sql = "SELECT * FROM tblusers WHERE USERNAME = 'admin' AND ROLE = 'Administrator'";
    $mydb->setQuery($sql);
    $adminUser = $mydb->loadSingleResult();
    
    if (!$adminUser) {
        $adminId = 'ADMIN001';
        $adminName = 'System Administrator';
        $adminUsername = 'admin';
        $adminPassword = 'admin123';
        $adminPasswordHash = sha1($adminPassword);
        
        // Use the correct column names based on what exists
        $sql = "INSERT INTO tblusers (USERID, FULLNAME, USERNAME, PASS, ROLE) 
                VALUES ('{$mydb->escape_string($adminId)}', '{$mydb->escape_string($adminName)}', '{$mydb->escape_string($adminUsername)}', '{$mydb->escape_string($adminPasswordHash)}', 'Administrator')";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Admin user already exists</p>";
    }
    
    echo "<h3>4. Final Table Structure</h3>";
    $sql = "DESCRIBE tblusers";
    $mydb->setQuery($sql);
    $result = $mydb->loadResultList();
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>{$row->Field}</td>";
            echo "<td>{$row->Type}</td>";
            echo "<td>{$row->Null}</td>";
            echo "<td>{$row->Key}</td>";
            echo "<td>{$row->Default}</td>";
            echo "<td>{$row->Extra}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>5. Admin System Status</h3>";
    echo "<p style='color: green;'>✅ Users table fixed successfully!</p>";
    
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