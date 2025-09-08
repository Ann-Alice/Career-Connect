<?php
defined('server') ? null : define("server", "localhost");
defined('user') ? null : define ("user", "root") ;
defined('pass') ? null : define("pass", "");  // No password after reset
defined('database_name') ? null : define("database_name", "erisdb") ;
defined('mysql_port') ? null : define("mysql_port", 4306);  // MySQL port set to 4306 as required

// Email Configuration
defined('ADMIN_EMAIL') ? null : define("ADMIN_EMAIL", "admin@eris.com");
defined('ADMIN_NAME') ? null : define("ADMIN_NAME", "ERIS Admin");
defined('SMTP_HOST') ? null : define("SMTP_HOST", "sandbox.smtp.mailtrap.io");
defined('SMTP_PORT') ? null : define("SMTP_PORT", "2525");
defined('SMTP_USER') ? null : define("SMTP_USER", "060d15df426995");
defined('SMTP_PASS') ? null : define("SMTP_PASS", "e46939916ffabe");
defined('SMTP_ENCRYPTION') ? null : define("SMTP_ENCRYPTION", "tls");

// Calculate web_root dynamically
$this_file = str_replace('\\', '/', __FILE__);
$doc_root = $_SERVER['DOCUMENT_ROOT'];
$web_root = str_replace($doc_root, '', dirname(dirname($this_file)));
if (substr($web_root, -1) !== '/') {
    $web_root .= '/';
}
define('web_root', $web_root);

// For debugging
error_log("Document Root: " . $doc_root);
error_log("Web Root: " . $web_root);

// The following lines are commented out as web_root is now explicitly defined.
// $this_file = str_replace('\\', '/', __File__) ;
// $doc_root = $_SERVER['DOCUMENT_ROOT();
// $web_root = str_replace($doc_root, '', SITE_ROOT); 
// if (substr($web_root, -1) !== '/') {
//     $web_root .= '/';
// }
// define('server_root' , $server_root);

?>