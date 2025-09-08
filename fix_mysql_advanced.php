<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting advanced MySQL fix...\n";

// Function to check if a process is running
function isProcessRunning($processName) {
    $output = [];
    exec("tasklist /FI \"IMAGENAME eq $processName\"", $output);
    return count($output) > 1;
}

// Function to kill a process
function killProcess($processName) {
    exec("taskkill /F /IM $processName");
}

// Check and kill MySQL processes
if (isProcessRunning('mysqld.exe')) {
    echo "Found running MySQL processes. Attempting to kill...\n";
    killProcess('mysqld.exe');
    sleep(2);
}

// Check and kill MySQL service
exec('net stop MySQL 2>&1', $output, $returnVar);
echo "Stopped MySQL service\n";

// Define paths
$mysqlDir = 'C:/xampp/mysql';
$dataDir = $mysqlDir . '/data';
$backupDir = $mysqlDir . '/data_backup_' . date('Y-m-d_H-i-s');

// Create backup of data directory
if (is_dir($dataDir)) {
    echo "Creating backup of data directory...\n";
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0777, true);
    }
    $files = scandir($dataDir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            copy($dataDir . '/' . $file, $backupDir . '/' . $file);
        }
    }
    echo "Backup created at: $backupDir\n";
}

// Remove problematic files
$problemFiles = [
    $dataDir . '/ibdata1',
    $dataDir . '/ib_logfile0',
    $dataDir . '/ib_logfile1',
    $dataDir . '/mysql-bin.index',
    $dataDir . '/mysql-bin.000001'
];

foreach ($problemFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Removed: $file\n";
    }
}

// Create new my.ini with minimal settings
$myIni = <<<EOT
[mysqld]
# Basic Settings
port=4306
socket="C:/xampp/mysql/mysql.sock"
basedir="C:/xampp/mysql"
tmpdir="C:/xampp/tmp"
datadir="C:/xampp/mysql/data"
pid_file="mysql.pid"    
default_authentication_plugin=mysql_native_password

# Minimal InnoDB Settings
innodb_buffer_pool_size=256M
innodb_log_file_size=48M
innodb_log_buffer_size=16M
innodb_flush_log_at_trx_commit=1

# Character Set
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci

[client]
port=4306
socket="C:/xampp/mysql/mysql.sock"
default-character-set=utf8mb4
EOT;

file_put_contents($mysqlDir . '/bin/my.ini', $myIni);
echo "Created new minimal my.ini\n";

// Create necessary directories
$dirs = [
    $dataDir,
    $mysqlDir . '/tmp',
    $mysqlDir . '/logs'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir\n";
    }
    chmod($dir, 0777);
}

// Initialize MySQL data directory
echo "Initializing MySQL data directory...\n";
$initCmd = '"' . $mysqlDir . '/bin/mysqld.exe" --initialize-insecure --console';
exec($initCmd, $output, $returnVar);
echo "MySQL initialization completed with code: $returnVar\n";

echo "\nAdvanced MySQL fix completed!\n";
echo "Please follow these steps:\n";
echo "1. Open XAMPP Control Panel as Administrator\n";
echo "2. Click 'Start' for MySQL\n";
echo "3. If it fails, check the logs in XAMPP Control Panel\n";
echo "4. If you need to restore your data, it's backed up in: $backupDir\n";
?> 