<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>🧪 Testing Admin Login System</h2>";

try {
    // Test 1: Check if database connection works
    echo "<h3>1. Database Connection Test</h3>";
    if ($mydb) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit;
    }
    
    // Test 2: Check if tblusers table exists
    echo "<h3>2. Table Structure Test</h3>";
    $sql = "SHOW TABLES LIKE 'tblusers'";
    $mydb->setQuery($sql);
    $result = $mydb->executeQuery();
    
    if ($result && $mydb->num_rows($result) > 0) {
        echo "<p style='color: green;'>✅ Table tblusers exists</p>";
        
        // Check table structure
        $sql = "DESCRIBE tblusers";
        $mydb->setQuery($sql);
        $result = $mydb->executeQuery();
        
        if ($result) {
            echo "<p style='color: green;'>✅ Table structure check successful</p>";
            
            $fields = [];
            while ($row = $mydb->fetch_object($result)) {
                $fields[] = $row;
            }
            
            echo "<h4>Table Fields:</h4>";
            echo "<ul>";
            foreach ($fields as $field) {
                echo "<li><strong>{$field->Field}</strong> - {$field->Type} ({$field->Null})</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>❌ Table tblusers does not exist</p>";
        echo "<p>You need to run the fix_admin_login.php script first.</p>";
        exit;
    }
    
    // Test 3: Check if admin user exists
    echo "<h3>3. Admin User Test</h3>";
    $sql = "SELECT * FROM tblusers WHERE ROLE = 'Administrator' LIMIT 1";
    $mydb->setQuery($sql);
    $adminUser = $mydb->loadSingleResult();
    
    if ($adminUser) {
        echo "<p style='color: green;'>✅ Admin user found</p>";
        echo "<p><strong>Username:</strong> {$adminUser->USERNAME}</p>";
        echo "<p><strong>Full Name:</strong> {$adminUser->FULLNAME}</p>";
        echo "<p><strong>Role:</strong> {$adminUser->ROLE}</p>";
    } else {
        echo "<p style='color: red;'>❌ No admin user found</p>";
        echo "<p>You need to run the fix_admin_login.php script to create an admin user.</p>";
        exit;
    }
    
    // Test 4: Test login process
    echo "<h3>4. Login Process Test</h3>";
    
    // Simulate login attempt
    $username = $adminUser->USERNAME;
    $password = 'admin123'; // Default password
    $hashed_password = sha1($password);
    
    echo "<p>Testing login with username: <strong>{$username}</strong></p>";
    
    $sql = "SELECT * FROM tblusers WHERE USERNAME = ? AND PASS = ?";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    $result = $mydb->loadSingleResult();
    
    if ($result) {
        echo "<p style='color: green;'>✅ Login credentials are valid</p>";
        echo "<p>You can now log in to the admin panel.</p>";
    } else {
        echo "<p style='color: red;'>❌ Login credentials failed</p>";
        echo "<p>This might indicate a password mismatch or database issue.</p>";
    }
    
    // Test 5: Check admin files
    echo "<h3>5. Admin Files Test</h3>";
    $adminFiles = [
        'admin/login.php' => 'Admin Login Page',
        'admin/process.php' => 'Admin Login Process',
        'admin/index.php' => 'Admin Dashboard',
        'admin/home.php' => 'Admin Home Page'
    ];
    
    foreach ($adminFiles as $file => $description) {
        if (file_exists($file)) {
            echo "<p style='color: green;'>✅ {$description} exists</p>";
        } else {
            echo "<p style='color: red;'>❌ {$description} missing</p>";
        }
    }
    
    echo "<h3>🎯 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Go to <a href='admin/login.php' target='_blank'>Admin Login Page</a></li>";
    echo "<li>Use the credentials shown above</li>";
    echo "<li>If login fails, run <a href='fix_admin_login.php'>fix_admin_login.php</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='admin/login.php' class='btn btn-primary'>🚀 Go to Admin Login</a></p>";
echo "<p><a href='fix_admin_login.php' class='btn btn-warning'>🔧 Fix Admin Login</a></p>";
echo "<p><a href='index.php' class='btn btn-secondary'>🏠 Return to Home</a></p>";
?> 