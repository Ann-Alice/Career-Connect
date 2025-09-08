<?php
require_once("../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/login.php");
}

// Include the real working dashboard
require_once("dashboard.php");
?>