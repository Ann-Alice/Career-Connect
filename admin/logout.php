<?php
require_once('../include/initialize.php');

// Clear admin session variables
unset($_SESSION['ADMIN_USERID']);
unset($_SESSION['ADMIN_FULLNAME']);
unset($_SESSION['ADMIN_USERNAME']);
unset($_SESSION['ADMIN_ROLE']);
unset($_SESSION['ADMIN_PICLOCATION']);

// Destroy session
session_destroy();

redirect(web_root."admin/login.php");
?>