<?php
// Debug version of initialize.php to identify where the issue is occurring
echo "<!-- Starting debug_initialize.php -->\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<!-- Defining constants -->\n";
//define the core paths
//Define them as absolute paths to make sure that require_once works as expected

//DIRECTORY_SEPARATOR is a PHP Pre-defined constants:
//(\ for windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// Use current directory for command line, or document root for web
if (php_sapi_name() === 'cli') {
    defined('SITE_ROOT') ? null : define('SITE_ROOT', dirname(__DIR__));
} else {
    // Fixed the path to correctly point to the project directory
    defined('SITE_ROOT') ? null : define('SITE_ROOT', dirname(__DIR__));
}

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'include');

echo "<!-- Loading config.php -->\n";
//load the database configuration first.
require_once(LIB_PATH.DS."config.php");

echo "<!-- Loading functions.php -->\n";
require_once(LIB_PATH.DS."functions.php");

echo "<!-- Loading session.php -->\n";
require_once(LIB_PATH.DS."session.php");

echo "<!-- Loading database.php -->\n";
// Load database.php FIRST and create $mydb instance
require_once(LIB_PATH.DS."database.php");

echo "<!-- Creating Database instance -->\n";
$mydb = new Database();

echo "<!-- Loading accounts.php -->\n";
// Now load other files that depend on $mydb
require_once(LIB_PATH.DS."accounts.php");

echo "<!-- Loading autonumbers.php -->\n";
require_once(LIB_PATH.DS."autonumbers.php");

echo "<!-- Loading companies.php -->\n";
require_once(LIB_PATH.DS."companies.php");

echo "<!-- Loading job.php -->\n";
require_once(LIB_PATH.DS."job.php");

echo "<!-- Loading employees.php -->\n";
require_once(LIB_PATH.DS."employees.php");

echo "<!-- Loading categories.php -->\n";
require_once(LIB_PATH.DS."categories.php");

echo "<!-- Loading applicant.php -->\n";
require_once(LIB_PATH.DS."applicant.php");

echo "<!-- Loading jobregistration.php -->\n";
require_once(LIB_PATH.DS."jobregistration.php");

echo "<!-- debug_initialize.php completed -->\n";
?>