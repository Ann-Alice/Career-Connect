<?php
require_once("../include/initialize.php");

if(isset($_GET['action'])){
    $action = $_GET['action'];
    
    // Validate CSRF token for login action
    if($action == 'login'){
        // Check if CSRF token is present and valid
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            message("Invalid request. Please try again.", "error");
            redirect(web_root . "admin/login.php");
            exit;
        }
        
        $username = sanitizeInput($_POST['USERNAME']);
        $password = $_POST['PASS'];
        
        // Rate limiting check
        if (!checkLoginAttempts($username)) {
            message("Too many failed login attempts. Please try again later.", "error");
            redirect(web_root . "admin/login.php");
            exit;
        }
        
        // Use prepared statement for secure authentication
        $sql = "SELECT * FROM tblusers WHERE USERNAME = ? AND PASS = ?";
        $hashedPassword = sha1($password);
        $result = $mydb->loadSingleResultPrepared($sql, [$username, $hashedPassword], "ss");
        
        if ($result) {
            // Login successful
            recordLoginAttempt($username, true);
            
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            $_SESSION['ADMIN_USERID'] = $result->USERID;
            $_SESSION['ADMIN_FULLNAME'] = $result->FULLNAME;
            $_SESSION['ADMIN_USERNAME'] = $result->USERNAME;
            $_SESSION['ADMIN_ROLE'] = $result->ROLE;
            $_SESSION['ADMIN_PICLOCATION'] = isset($result->PICLOCATION) ? $result->PICLOCATION : 'avatar.jpg';
            
            // Generate new CSRF token for the session
            generateCSRFToken();
            
            redirect(web_root . "admin/");
        } else {
            // Login failed
            recordLoginAttempt($username, false);
            message("Invalid username or password.", "error");
            redirect(web_root . "admin/login.php");
        }
    }
    
    if($action == 'logout'){
        // Clear admin session variables
        unset($_SESSION['ADMIN_USERID']);
        unset($_SESSION['ADMIN_FULLNAME']);
        unset($_SESSION['ADMIN_USERNAME']);
        unset($_SESSION['ADMIN_ROLE']);
        unset($_SESSION['ADMIN_PICLOCATION']);
        
        // Destroy session
        session_destroy();
        
        redirect(web_root."admin/login.php");
    }
}

// If no action, redirect to login
redirect(web_root."admin/login.php");
?>