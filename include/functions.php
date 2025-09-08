<?php
// Helper functions for the application

// Using message() from session.php instead of duplicate function
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = isset($_SESSION['msgtype']) ? $_SESSION['msgtype'] : 'info';
        $msg = $_SESSION['message'];
        
        // Clear the message
        unset($_SESSION['message']);
        unset($_SESSION['msgtype']);
        
        return "<div class='alert alert-{$type}'>{$msg}</div>";
    }
    return '';
}

// Essential functions that were in function.php
function redirect($location=Null){
    if($location!=Null){
        echo "<script>
                window.location='{$location}'
            </script>";	
    }else{
        echo 'error location';
    }
}

function redirect_to($location = NULL) {
    if($location != NULL){
        header("Location: {$location}");
        exit;
    }
}

function output_message($message="") {
    if(!empty($message)){
        return "<p class=\"message\">{$message}</p>";
    }else{
        return "";
    }
}

function strip_zeros_from_date($marked_string="") {
    //first remove the marked zeros
    $no_zeros = str_replace('*0','',$marked_string);
    $cleaned_string = str_replace('*0','',$no_zeros);
    return $cleaned_string;
}

function date_toText($datetime=""){
    $nicetime = strtotime($datetime);
    return strftime("%B %d, %Y at %I:%M %p", $nicetime);	
}

function msgBox($msg=""){
    ?>
    <script type="text/javascript">
         alert(<?php echo $msg; ?>)
    </script>
    <?php
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Using formatDate, formatDateTime, and sanitize from function.php

function isLoggedIn() {
    return isset($_SESSION['USERID']);
}

function isAdmin() {
    return isset($_SESSION['ROLE']) && $_SESSION['ROLE'] == 'Administrator';
}

function isApplicant() {
    return isset($_SESSION['APPLICANTID']);
}

function getCurrentUserId() {
    if (isAdmin()) {
        return $_SESSION['USERID'];
    } elseif (isApplicant()) {
        return $_SESSION['APPLICANTID'];
    }
    return null;
}

function getCurrentUserName() {
    if (isAdmin()) {
        return $_SESSION['FULLNAME'];
    } elseif (isApplicant()) {
        return $_SESSION['USERNAME'];
    }
    return null;
}
?> 