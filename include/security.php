<?php
// Security Configuration and Utilities

// Security headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Input validation functions
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^[0-9+\-\s\(\)]{7,20}$/', $phone);
}

function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function validatePassword($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $password);
}

// XSS protection
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// SQL injection protection for legacy code (when prepared statements aren't used)
function sanitizeForSQL($data) {
    global $mydb;
    if (isset($mydb) && method_exists($mydb, 'escape_string')) {
        return $mydb->escape_string($data);
    }
    return mysqli_real_escape_string($GLOBALS['mydb']->conn, $data);
}

// File upload security
function validateFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpName = $file['tmp_name'];
    
    // Check file size (5MB limit)
    if ($fileSize > 5 * 1024 * 1024) {
        return false;
    }
    
    // Check file extension
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        return false;
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpName);
    finfo_close($finfo);
    
    $allowedMimeTypes = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'application/pdf' => ['pdf']
    ];
    
    $validMime = false;
    foreach ($allowedMimeTypes as $mime => $extensions) {
        if ($mimeType === $mime && in_array($fileExtension, $extensions)) {
            $validMime = true;
            break;
        }
    }
    
    return $validMime;
}

// IP address logging
function getRealIP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    return $ip;
}

// Log security events
function logSecurityEvent($event, $details = '') {
    $logFile = SITE_ROOT . DS . 'logs' . DS . 'security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = getRealIP();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    $logEntry = "[{$timestamp}] [{$ip}] [{$userAgent}] {$event}: {$details}" . PHP_EOL;
    
    // Create logs directory if it doesn't exist
    $logDir = SITE_ROOT . DS . 'logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Brute force protection
class BruteForceProtection {
    private $maxAttempts = 5;
    private $timeWindow = 900; // 15 minutes
    
    public function isBlocked($identifier) {
        $attempts = $_SESSION['login_attempts'][$identifier] ?? 0;
        $last_attempt = $_SESSION['last_login_attempt'][$identifier] ?? 0;
        
        // Reset attempts if more than time window has passed
        if (time() - $last_attempt > $this->timeWindow) {
            $_SESSION['login_attempts'][$identifier] = 0;
            return false;
        }
        
        // Block if more than max attempts
        return $attempts >= $this->maxAttempts;
    }
    
    public function recordAttempt($identifier, $success = false) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
            $_SESSION['last_login_attempt'] = [];
        }
        
        if ($success) {
            // Reset on successful login
            $_SESSION['login_attempts'][$identifier] = 0;
        } else {
            // Increment on failed login
            $_SESSION['login_attempts'][$identifier] = ($_SESSION['login_attempts'][$identifier] ?? 0) + 1;
        }
        
        $_SESSION['last_login_attempt'][$identifier] = time();
    }
}
?>