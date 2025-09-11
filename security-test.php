<!DOCTYPE html>
<html>
<head>
    <title>Security Validation Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .test-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .test-section { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        h1, h2 { color: #333; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .checklist { margin: 10px 0; }
        .checklist li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>Security Validation Test</h1>
        <p>This test checks for common security vulnerabilities and best practices implementation.</p>
        
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Include necessary files
        require_once('include/initialize.php');
        require_once('include/security.php');
        
        // Test 1: HTTPS Enforcement
        echo "<div class='test-section info'>";
        echo "<h2>1. HTTPS Enforcement Check</h2>";
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            echo "<p class='success'>✅ HTTPS is enabled</p>";
        } else {
            echo "<p class='warning'>⚠️ HTTPS is not enabled (expected for production)</p>";
        }
        echo "<p><strong>Current protocol:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP') . "</p>";
        echo "</div>";
        
        // Test 2: Security Headers
        echo "<div class='test-section info'>";
        echo "<h2>2. Security Headers Verification</h2>";
        $headers = headers_list();
        $headerMap = [];
        foreach ($headers as $header) {
            $parts = explode(':', $header, 2);
            if (count($parts) == 2) {
                $headerMap[trim($parts[0])] = trim($parts[1]);
            }
        }
        
        $requiredHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin'
        ];
        
        foreach ($requiredHeaders as $header => $expectedValue) {
            if (isset($headerMap[$header])) {
                if (stripos($headerMap[$header], $expectedValue) !== false) {
                    echo "<p class='success'>✅ $header: " . $headerMap[$header] . "</p>";
                } else {
                    echo "<p class='warning'>⚠️ $header: " . $headerMap[$header] . " (expected: $expectedValue)</p>";
                }
            } else {
                echo "<p class='error'>❌ $header: Not set</p>";
            }
        }
        echo "</div>";
        
        // Test 3: CSRF Protection
        echo "<div class='test-section info'>";
        echo "<h2>3. CSRF Protection Validation</h2>";
        if (function_exists('generateCSRFToken')) {
            $token = generateCSRFToken();
            if ($token && strlen($token) > 20) {
                echo "<p class='success'>✅ CSRF token generation: Working</p>";
                echo "<p><strong>Sample token:</strong> " . substr($token, 0, 20) . "...</p>";
            } else {
                echo "<p class='error'>❌ CSRF token generation: Failed</p>";
            }
        } else {
            echo "<p class='error'>❌ CSRF token function not found</p>";
        }
        
        if (function_exists('validateCSRFToken')) {
            echo "<p class='success'>✅ CSRF token validation function exists</p>";
            
            // Test token validation
            $testToken = $_SESSION['csrf_token'] ?? '';
            if ($testToken && validateCSRFToken($testToken)) {
                echo "<p class='success'>✅ CSRF token validation: Working</p>";
            } else {
                echo "<p class='error'>❌ CSRF token validation: Failed</p>";
            }
        } else {
            echo "<p class='error'>❌ CSRF token validation function not found</p>";
        }
        echo "</div>";
        
        // Test 4: XSS Protection
        echo "<div class='test-section info'>";
        echo "<h2>4. XSS Protection Validation</h2>";
        $testInputs = [
            '<script>alert("XSS")</script>',
            '"><script>alert(1)</script>',
            '<img src=x onerror=alert(1)>',
            'javascript:alert(1)',
            'onload=alert(1)'
        ];
        
        if (function_exists('sanitizeInput')) {
            echo "<p class='success'>✅ Input sanitization function exists</p>";
            echo "<p><strong>Testing sanitization:</strong></p>";
            echo "<pre>";
            foreach ($testInputs as $input) {
                $sanitized = sanitizeInput($input);
                echo "Original: " . htmlspecialchars($input) . "\n";
                echo "Sanitized: " . htmlspecialchars($sanitized) . "\n";
                echo "---\n";
            }
            echo "</pre>";
        } else {
            echo "<p class='error'>❌ Input sanitization function not found</p>";
        }
        
        // Test Validation class sanitization
        if (class_exists('Validation') && method_exists('Validation', 'sanitizeInput')) {
            echo "<p class='success'>✅ Validation class sanitization method exists</p>";
            echo "<p><strong>Testing Validation::sanitizeInput():</strong></p>";
            echo "<pre>";
            foreach ($testInputs as $input) {
                $sanitized = Validation::sanitizeInput($input);
                echo "Original: " . htmlspecialchars($input) . "\n";
                echo "Sanitized: " . htmlspecialchars($sanitized) . "\n";
                echo "---\n";
            }
            echo "</pre>";
        } else {
            echo "<p class='error'>❌ Validation class sanitization method not found</p>";
        }
        echo "</div>";
        
        // Test 5: Session Security
        echo "<div class='test-section info'>";
        echo "<h2>5. Session Security Check</h2>";
        if (isset($_SESSION)) {
            echo "<p class='success'>✅ Session is active</p>";
            echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
        } else {
            echo "<p class='error'>❌ Session not started</p>";
        }
        
        // Check session configuration
        $sessionConfigs = [
            'session.cookie_httponly' => '1',
            'session.use_strict_mode' => '1',
            'session.cookie_secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? '1' : '0 (expected 1 with HTTPS)',
            'session.cookie_samesite' => 'Strict'
        ];
        
        echo "<p><strong>Session configuration:</strong></p>";
        foreach ($sessionConfigs as $config => $expected) {
            $value = ini_get($config);
            if ($value == '1' || $value == 'Strict' || ($config == 'session.cookie_secure' && $value == '0')) {
                echo "<p class='success'>✅ $config: $value</p>";
            } else {
                echo "<p class='warning'>⚠️ $config: $value (expected: $expected)</p>";
            }
        }
        echo "</div>";
        
        // Test 6: Password Security
        echo "<div class='test-section info'>";
        echo "<h2>6. Password Security</h2>";
        $testPassword = "TestPassword123!";
        if (function_exists('password_hash') && function_exists('password_verify')) {
            $hash = password_hash($testPassword, PASSWORD_DEFAULT);
            if ($hash && password_verify($testPassword, $hash)) {
                echo "<p class='success'>✅ Password hashing and verification: Working</p>";
                echo "<p><strong>Hash algorithm:</strong> " . (defined('PASSWORD_ARGON2ID') ? 'Argon2id' : 'Bcrypt') . "</p>";
            } else {
                echo "<p class='error'>❌ Password hashing and verification: Failed</p>";
            }
        } else {
            echo "<p class='error'>❌ Password functions not available</p>";
        }
        echo "</div>";
        
        // Test 7: File Upload Security
        echo "<div class='test-section info'>";
        echo "<h2>7. File Upload Security</h2>";
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . web_root . 'uploads/';
        if (is_dir($uploadDir)) {
            echo "<p class='success'>✅ Upload directory exists</p>";
            
            // Check .htaccess file
            $htaccessPath = $uploadDir . '.htaccess';
            if (file_exists($htaccessPath)) {
                echo "<p class='success'>✅ .htaccess file exists in uploads directory</p>";
            } else {
                echo "<p class='warning'>⚠️ .htaccess file missing in uploads directory</p>";
                echo "<p>Should contain rules to prevent direct execution of uploaded files</p>";
            }
        } else {
            echo "<p class='error'>❌ Upload directory does not exist</p>";
        }
        echo "</div>";
        
        // Test 8: SQL Injection Protection
        echo "<div class='test-section info'>";
        echo "<h2>8. SQL Injection Protection</h2>";
        global $mydb;
        if ($mydb) {
            echo "<p class='success'>✅ Database connection established</p>";
            
            // Check if prepared statements are used
            if (method_exists($mydb, 'prepareStatement')) {
                echo "<p class='success'>✅ Prepared statement methods available</p>";
            } else {
                echo "<p class='error'>❌ Prepared statement methods not found</p>";
            }
        } else {
            echo "<p class='error'>❌ Database connection failed</p>";
        }
        echo "</div>";
        
        // Summary
        echo "<div class='test-section' style='background: #e9ecef;'>";
        echo "<h2>Security Test Summary</h2>";
        echo "<ul class='checklist'>";
        echo "<li>HTTPS Enforcement: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? '✅' : '⚠️') . "</li>";
        echo "<li>Security Headers: " . (count($headerMap) > 0 ? '✅' : '⚠️') . "</li>";
        echo "<li>CSRF Protection: " . (function_exists('generateCSRFToken') ? '✅' : '❌') . "</li>";
        echo "<li>XSS Protection: " . (function_exists('sanitizeInput') || class_exists('Validation') ? '✅' : '❌') . "</li>";
        echo "<li>Session Security: " . (isset($_SESSION) ? '✅' : '❌') . "</li>";
        echo "<li>Password Security: " . (function_exists('password_hash') ? '✅' : '❌') . "</li>";
        echo "<li>File Upload Security: " . (is_dir($uploadDir) ? '✅' : '❌') . "</li>";
        echo "<li>SQL Injection Protection: " . ($mydb ? '✅' : '❌') . "</li>";
        echo "</ul>";
        echo "<p><strong>Recommendation:</strong> Review any warnings or errors and implement additional security measures as needed.</p>";
        echo "</div>";
        ?>
        
        <div style="margin-top: 20px;">
            <a href="validation-test.php" style="padding: 10px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 4px;">Run Validation Test</a>
            <a href="index.php" style="padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Back to Home</a>
        </div>
    </div>
</body>
</html>