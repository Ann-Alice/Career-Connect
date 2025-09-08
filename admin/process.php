<?php
require_once("../include/initialize.php");

if(isset($_GET['action'])){
    $action = $_GET['action'];
    
    if($action == 'login'){
        $username = $_POST['USERNAME'];
        $password = $_POST['PASS'];
        
        // Use real database authentication
        $sql = "SELECT * FROM tblusers WHERE USERNAME = '{$mydb->escape_string($username)}' AND PASS = '{$mydb->escape_string(sha1($password))}'";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        
        if ($result) {
            // Login successful
            $_SESSION['ADMIN_USERID'] = $result->USERID;
            $_SESSION['ADMIN_FULLNAME'] = $result->FULLNAME;
            $_SESSION['ADMIN_USERNAME'] = $result->USERNAME;
            $_SESSION['ADMIN_ROLE'] = $result->ROLE;
            $_SESSION['ADMIN_PICLOCATION'] = isset($result->PICLOCATION) ? $result->PICLOCATION : 'avatar.jpg';
            
            redirect(web_root . "admin/");
        } else {
            // Login failed
            redirect(web_root . "admin/login.php?error=invalid");
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