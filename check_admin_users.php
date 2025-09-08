<?php
echo "<h2>👑 Checking Admin Users</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Check if tblusers table exists and has data
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'tblusers'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>✅ tblusers table exists</p>";
        
        // Check if there are any users
        $result = mysqli_query($conn, "SELECT * FROM tblusers");
        $user_count = mysqli_num_rows($result);
        echo "<p>Found $user_count users in tblusers table</p>";
        
        if ($user_count > 0) {
            echo "<h3>📋 Existing Users:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>USERID</th><th>UNAME</th><th>PASS</th><th>TYPE</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['USERID'] . "</td>";
                echo "<td>" . $row['UNAME'] . "</td>";
                echo "<td>" . substr($row['PASS'], 0, 10) . "...</td>";
                echo "<td>" . $row['TYPE'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Check if there are any admin users
            $result = mysqli_query($conn, "SELECT * FROM tblusers WHERE TYPE = 'Administrator'");
            $admin_count = mysqli_num_rows($result);
            
            if ($admin_count > 0) {
                echo "<p style='color: green;'>✅ Found $admin_count administrator users</p>";
                echo "<p>You can log in with any of these accounts</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ No administrator users found</p>";
                echo "<p>Creating default admin user...</p>";
                
                // Create default admin user
                $admin_username = 'admin';
                $admin_password = sha1('admin123');
                $sql = "INSERT INTO tblusers (UNAME, PASS, TYPE) VALUES ('$admin_username', '$admin_password', 'Administrator')";
                
                if (mysqli_query($conn, $sql)) {
                    echo "<p style='color: green;'>✅ Default admin user created</p>";
                    echo "<p><strong>Username:</strong> admin</p>";
                    echo "<p><strong>Password:</strong> admin123</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to create admin user: " . mysqli_error($conn) . "</p>";
                }
            }
        } else {
            echo "<p style='color: orange;'>⚠️ No users found in tblusers table</p>";
            echo "<p>Creating default admin user...</p>";
            
            // Create default admin user
            $admin_username = 'admin';
            $admin_password = sha1('admin123');
            $sql = "INSERT INTO tblusers (UNAME, PASS, TYPE) VALUES ('$admin_username', '$admin_password', 'Administrator')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<p style='color: green;'>✅ Default admin user created</p>";
                echo "<p><strong>Username:</strong> admin</p>";
                echo "<p><strong>Password:</strong> admin123</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to create admin user: " . mysqli_error($conn) . "</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ tblusers table not found</p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}

echo "<h3>🎯 Next Steps:</h3>";
echo "<p>1. Use the admin credentials above to log in</p>";
echo "<p>2. Access admin panel at: <a href='admin/login.php'>admin/login.php</a></p>";
echo "<p>3. Or go directly to: <a href='admin/index.php'>admin/index.php</a></p>";
?> 