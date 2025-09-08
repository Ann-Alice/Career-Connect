<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting MySQL restoration...\n";

// Define paths
$mysqlDir = 'C:/xampp/mysql';
$dataDir = $mysqlDir . '/data';
$backupDir = $mysqlDir . '/data_backup_' . date('Y-m-d_H-i-s');

// Stop MySQL if running
exec('net stop MySQL 2>&1', $output, $returnVar);
echo "Stopped MySQL service\n";

// Kill any running MySQL processes
exec("taskkill /F /IM mysqld.exe 2>&1", $output, $returnVar);
echo "Killed MySQL processes\n";

// Restore original my.ini
$originalIni = <<<EOT
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

// Save the original configuration
file_put_contents($mysqlDir . '/bin/my.ini', $originalIni);
echo "Restored original my.ini\n";

// Remove any backup directories created today
$today = date('Y-m-d');
$dirs = glob($mysqlDir . '/data_backup_' . $today . '*');
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        array_map('unlink', glob("$dir/*.*"));
        rmdir($dir);
        echo "Removed backup directory: $dir\n";
    }
}

// Remove any temporary files created today
$tempFiles = [
    $dataDir . '/ibdata1',
    $dataDir . '/ib_logfile0',
    $dataDir . '/ib_logfile1',
    $dataDir . '/mysql-bin.index',
    $dataDir . '/mysql-bin.000001'
];

foreach ($tempFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Removed temporary file: $file\n";
    }
}

echo "\nMySQL restoration completed!\n";
echo "Please follow these steps:\n";
echo "1. Open XAMPP Control Panel as Administrator\n";
echo "2. Click 'Start' for MySQL\n";
echo "3. If it fails, you may need to reinstall MySQL from XAMPP Control Panel\n";
?> 