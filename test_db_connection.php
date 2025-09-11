<?php
require_once('include/initialize.php');

// Test database connection and prepared statement methods
echo "Testing database connection...\n";

// Test if we can query the database
$sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result) {
    echo "Database connection successful. Total recordings: " . $result->count . "\n";
} else {
    echo "Database connection failed.\n";
    exit(1);
}

// Test prepared statement methods
echo "Testing prepared statement methods...\n";

$sql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = ? AND EXPIRY_DATE > NOW()";
$params = ['test_token'];
$types = 's';

// Try to use the prepareStatement method
$stmt = $mydb->prepareStatement($sql, $params, $types);
if ($stmt) {
    echo "prepareStatement method works correctly.\n";
    mysqli_stmt_close($stmt);
} else {
    echo "prepareStatement method failed.\n";
    echo "Error: " . $mydb->error_msg . "\n";
    exit(1);
}

echo "All tests passed.\n";
?>
<?php
echo "<h1>Database Connection Test</h1>";

// Include the configuration
require_once('include/config.php');

echo "<h2>Configuration Settings</h2>";
echo "<p>Server: " . server . "</p>";
echo "<p>User: " . user . "</p>";
echo "<p>Database: " . database_name . "</p>";
echo "<p>Port: " . mysql_port . "</p>";

echo "<h2>Testing Connection</h2>";

// Test connection
try {
    $conn = mysqli_connect(server, user, pass, "", mysql_port);
    
    if ($conn) {
        echo "<p style='color: green;'>✅ Successfully connected to MySQL!</p>";
        
        // Test database creation
        $sql = "CREATE DATABASE IF NOT EXISTS " . database_name;
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>✅ Database '" . database_name . "' is ready</p>";
            
            // Select the database
            if (mysqli_select_db($conn, database_name)) {
                echo "<p style='color: green;'>✅ Selected database '" . database_name . "'</p>";
                
                // Import the SQL file
                echo "<h2>Importing Database Schema</h2>";
                $sqlFile = file_get_contents('erisdb.sql');
                if ($sqlFile) {
                    // Split the SQL file into individual queries
                    $queries = explode(';', $sqlFile);
                    $success = 0;
                    $errors = 0;
                    
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if (!empty($query)) {
                            if (mysqli_query($conn, $query)) {
                                $success++;
                            } else {
                                $errors++;
                                echo "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
                            }
                        }
                    }
                    
                    echo "<p>Executed $success queries successfully</p>";
                    if ($errors > 0) {
                        echo "<p style='color: red;'>$errors queries failed</p>";
                    } else {
                        echo "<p style='color: green;'>✅ All queries executed successfully!</p>";
                    }
                } else {
                    echo "<p style='color: red;'>❌ Error reading SQL file</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Error selecting database: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Error creating database: " . mysqli_error($conn) . "</p>";
        }
        
        mysqli_close($conn);
    } else {
        echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<h2>Next Steps</h2>";
echo "<p>If the database was created successfully, you can now:</p>";
echo "<ol>";
echo "<li>Access the admin login page: <a href='admin/login.php'>Admin Login</a></li>";
echo "<li>Create an admin user by running: <a href='ensure_admin_user.php'>Ensure Admin User</a></li>";
echo "</ol>";
?>