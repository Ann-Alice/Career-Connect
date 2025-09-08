<?php
//define the core paths
//Define them as absolute peths to make sure that require_once works as expected

//DIRECTORY_SEPARATOR is a PHP Pre-defined constants:
//(\ for windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// Use current directory for command line, or document root for web
if (php_sapi_name() === 'cli') {
    defined('SITE_ROOT') ? null : define('SITE_ROOT', dirname(__DIR__));
} else {
    defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'].DS.'eris');
}

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'include');

//load the database configuration first.
require_once(LIB_PATH.DS."config.php");
require_once(LIB_PATH.DS."functions.php");
require_once(LIB_PATH.DS."session.php");

// Load database.php FIRST and create $mydb instance
require_once(LIB_PATH.DS."database.php");
$mydb = new Database();

// Now load other files that depend on $mydb
require_once(LIB_PATH.DS."accounts.php");
require_once(LIB_PATH.DS."autonumbers.php");  
require_once(LIB_PATH.DS."companies.php");  
require_once(LIB_PATH.DS."job.php");  
require_once(LIB_PATH.DS."employees.php");  
require_once(LIB_PATH.DS."categories.php");  
require_once(LIB_PATH.DS."applicant.php");  
require_once(LIB_PATH.DS."jobregistration.php");  
?>