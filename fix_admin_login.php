<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>🔧 Fixing Admin Login System</h2>";

try {
    // Check if tblusers table exists and get its current structure
    $sql = "DESCRIBE tblusers";
    $mydb->setQuery($sql);
    $result = $mydb->executeQuery();
    
    if (!$result) {
        echo "<p style='color: orange;'>Table tblusers does not exist. Creating it...</p>";
        
        // Create the table with proper structure
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
            echo "<p style='color: green;'>✅ Table tblusers created successfully</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create tblusers table</p>";
            exit;
        }
    } else {
        echo "<p style='color: green;'>✅ Table tblusers exists</p>";
        
        // Check table structure
        $fields = [];
        while ($row = $mydb->fetch_object($result)) {
            $fields[] = $row;
        }
        
        echo "<h3>Current Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($fields as $field) {
            echo "<tr>";
            echo "<td>{$field->Field}</td>";
            echo "<td>{$field->Type}</td>";
            echo "<td>{$field->Null}</td>";
            echo "<td>{$field->Key}</td>";
            echo "<td>{$field->Default}</td>";
            echo "<td>{$field->Extra}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if required fields exist
        $requiredFields = ['USERID', 'FULLNAME', 'USERNAME', 'PASS', 'ROLE'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            $fieldExists = false;
            foreach ($fields as $f) {
                if ($f->Field === $field) {
                    $fieldExists = true;
                    break;
                }
            }
            if (!$fieldExists) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            echo "<p style='color: orange;'>Missing required fields: " . implode(', ', $missingFields) . "</p>";
            
            // Add missing fields
            foreach ($missingFields as $field) {
                $fieldDef = '';
                switch ($field) {
                    case 'USERID':
                        $fieldDef = "ADD COLUMN `USERID` varchar(30) NOT NULL FIRST";
                        break;
                    case 'FULLNAME':
                        $fieldDef = "ADD COLUMN `FULLNAME` varchar(100) NOT NULL AFTER `USERID`";
                        break;
                    case 'USERNAME':
                        $fieldDef = "ADD COLUMN `USERNAME` varchar(90) NOT NULL AFTER `FULLNAME`";
                        break;
                    case 'PASS':
                        $fieldDef = "ADD COLUMN `PASS` varchar(255) NOT NULL COMMENT 'SHA1 hash' AFTER `USERNAME`";
                        break;
                    case 'ROLE':
                        $fieldDef = "ADD COLUMN `ROLE` enum('Administrator','Employee','Manager') NOT NULL DEFAULT 'Employee' AFTER `PASS`";
                        break;
                }
                
                if ($fieldDef) {
                    $sql = "ALTER TABLE tblusers $fieldDef";
                    $mydb->setQuery($sql);
                    if ($mydb->executeQuery()) {
                        echo "<p style='color: green;'>✅ Added field: $field</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Failed to add field: $field</p>";
                    }
                }
            }
        }
    }
    
    // Check if admin user exists
    $sql = "SELECT * FROM tblusers WHERE USERNAME = 'admin' AND ROLE = 'Administrator'";
    $mydb->setQuery($sql);
    $adminUser = $mydb->loadSingleResult();
    
    if (!$adminUser) {
        echo "<p style='color: orange;'>No admin user found. Creating default admin account...</p>";
        
        // Create default admin user
        $adminId = 'ADMIN001';
        $adminName = 'System Administrator';
        $adminUsername = 'admin';
        $adminPassword = 'admin123'; // Default password - CHANGE THIS!
        $adminPasswordHash = sha1($adminPassword);
        
        $sql = "INSERT INTO tblusers (USERID, FULLNAME, USERNAME, PASS, ROLE, IS_ACTIVE) 
                VALUES (?, ?, ?, ?, 'Administrator', 1)";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>✅ Default admin user created successfully!</p>";
            echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
            echo "<h4>🔑 Default Admin Credentials:</h4>";
            echo "<p><strong>Username:</strong> admin</p>";
            echo "<p><strong>Password:</strong> admin123</p>";
            echo "<p><strong>⚠️ IMPORTANT:</strong> Change this password immediately after first login!</p>";
            echo "</div>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Admin user already exists</p>";
    }
    
    // Test the login process
    echo "<h3>🧪 Testing Admin Login Process</h3>";
    
    // Check if the admin directory exists
    $adminDir = 'admin/';
    if (is_dir($adminDir)) {
        echo "<p style='color: green;'>✅ Admin directory exists</p>";
        
        // Check if admin files exist
        $adminFiles = ['index.php', 'login.php', 'process.php'];
        foreach ($adminFiles as $file) {
            if (file_exists($adminDir . $file)) {
                echo "<p style='color: green;'>✅ $file exists</p>";
            } else {
                echo "<p style='color: red;'>❌ $file missing</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Admin directory not found</p>";
    }
    
    echo "<h3>🎯 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Go to <a href='admin/login.php' target='_blank'>Admin Login Page</a></li>";
    echo "<li>Use the default credentials: <strong>admin</strong> / <strong>admin123</strong></li>";
    echo "<li>Change the password immediately after login</li>";
    echo "<li>Access the admin dashboard</li>";
    echo "</ol>";
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h4>⚠️ Security Notice:</h4>";
    echo "<p>The default password 'admin123' is for initial setup only. Please change it immediately after your first login to secure your system.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='admin/login.php' class='btn btn-primary'>🚀 Go to Admin Login</a></p>";
echo "<p><a href='index.php' class='btn btn-secondary'>🏠 Return to Home</a></p>";
?> 