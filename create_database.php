<?php
echo "<h1>Creating Database</h1>";

// Try to connect to MySQL with skip-grant-tables equivalent approach
$connected = false;
$conn = null;

// Try multiple approaches
$approaches = [
    // Direct connection without password
    function() { return new mysqli("localhost", "root", "", "", 3306); },
    // Connection with empty password
    function() { return new mysqli("localhost", "root", "", "", 3306); },
    // Connection with port 4306
    function() { return new mysqli("localhost", "root", "", "", 4306); },
];

foreach ($approaches as $index => $approach) {
    try {
        $conn = $approach();
        if ($conn->connect_error) {
            echo "<p>Approach " . ($index + 1) . " failed: " . $conn->connect_error . "</p>";
        } else {
            echo "<p style='color: green;'>Approach " . ($index + 1) . " connected successfully!</p>";
            $connected = true;
            break;
        }
    } catch (Exception $e) {
        echo "<p>Approach " . ($index + 1) . " failed: " . $e->getMessage() . "</p>";
    }
}

if (!$connected) {
    echo "<p style='color: red;'>All connection approaches failed.</p>";
    echo "<p>This is likely because MySQL needs to be restarted after adding 'skip-grant-tables' to the configuration.</p>";
    echo "<p>Please:</p>";
    echo "<ol>";
    echo "<li>Stop MySQL in XAMPP Control Panel</li>";
    echo "<li>Start MySQL in XAMPP Control Panel</li>";
    echo "<li>Run this script again</li>";
    echo "</ol>";
    exit;
}

// Create the database
try {
    $sql = "CREATE DATABASE IF NOT EXISTS erisdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "<p>Database 'erisdb' created successfully</p>";
    } else {
        echo "<p>Error creating database: " . $conn->error . "</p>";
    }
} catch (Exception $e) {
    echo "<p>Error creating database: " . $e->getMessage() . "</p>";
}

// Select the database
if (!$conn->select_db("erisdb")) {
    echo "<p style='color: red;'>Error selecting database: " . $conn->error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>Selected database 'erisdb' successfully</p>";
}

// Read and execute the SQL file
$sqlFile = file_get_contents('erisdb.sql');
if ($sqlFile === false) {
    die("<p>Error reading erisdb.sql file</p>");
}

// Split the SQL file into individual queries
$queries = explode(';', $sqlFile);

$success = 0;
$errors = 0;

// Execute each query
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        try {
            if ($conn->query($query)) {
                $success++;
            } else {
                $errors++;
                echo "<p style='color: red;'>Error executing query: " . $conn->error . "</p>";
            }
        } catch (Exception $e) {
            $errors++;
            echo "<p style='color: red;'>Exception executing query: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<p>Executed $success queries successfully</p>";
if ($errors > 0) {
    echo "<p style='color: red;'>$errors queries failed</p>";
} else {
    echo "<p style='color: green;'>All queries executed successfully!</p>";
}

// Create admin user
try {
    $sql = "INSERT INTO tblusers (USERID, FULLNAME, USERNAME, PASS, ROLE, PICLOCATION) VALUES ('00018', 'JANO ', 'janobe', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator', 'photos/Koala.jpg') ON DUPLICATE KEY UPDATE USERNAME=USERNAME";
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Admin user created/updated successfully</p>";
    } else {
        echo "<p style='color: red;'>Error creating admin user: " . $conn->error . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Exception creating admin user: " . $e->getMessage() . "</p>";
}

echo "<h2>Setup Complete!</h2>";
echo "<p>You can now access the admin login page at: <a href='admin/login.php'>Admin Login</a></p>";
echo "<p>Default credentials:</p>";
echo "<ul>";
echo "<li>Username: janobe</li>";
echo "<li>Password: admin</li>";
echo "</ul>";
?>