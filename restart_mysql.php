<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting MySQL restart process...\n";

// Kill any existing MySQL processes
exec("taskkill /F /IM mysqld.exe 2>&1", $output, $returnVar);
echo "Killed existing MySQL processes\n";

// Stop MySQL service
exec('net stop MySQL 2>&1', $output, $returnVar);
echo "Stopped MySQL service\n";

// Wait a moment
sleep(2);

// Define paths
$mysqlDir = 'C:/xampp/mysql';
$dataDir = $mysqlDir . '/data';

// Check and fix permissions
$dirs = [
    $dataDir,
    $mysqlDir . '/tmp',
    $mysqlDir . '/logs'
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0777);
        echo "Fixed permissions for: $dir\n";
    }
}

// Create minimal my.ini
$myIni = <<<EOT
[mysqld]
# Basic Settings
port=4306
socket="C:/xampp/mysql/mysql.sock"
basedir="C:/xampp/mysql"
tmpdir="C:/xampp/tmp"
datadir="C:/xampp/mysql/data"
pid_file="mysql.pid"

# Character Set
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci

[client]
port=4306
socket="C:/xampp/mysql/mysql.sock"
default-character-set=utf8mb4

[mysqldump]
max_allowed_packet=16M
default-character-set=utf8mb4
EOT;

file_put_contents($mysqlDir . '/bin/my.ini', $myIni);
echo "Created minimal my.ini\n";

// Start MySQL service
exec('net start MySQL 2>&1', $output, $returnVar);
echo "Started MySQL service\n";

// Wait for MySQL to start
sleep(5);

// Test connection
$conn = @mysqli_connect('localhost', 'root', '');
if ($conn) {
    echo "Successfully connected to MySQL!\n";
    mysqli_close($conn);
} else {
    echo "Failed to connect to MySQL. Error: " . mysqli_connect_error() . "\n";
    echo "Please try these steps:\n";
    echo "1. Open XAMPP Control Panel as Administrator\n";
    echo "2. Click 'Config' next to MySQL\n";
    echo "3. Click 'Uninstall' for MySQL\n";
    echo "4. After uninstallation completes, click 'Install' for MySQL\n";
    echo "5. Click 'Start' for MySQL\n";
}
?> 