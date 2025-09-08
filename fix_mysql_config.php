<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting MySQL configuration fix...\n";

// Backup the original my.ini
$myIniPath = 'C:/xampp/mysql/bin/my.ini';
$backupPath = 'C:/xampp/mysql/bin/my.ini.backup';

if (file_exists($myIniPath)) {
    copy($myIniPath, $backupPath);
    echo "Created backup of my.ini\n";
}

// Create new my.ini with optimized settings
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

# Performance Settings
max_allowed_packet=16M
max_connections=100
thread_cache_size=8
query_cache_size=16M
table_open_cache=64
tmp_table_size=32M
thread_stack=256K
max_heap_table_size=32M

# InnoDB Settings
innodb_buffer_pool_size=256M
innodb_log_file_size=48M
innodb_log_buffer_size=16M
innodb_flush_log_at_trx_commit=1
innodb_lock_wait_timeout=50

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

if (file_put_contents($myIniPath, $myIni)) {
    echo "Created new my.ini with optimized settings\n";
} else {
    echo "Error creating my.ini\n";
}

// Create data directory if it doesn't exist
$dataDir = 'C:/xampp/mysql/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
    echo "Created data directory\n";
}

// Set proper permissions
chmod($dataDir, 0777);
echo "Set permissions on data directory\n";

echo "MySQL configuration fix completed!\n";
echo "Please restart XAMPP Control Panel as Administrator and start MySQL.\n";
?> 