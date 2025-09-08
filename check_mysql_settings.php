<?php
require_once("include/config.php");

// Direct database connection
$conn = mysqli_connect(server, user, pass, database_name, mysql_port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully\n";

// Check MySQL settings
$settings = [
    'sql_mode',
    'innodb_strict_mode',
    'sql_safe_updates'
];

foreach ($settings as $setting) {
    $result = mysqli_query($conn, "SELECT @@{$setting} as value");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "$setting: " . $row['value'] . "\n";
    } else {
        echo "Error checking $setting: " . mysqli_error($conn) . "\n";
    }
}

// Check for any warnings
echo "\nChecking for warnings...\n";
$result = mysqli_query($conn, "SHOW WARNINGS");
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        print_r($row);
    }
}

mysqli_close($conn);
echo "Settings check complete!\n";
?>