<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting MySQL fix...\n";

// Create a temporary my.ini file
$myIni = <<<EOT
[mysqld]
default_authentication_plugin=mysql_native_password
EOT;

file_put_contents('C:/xampp/mysql/bin/my.ini', $myIni);

echo "Created temporary my.ini file\n";

// Stop MySQL service
echo "Stopping MySQL service...\n";
exec('net stop MySQL');

// Start MySQL service
echo "Starting MySQL service...\n";
exec('net start MySQL');

echo "MySQL service restarted. Please try accessing your application now.\n";
?> 