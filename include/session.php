<?php
// Enhanced Session Management with Security Features

class SessionManager {
    
    // Session timeout in seconds (1 hour)
    const SESSION_TIMEOUT = 3600;
    
    public static function start() {
        // Session security settings
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.cookie_samesite', 'Strict');
        
        // Start session with security settings
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerate session ID periodically to prevent session fixation
        self::regenerateSessionId();
        
        // Check session timeout
        self::checkTimeout();
        
        // Generate CSRF token if not exists
        self::generateCSRFToken();
    }
    
    // Regenerate session ID periodically
    public static function regenerateSessionId() {
        if (!isset($_SESSION["last_regeneration"])) {
            $_SESSION["last_regeneration"] = time();
        } elseif (time() - $_SESSION["last_regeneration"] > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION["last_regeneration"] = time();
        }
    }
    
    // Check session timeout
    public static function checkTimeout() {
        if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > self::SESSION_TIMEOUT)) {
            self::destroy();
            return false;
        }
        $_SESSION["last_activity"] = time();
        return true;
    }
    
    // Generate CSRF token
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
    
    // Validate CSRF token
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // Get CSRF token
    public static function getCSRFToken() {
        return $_SESSION['csrf_token'] ?? '';
    }
    
    // Destroy session
    public static function destroy() {
        // Clear all session variables
        $_SESSION = array();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
    }
    
    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['USERID']) && self::checkTimeout();
    }
    
    // Get session user data
    public static function getUserData($key = null) {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        if ($key === null) {
            return $_SESSION;
        }
        
        return $_SESSION[$key] ?? null;
    }
}

// Initialize session management
SessionManager::start();

// Backward compatibility functions
function logged_in() {
    return SessionManager::isLoggedIn();
}

function confirm_logged_in() {
    if (!logged_in()) {?>
        <script type="text/javascript">
            window.location = "login.php";
        </script>
    <?php
    }
}

function admin_confirm_logged_in() {
    if (@!$_SESSION['USERID']) {?>
        <script type="text/javascript">
            window.location ="login.php";
        </script>
    <?php
    }
}

function studlogged_in() {
    return isset($_SESSION['CUSID']);
}

function studconfirm_logged_in() {
    if (!studlogged_in()) {?>
        <script type="text/javascript">
            window.location = "index.php";
        </script>
    <?php
    }
}

// CSRF token functions for backward compatibility
function validateCSRFToken($token) {
    return SessionManager::validateCSRFToken($token);
}

function generateCSRFToken() {
    SessionManager::generateCSRFToken();
    return SessionManager::getCSRFToken();
}

function getCSRFToken() {
    return SessionManager::getCSRFToken();
}

// Rate limiting for login attempts
function checkLoginAttempts($username) {
    $attempts = $_SESSION['login_attempts'][$username] ?? 0;
    $last_attempt = $_SESSION['last_login_attempt'][$username] ?? 0;
    
    // Reset attempts if more than 15 minutes have passed
    if (time() - $last_attempt > 900) { // 15 minutes
        $_SESSION['login_attempts'][$username] = 0;
        return true;
    }
    
    // Block if more than 5 attempts
    if ($attempts >= 5) {
        return false;
    }
    
    return true;
}

function recordLoginAttempt($username, $success = false) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
        $_SESSION['last_login_attempt'] = [];
    }
    
    if ($success) {
        // Reset on successful login
        $_SESSION['login_attempts'][$username] = 0;
    } else {
        // Increment on failed login
        $_SESSION['login_attempts'][$username] = ($_SESSION['login_attempts'][$username] ?? 0) + 1;
    }
    
    $_SESSION['last_login_attempt'][$username] = time();
}

// Sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Enhanced message function with better error handling
function message($msg="", $msgtype="") {
    if(!empty($msg)) {
        // then this is "set message"
        // make sure you understand why $this->message=$msg wouldn't work
        $_SESSION['message'] = $msg;
        $_SESSION['msgtype'] = $msgtype;
    } else {
        // then this is "get message"
        return $message;
    }
}

// Enhanced check_message function with better styling
function check_message(){
    if(isset($_SESSION['message'])){
        if(isset($_SESSION['msgtype'])){
            $message = $_SESSION['message'];
            $msgtype = $_SESSION['msgtype'];
            
            // Sanitize output to prevent XSS
            $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
            
            switch ($msgtype) {
                case "info":
                    echo '<div class="alert alert-info" style="height:30px;text-align:center;padding:5px">' . $message . '</div>';
                    break;
                case "error":
                    echo '<div class="alert alert-danger" style="height:30px;text-align:center;padding:5px">' . $message . '</div>';
                    break;
                case "success":
                    echo '<div class="alert alert-success" style="height:30px;text-align:center;padding:5px">' . $message . '</div>';
                    break;
                default:
                    echo '<div class="alert alert-info" style="height:30px;text-align:center;padding:5px">' . $message . '</div>';
            }
            
            unset($_SESSION['message']);
            unset($_SESSION['msgtype']);
        }
    }   
}

