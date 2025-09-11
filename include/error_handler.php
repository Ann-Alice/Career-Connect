<?php
// Error Handling and Logging Utilities

class ErrorHandler {
    
    // Log levels
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_CRITICAL = 5;
    
    private static $logLevel = self::LEVEL_INFO;
    private static $logFile = '';
    
    public static function initialize($logLevel = self::LEVEL_INFO) {
        self::$logLevel = $logLevel;
        self::$logFile = SITE_ROOT . DS . 'logs' . DS . 'application.log';
        
        // Create logs directory if it doesn't exist
        $logDir = SITE_ROOT . DS . 'logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Set error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', APP_DEBUG ? 1 : 0);
        ini_set('log_errors', 1);
        ini_set('error_log', self::$logFile);
    }
    
    public static function log($level, $message, $context = []) {
        // Check if we should log this level
        if ($level < self::$logLevel) {
            return;
        }
        
        $levelNames = [
            self::LEVEL_DEBUG => 'DEBUG',
            self::LEVEL_INFO => 'INFO',
            self::LEVEL_WARNING => 'WARNING',
            self::LEVEL_ERROR => 'ERROR',
            self::LEVEL_CRITICAL => 'CRITICAL'
        ];
        
        $timestamp = date('Y-m-d H:i:s');
        $levelName = $levelNames[$level] ?? 'UNKNOWN';
        $ip = self::getRealIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $userId = $_SESSION['USERID'] ?? 'Anonymous';
        
        // Format context data
        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' Context: ' . json_encode($context);
        }
        
        $logEntry = "[{$timestamp}] [{$levelName}] [{$ip}] [User: {$userId}] [{$userAgent}] {$message}{$contextStr}" . PHP_EOL;
        
        // Write to log file
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public static function debug($message, $context = []) {
        self::log(self::LEVEL_DEBUG, $message, $context);
    }
    
    public static function info($message, $context = []) {
        self::log(self::LEVEL_INFO, $message, $context);
    }
    
    public static function warning($message, $context = []) {
        self::log(self::LEVEL_WARNING, $message, $context);
    }
    
    public static function error($message, $context = []) {
        self::log(self::LEVEL_ERROR, $message, $context);
    }
    
    public static function critical($message, $context = []) {
        self::log(self::LEVEL_CRITICAL, $message, $context);
    }
    
    // Custom exception handler
    public static function handleException($exception) {
        $message = "Uncaught Exception: " . $exception->getMessage();
        $context = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];
        
        self::critical($message, $context);
        
        // Show user-friendly error message in production
        if (APP_DEBUG) {
            echo "<div style='background: #fce4e4; border: 1px solid #fcc2c2; padding: 20px; margin: 20px; border-radius: 5px;'>";
            echo "<h2 style='color: #cc0000;'>Application Error</h2>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . $exception->getFile() . "</p>";
            echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
            echo "</div>";
        } else {
            echo "<div style='background: #fce4e4; border: 1px solid #fcc2c2; padding: 20px; margin: 20px; border-radius: 5px; text-align: center;'>";
            echo "<h2 style='color: #cc0000;'>Something went wrong!</h2>";
            echo "<p>We're sorry, but an error occurred. Please try again later.</p>";
            echo "</div>";
        }
    }
    
    // Custom error handler
    public static function handleError($errno, $errstr, $errfile, $errline) {
        $errorTypes = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Runtime Notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        $errorType = $errorTypes[$errno] ?? 'Unknown Error';
        $message = "{$errorType}: {$errstr}";
        $context = [
            'file' => $errfile,
            'line' => $errline
        ];
        
        // Log the error
        self::error($message, $context);
        
        // In debug mode, show the error
        if (APP_DEBUG) {
            echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 10px; border-radius: 5px;'>";
            echo "<strong>{$errorType}:</strong> {$errstr} in <strong>{$errfile}</strong> on line <strong>{$errline}</strong>";
            echo "</div>";
        }
        
        // Don't execute PHP internal error handler for non-fatal errors
        return !($errno & (E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR));
    }
    
    // Get real IP address
    private static function getRealIP() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        return $ip;
    }
    
    // Log security events
    public static function logSecurityEvent($event, $details = '') {
        $context = [
            'event' => $event,
            'details' => $details
        ];
        
        self::warning("Security Event", $context);
    }
}

// Set custom error and exception handlers
set_exception_handler(['ErrorHandler', 'handleException']);
set_error_handler(['ErrorHandler', 'handleError']);

// Initialize error handler
ErrorHandler::initialize();
?>