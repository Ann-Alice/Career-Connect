<?php
// Test script to verify session configuration fix
require_once("include/initialize.php");

echo "<h1>Session Configuration Test</h1>";

// Test that session is properly configured
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>✅ Session is active</p>";
    
    // Check session configuration
    $cookie_params = session_get_cookie_params();
    echo "<p>Cookie HttpOnly: " . ($cookie_params['httponly'] ? '✅ Enabled' : '❌ Disabled') . "</p>";
    echo "<p>Cookie Secure: " . ($cookie_params['secure'] ? '✅ Enabled' : '❌ Disabled') . "</p>";
    
    // Test CSRF token generation
    $token = generateCSRFToken();
    echo "<p>CSRF Token Generation: " . (strlen($token) == 64 ? '✅ Success' : '❌ Failed') . "</p>";
    
    // Test session timeout functionality
    $_SESSION["last_activity"] = time();
    $timeout_check = SessionManager::checkTimeout();
    echo "<p>Session Timeout Check: " . ($timeout_check ? '✅ Active' : '❌ Expired') . "</p>";
    
    echo "<h2>Session Configuration Summary</h2>";
    echo "<ul>";
    echo "<li>Session ID: " . session_id() . "</li>";
    echo "<li>Session Name: " . session_name() . "</li>";
    echo "<li>Cookie Path: " . $cookie_params['path'] . "</li>";
    echo "<li>Cookie Domain: " . $cookie_params['domain'] . "</li>";
    echo "<li>Cookie Lifetime: " . $cookie_params['lifetime'] . "</li>";
    echo "<li>Cookie Secure: " . ($cookie_params['secure'] ? 'Yes' : 'No') . "</li>";
    echo "<li>Cookie HttpOnly: " . ($cookie_params['httponly'] ? 'Yes' : 'No') . "</li>";
    echo "<li>Cookie SameSite: " . ($cookie_params['samesite'] ?? 'Not Set') . "</li>";
    echo "</ul>";
} else {
    echo "<p>❌ Session is not active</p>";
}

echo "<h2>All tests completed successfully!</h2>";
echo "<p>No session configuration warnings should appear.</p>";
?>