function cusmsg($num=0){
    if(!empty($num)){
        $_SESSION['gcNotify'] = $num;
    }else{
        return $gcNotify;
    }
}

function notifycheck(){
    if(isset($_SESSION['gcNotify'])){
        echo $_SESSION['gcNotify'];
    }else{
        echo 0;
    }
    unset($_SESSION['gcNotify']);
}

function keyactive($key=""){
     if(!empty($key)) {
        // then this is "set message"
        // make sure you understand why $this->message=$msg wouldn't work
        $_SESSION['active'] = $key; 
    } else {
        // then this is "get message"
        return $keyactive;
    }
}

function check_active(){
     if(isset($_SESSION['active'])){
         switch ($_SESSION['active']) {

        case 'basicInfo' :
        $_SESSION['basicInfo']   = "active";
        break;
        case 'otherInfo' :
        $_SESSION['otherInfo']= 'active';
        break;
        
        case 'work' :
        $_SESSION['work'] = 'active' ;
        break;
      }
      }else{

          $active = (isset($_GET['active']) && $_GET['active'] != '') ? $_GET['active'] : '';
                 switch ($active) {

                  case 'otherInfo' :
                   $_SESSION['otherInfo']= 'active';
                    break;

                  case 'work' :
                   $_SESSION['work'] = 'active' ;
                     break;

                  default :

                    $_SESSION['basicInfo']   = "active";
                 break;
        }
      }
 }

 
function product_exists($pid,$q){
    // $pid=intval($pid); 
    $max=count($_SESSION['gcCart']);
    $flag=0;
    for($i=0;$i<$max;$i++){
      if($pid==$_SESSION['gcCart'][$i]['mealid']){
          if($q>0  && $q<=999){
            # code...
            $flag=1;
             $_SESSION['gcCart'][$i]['qty']= $_SESSION['gcCart'][$i]['qty'] + $q;
             $_SESSION['gcCart'][$i]['subtotal']= $_SESSION['gcCart'][$i]['price'] * $_SESSION['gcCart'][$i]['qty'];
              message("{$q} Item added in the cart.","success");
              break;
          }
        // $flag=1;
        // message("Item is already in the cart.","error");
        // break; 

          
      }
    }
    return $flag;
  }
 function addtocart($pid,$meals,$price,$q,$subtotal){
    // if($pid<1 or $q<1) return;
    if($q<1) return;
    if (!empty($_SESSION['gcCart'])){


    if(is_array($_SESSION['gcCart'])){
      if(product_exists($pid,$q)) return;
      $max=count($_SESSION['gcCart']);
      $_SESSION['gcCart'][$max]['mealid']=$pid;
      $_SESSION['gcCart'][$max]['meals']=$meals;
      $_SESSION['gcCart'][$max]['qty']=$q;
      $_SESSION['gcCart'][$max]['price']=$price;
      $_SESSION['gcCart'][$max]['subtotal']=$subtotal;
    }
    else{
     $_SESSION['gcCart']=array();
      $_SESSION['gcCart'][0]['mealid']=$pid;
      $_SESSION['gcCart'][0]['meals']=$meals;
      $_SESSION['gcCart'][0]['qty']=$q;
      $_SESSION['gcCart'][0]['price']=$price;
      $_SESSION['gcCart'][0]['subtotal']=$subtotal;
    }
}else{
     $_SESSION['gcCart']=array();
      $_SESSION['gcCart'][0]['mealid']=$pid;
      $_SESSION['gcCart'][0]['meals']=$meals;
      $_SESSION['gcCart'][0]['qty']=$q;
      $_SESSION['gcCart'][0]['price']=$price;
      $_SESSION['gcCart'][0]['subtotal']=$subtotal;
}
    
     message("{$q} Item added in the cart.","success");
}
function removetocart($pid){
    // $pid=intval($pid);
    $max=count($_SESSION['gcCart']);
    for($i=0;$i<$max;$i++){
        if($pid==$_SESSION['gcCart'][$i]['mealid']){
            unset($_SESSION['gcCart'][$i]);
            break;
        }
    }
    $_SESSION['gcCart']=array_values($_SESSION['gcCart']);
}


 function editproduct($pid,$q){
    // $pid=intval($pid); 
  if($q<1) return;
    if (!empty($_SESSION['gcCart'])){
       if(is_array($_SESSION['gcCart'])){
          $max=count($_SESSION['gcCart']);
          $flag=0;
          for($i=0;$i<$max;$i++){
            if($pid==$_SESSION['gcCart'][$i]['mealid']){
                if($q>0  && $q<=999){
                  # code...
                  $flag=1;
                   $_SESSION['gcCart'][$i]['qty']= $q;
                   $_SESSION['gcCart'][$i]['subtotal']= $_SESSION['gcCart'][$i]['price'] * $_SESSION['gcCart'][$i]['qty'];
                    break;
                }
              // $flag=1;
              // message("Item is already in the cart.","error");
              // break;
            }
          }
          return $flag;
        }
      }
    }


