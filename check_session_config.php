<?php
// Check session configuration
echo "<h1>Session Configuration Check</h1>\n";

echo "<h2>PHP Session Settings</h2>\n";
echo "<ul>\n";
echo "<li>session.save_path: " . ini_get('session.save_path') . "</li>\n";
echo "<li>session.name: " . ini_get('session.name') . "</li>\n";
echo "<li>session.auto_start: " . ini_get('session.auto_start') . "</li>\n";
echo "<li>session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "</li>\n";
echo "<li>session.cookie_path: " . ini_get('session.cookie_path') . "</li>\n";
echo "<li>session.cookie_domain: " . ini_get('session.cookie_domain') . "</li>\n";
echo "<li>session.cookie_secure: " . ini_get('session.cookie_secure') . "</li>\n";
echo "<li>session.cookie_httponly: " . ini_get('session.cookie_httponly') . "</li>\n";
echo "<li>session.use_strict_mode: " . ini_get('session.use_strict_mode') . "</li>\n";
echo "<li>session.use_cookies: " . ini_get('session.use_cookies') . "</li>\n";
echo "<li>session.use_only_cookies: " . ini_get('session.use_only_cookies') . "</li>\n";
echo "<li>session.cache_limiter: " . ini_get('session.cache_limiter') . "</li>\n";
echo "<li>session.cache_expire: " . ini_get('session.cache_expire') . "</li>\n";
echo "<li>session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "</li>\n";
echo "</ul>\n";

echo "<h2>Current Session Status</h2>\n";
echo "<ul>\n";
echo "<li>Session ID: " . (session_id() ?: 'No active session') . "</li>\n";
echo "<li>Session Status: " . session_status() . " (" . 
    (session_status() == PHP_SESSION_DISABLED ? 'Disabled' : 
     (session_status() == PHP_SESSION_NONE ? 'None' : 
     'Active')) . ")</li>\n";
echo "</ul>\n";

if (session_status() == PHP_SESSION_NONE) {
    echo "<p>Starting session...</p>\n";
    session_start();
    echo "<p>Session started. Session ID: " . session_id() . "</p>\n";
}

echo "<h2>Session Variables</h2>\n";
echo "<pre>\n";
print_r($_SESSION);
echo "</pre>\n";

echo "<p>Configuration check completed.</p>\n";
?>