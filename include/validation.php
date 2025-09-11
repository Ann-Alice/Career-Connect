<?php
// Input Validation and Sanitization Utilities

class Validation {
    
    /**
     * Validate email address
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number
     * @param string $phone
     * @return bool
     */
    public static function validatePhone($phone) {
        // Allow common phone number formats
        return preg_match('/^[0-9+\-\s\(\)]{7,20}$/', $phone) === 1;
    }
    
    /**
     * Validate username
     * @param string $username
     * @return bool
     */
    public static function validateUsername($username) {
        // Alphanumeric and underscore, 3-20 characters
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username) === 1;
    }
    
    /**
     * Validate password strength
     * @param string $password
     * @return bool
     */
    public static function validatePassword($password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $password) === 1;
    }
    
    /**
     * Validate name (letters, spaces, hyphens, apostrophes)
     * @param string $name
     * @return bool
     */
    public static function validateName($name) {
        return preg_match("/^[a-zA-Z\s\-']+$/", $name) === 1;
    }
    
    /**
     * Validate numeric value
     * @param mixed $value
     * @param int|null $min
     * @param int|null $max
     * @return bool
     */
    public static function validateNumeric($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return false;
        }
        
        $num = floatval($value);
        
        if ($min !== null && $num < $min) {
            return false;
        }
        
        if ($max !== null && $num > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate date format
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Sanitize string for output to prevent XSS
     * @param string $data
     * @return string
     */
    public static function sanitizeOutput($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize string for database queries (for legacy code)
     * @param string $data
     * @return string
     */
    public static function sanitizeForSQL($data) {
        global $mydb;
        if (isset($mydb) && method_exists($mydb, 'escape_string')) {
            return $mydb->escape_string($data);
        }
        // Fallback if $mydb is not available
        return mysqli_real_escape_string($GLOBALS['mydb']->conn, $data);
    }
    
    /**
     * Sanitize input data
     * @param string $data
     * @return string
     */
    public static function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    /**
     * Validate file upload
     * @param array $file
     * @param array $allowedTypes
     * @return bool
     */
    public static function validateFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
        // Check if file was uploaded without errors
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
    
    /**
     * Validate URL
     * @param string $url
     * @return bool
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Validate array of inputs
     * @param array $inputs
     * @param array $rules
     * @return array
     */
    public static function validateInputs($inputs, $rules) {
        $errors = [];
        $cleaned = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($inputs[$field]) && isset($rule['required']) && $rule['required']) {
                $errors[$field] = "This field is required";
                continue;
            }
            
            if (!isset($inputs[$field])) {
                $cleaned[$field] = null;
                continue;
            }
            
            $value = $inputs[$field];
            
            // Apply sanitization first
            if (isset($rule['sanitize'])) {
                switch ($rule['sanitize']) {
                    case 'trim':
                        $value = trim($value);
                        break;
                    case 'strip_tags':
                        $value = strip_tags($value);
                        break;
                    case 'htmlspecialchars':
                        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                        break;
                }
            }
            
            // Apply validation rules
            if (isset($rule['validate'])) {
                foreach ($rule['validate'] as $validation) {
                    $valid = true;
                    switch ($validation) {
                        case 'email':
                            $valid = self::validateEmail($value);
                            break;
                        case 'phone':
                            $valid = self::validatePhone($value);
                            break;
                        case 'username':
                            $valid = self::validateUsername($value);
                            break;
                        case 'numeric':
                            $valid = self::validateNumeric($value, $rule['min'] ?? null, $rule['max'] ?? null);
                            break;
                        case 'date':
                            $valid = self::validateDate($value, $rule['format'] ?? 'Y-m-d');
                            break;
                        case 'url':
                            $valid = self::validateURL($value);
                            break;
                        case 'length':
                            $min = $rule['min_length'] ?? 0;
                            $max = $rule['max_length'] ?? PHP_INT_MAX;
                            $valid = (strlen($value) >= $min && strlen($value) <= $max);
                            break;
                    }
                    
                    if (!$valid) {
                        $errors[$field] = $rule['message'] ?? "Invalid value for {$field}";
                        break;
                    }
                }
            }
            
            $cleaned[$field] = $value;
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'cleaned' => $cleaned
        ];
    }
}
?>