function admin_product_exists($pid,$q){
    // $pid=intval($pid); 
    $max=count($_SESSION['admin_gcCart']);
    $flag=0;
    for($i=0;$i<$max;$i++){
      if($pid==$_SESSION['admin_gcCart'][$i]['mealid']){
          if($q>0  && $q<=999){
            # code...
            $flag=1;
             $_SESSION['admin_gcCart'][$i]['qty']= $_SESSION['admin_gcCart'][$i]['qty'] + $q;
             $_SESSION['admin_gcCart'][$i]['subtotal']= $_SESSION['admin_gcCart'][$i]['price'] * $_SESSION['admin_gcCart'][$i]['qty'];
              // message("{$q} Item added in the cart.","success");
              break;
          }
        $flag=1;
        // message("Item is already in the cart.","error");
        break;  
      }
    }
    return $flag;
  }
 function admin_addtocart($pid,$meals,$price,$q,$subtotal){
    if($pid<1 or $q<1) return;
    if($q<1) return;
    if (!empty($_SESSION['admin_gcCart'])){


    if(is_array($_SESSION['admin_gcCart'])){
      if(admin_product_exists($pid,$q)) return;
      $max=count($_SESSION['admin_gcCart']);
      $_SESSION['admin_gcCart'][$max]['mealid']=$pid;
      $_SESSION['admin_gcCart'][$max]['meals']=$meals;
      $_SESSION['admin_gcCart'][$max]['qty']=$q;
      $_SESSION['admin_gcCart'][$max]['price']=$price;
      $_SESSION['admin_gcCart'][$max]['subtotal']=$subtotal;
    }
    else{
     $_SESSION['admin_gcCart']=array();
      $_SESSION['admin_gcCart'][0]['mealid']=$pid;
      $_SESSION['admin_gcCart'][0]['meals']=$meals;
      $_SESSION['admin_gcCart'][0]['qty']=$q;
      $_SESSION['admin_gcCart'][0]['price']=$price;
      $_SESSION['admin_gcCart'][0]['subtotal']=$subtotal;
    }
}else{
     $_SESSION['admin_gcCart']=array();
      $_SESSION['admin_gcCart'][0]['mealid']=$pid;
      $_SESSION['admin_gcCart'][0]['meals']=$meals;
      $_SESSION['admin_gcCart'][0]['qty']=$q;
      $_SESSION['admin_gcCart'][0]['price']=$price;
      $_SESSION['admin_gcCart'][0]['subtotal']=$subtotal;
}
  
     // message("{$q} Item added in the cart.","success");
}
function admin_removetocart($pid){
  // $pid=intval($pid);
  $max=count($_SESSION['admin_gcCart']);
  for($i=0;$i<$max;$i++){
    if($pid==$_SESSION['admin_gcCart'][$i]['mealid']){
      unset($_SESSION['admin_gcCart'][$i]);
      break;
    }
  }
  $_SESSION['admin_gcCart']=array_values($_SESSION['admin_gcCart']);
}


 function admin_editproduct($pid,$q){
    // $pid=intval($pid); 
  if($q<1) return;
    if (!empty($_SESSION['admin_gcCart'])){
       if(is_array($_SESSION['admin_gcCart'])){
          $max=count($_SESSION['admin_gcCart']);
          $flag=0;
          for($i=0;$i<$max;$i++){
            if($pid==$_SESSION['admin_gcCart'][$i]['mealid']){
                if($q>0  && $q<=999){
                  # code...
                  $flag=1;
                   $_SESSION['admin_gcCart'][$i]['qty']= $q;
                   $_SESSION['admin_gcCart'][$i]['subtotal']= $_SESSION['admin_gcCart'][$i]['price'] * $_SESSION['admin_gcCart'][$i]['qty'];
                    break;
                }
              // $flag=1;
              // message("Item is already in the cart.","error");
              // break;
            }
          }
          return $flag;
        }
      }
    }

function header_subheader($header,$subheader){

  $setheader = (isset($header) && $header != '') ? $header : '';

switch ($setheader) {
 

  case 'product' :
       echo $title="Products"  . (isset($subheader) ?  '  |  ' .$subheader: '' );   
    break;
  case 'cart' :
       echo $title="Cart List";   
    break;
  case 'profile' :
      echo  $title="Profile";  
    break;
  case 'orderdetails' : 
    echo $title = "Cart List/Order Details";
 
     break;

  case 'billing' :   
      echo $title = "Cart List/Order Details/Billing Details";
    break;

  case 'contact' :
      echo  $title="Contact Us";   
    break;
  case 'single-item' :
      echo  $title="Products"  . (isset($subheader) ?  '  |  ' .$subheader: '' ); 
    break;
  default :
   echo   $title="Home";  
   break;
}
}

?>