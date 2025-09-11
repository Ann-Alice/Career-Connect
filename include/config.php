<?php
defined('server') ? null : define("server", "localhost");
defined('user') ? null : define ("user", "root") ;
defined('pass') ? null : define("pass", "");  // No password after reset
defined('database_name') ? null : define("database_name", "erisdb") ;
defined('mysql_port') ? null : define("mysql_port", 4306);  // Changed back to 4306

// Performance and Cache Configuration
defined('CACHE_ENABLED') ? null : define("CACHE_ENABLED", true);
defined('APP_DEBUG') ? null : define("APP_DEBUG", false); // Changed back to false

// Email Configuration
defined('ADMIN_EMAIL') ? null : define("ADMIN_EMAIL", "admin@eris.com");
defined('ADMIN_NAME') ? null : define("ADMIN_NAME", "ERIS Admin");
defined('SMTP_HOST') ? null : define("SMTP_HOST", "sandbox.smtp.mailtrap.io");
defined('SMTP_PORT') ? null : define("SMTP_PORT", "2525");
defined('SMTP_USER') ? null : define("SMTP_USER", "060d15df426995");
defined('SMTP_PASS') ? null : define("SMTP_PASS", "e46939916ffabe");
defined('SMTP_ENCRYPTION') ? null : define("SMTP_ENCRYPTION", "tls");

// Hardcoded web_root for Career Connect directory structure
$web_root = '/Career Connect/Career-Connect/';
define('web_root', $web_root);

// Security headers
if (!headers_sent()) {
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}

// For debugging - comment out in production
// error_log("Document Root: " . $_SERVER['DOCUMENT_ROOT']);
// error_log("Project Root: " . dirname(dirname(__FILE__)));
// error_log("Web Root: " . web_root);