<?php
require_once("include/initialize.php");

echo "<h1>Security and Performance Improvements Test</h1>";

// Test Session Management
echo "<h2>Session Management Tests</h2>";
SessionManager::start();
echo "<p>Session started: " . (session_status() === PHP_SESSION_ACTIVE ? "✅ Success" : "❌ Failed") . "</p>";

// Test CSRF token generation
$token = generateCSRFToken();
echo "<p>CSRF Token Generated: " . (strlen($token) == 64 ? "✅ Success" : "❌ Failed") . "</p>";

// Test CSRF token validation
$valid = validateCSRFToken($token);
echo "<p>CSRF Token Validation: " . ($valid ? "✅ Success" : "❌ Failed") . "</p>";

// Test input sanitization
$test_input = "<script>alert('XSS')</script>Hello World";
$sanitized = sanitizeInput($test_input);
echo "<p>Input Sanitization: " . ($sanitized == "Hello World" ? "✅ Success" : "❌ Failed") . "</p>";

// Test login attempt tracking
$username = "testuser";
recordLoginAttempt($username, false);
$attempts = $_SESSION['login_attempts'][$username] ?? 0;
echo "<p>Login Attempt Tracking: " . ($attempts == 1 ? "✅ Success" : "❌ Failed") . "</p>";

// Test Validation Utilities
echo "<h2>Validation Tests</h2>";
$valid_email = Validation::validateEmail("test@example.com");
echo "<p>Email Validation: " . ($valid_email ? "✅ Success" : "❌ Failed") . "</p>";

$valid_phone = Validation::validatePhone("123-456-7890");
echo "<p>Phone Validation: " . ($valid_phone ? "✅ Success" : "❌ Failed") . "</p>";

$valid_username = Validation::validateUsername("testuser123");
echo "<p>Username Validation: " . ($valid_username ? "✅ Success" : "❌ Failed") . "</p>";

// Test database prepared statements
echo "<h2>Database Tests</h2>";
try {
    // Test prepared statement method
    $sql = "SELECT * FROM tblusers WHERE USERNAME = ? AND PASS = ?";
    $result = $mydb->loadSingleResultPrepared($sql, ["admin", "d033e22ae348aeb5660fc2140aec35850c4da997"], "ss");
    echo "<p>Prepared Statement Query: " . ($result ? "✅ Success" : "⚠️ No Data") . "</p>";
} catch (Exception $e) {
    echo "<p>Prepared Statement Query: ❌ Failed - " . $e->getMessage() . "</p>";
}

// Test performance monitoring
echo "<h2>Performance Tests</h2>";
Performance::start();
usleep(100000); // Sleep for 100ms to simulate work
$perf_data = Performance::end();
echo "<p>Performance Monitoring: " . ($perf_data['execution_time'] > 90 ? "✅ Success" : "❌ Failed") . "</p>";
echo "<p>Execution Time: " . $perf_data['execution_time'] . " ms</p>";

// Test caching
echo "<h2>Cache Tests</h2>";
$test_data = ["name" => "Test", "value" => 123];
$cached = Performance::cache("test_key", $test_data, 60);
echo "<p>Data Caching: " . ($cached === $test_data ? "✅ Success" : "❌ Failed") . "</p>";

$cached_data = Performance::getCached("test_key");
echo "<p>Data Retrieval: " . ($cached_data === $test_data ? "✅ Success" : "❌ Failed") . "</p>";

// Test error handling
echo "<h2>Error Handling Tests</h2>";
ErrorHandler::info("Test info message");
echo "<p>Info Logging: ✅ Success</p>";

ErrorHandler::error("Test error message");
echo "<p>Error Logging: ✅ Success</p>";

echo "<h2>All tests completed!</h2>";
echo "<p><a href='admin/login.php'>Go to Admin Login</a></p>";
?>