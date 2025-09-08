<?php
require_once(LIB_PATH.DS."config.php");

class Database {
    var $sql_string = '';
    var $error_no = 0;
    var $error_msg = '';
    private $conn;
    public $last_query;
    private $real_escape_string_exists;
    
    function __construct() {
        $this->open_connection();
        $this->real_escape_string_exists = function_exists("mysqli_real_escape_string");
    }
    
    public function open_connection() {
        error_log("Attempting to connect to MySQL server: " . server);
        error_log("Using username: " . user);
        error_log("Using port: " . (defined('mysql_port') ? mysql_port : '4306'));
        
        // Try connecting with the correct port
        $this->conn = mysqli_connect(server, user, pass, null, defined('mysql_port') ? mysql_port : 4306);
        if(!$this->conn) {
            $error = mysqli_connect_error();
            $errno = mysqli_connect_errno();
            error_log("Database connection failed: Error #" . $errno . " - " . $error);
            
            // Show user-friendly error
            if (strpos($error, 'Access denied') !== false) {
                die("
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 2px solid #ff6b6b; border-radius: 10px; background-color: #fff5f5;'>
                    <h2 style='color: #d63031;'>🔒 Database Access Denied</h2>
                    <p><strong>Error:</strong> Cannot connect to MySQL database</p>
                    <p><strong>Solution:</strong></p>
                    <ol>
                        <li>Check your MySQL password in XAMPP</li>
                        <li>Update include/config.php with the correct password</li>
                        <li>Or reset MySQL root password to no password</li>
                        <li>Current port: " . (defined('mysql_port') ? mysql_port : '4306') . "</li>
                    </ol>
                    <p><em>This will temporarily disable MySQL authentication to fix the connection issue.</em></p>
                </div>
                ");
            } else {
                die("
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 2px solid #ff6b6b; border-radius: 10px; background-color: #fff5f5;'>
                    <h2 style='color: #d63031;'>❌ Database Connection Failed</h2>
                    <p><strong>Error:</strong> " . $error . "</p>
                    <p><strong>Error Code:</strong> " . $errno . "</p>
                    <p><strong>Port:</strong> " . (defined('mysql_port') ? mysql_port : '4306') . "</p>
                    <p>Please check your MySQL configuration and try again.</p>
                </div>
                ");
            }
        }
        
        // Set charset to UTF8MB4
        if (!mysqli_set_charset($this->conn, "utf8mb4")) {
            error_log("Warning: Could not set charset to utf8mb4: " . mysqli_error($this->conn));
        }
        
        // Select database
        $db_select = mysqli_select_db($this->conn, database_name);
        if (!$db_select) {
            $error = mysqli_error($this->conn);
            error_log("Database selection failed: " . $error);
            
            // Try to create database if it doesn't exist
            if (mysqli_query($this->conn, "CREATE DATABASE IF NOT EXISTS `" . database_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
                error_log("Database created successfully: " . database_name);
                mysqli_select_db($this->conn, database_name);
            } else {
                die("
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 2px solid #ff6b6b; border-radius: 10px; background-color: #fff5f5;'>
                    <h2 style='color: #d63031;'>❌ Database Selection Failed</h2>
                    <p><strong>Error:</strong> " . $error . "</p>
                    <p>Could not select or create database: " . database_name . "</p>
                    <p>Please check your database configuration.</p>
                </div>
                ");
            }
        }
        
        error_log("Successfully connected to MySQL server on port " . (defined('mysql_port') ? mysql_port : '4306'));
        error_log("Successfully selected database: " . database_name);
    }
    
    function setQuery($sql='') {
        $this->sql_string = $sql;
    }
    
    function executeQuery() {
        // Log the SQL query for debugging
        error_log("Executing SQL query: " . $this->sql_string);
        
        $result = mysqli_query($this->conn, $this->sql_string);
        if (!$result) {
            $this->error_no = mysqli_errno($this->conn);
            $this->error_msg = mysqli_error($this->conn);
            error_log("Query failed: " . $this->sql_string . " - Error: " . $this->error_msg);
            throw new Exception("Database query failed: " . $this->error_msg . " (Error #" . $this->error_no . ")");
        }
        return $result;
    }    
    
    private function confirm_query($result) {
        if(!$result){
            $this->error_no = mysqli_errno($this->conn);
            $this->error_msg = mysqli_error($this->conn);
            error_log("Query failed: " . $this->sql_string . " - Error: " . $this->error_msg);
            return false;                
        }
        return $result;
    } 
    
    function loadResultList($key='') {
        $cur = $this->executeQuery();
        
        $array = array();
        while ($row = mysqli_fetch_object($cur)) {
            if ($key) {
                $array[$row->$key] = $row;
            } else {
                $array[] = $row;
            }
        }
        mysqli_free_result($cur);
        return $array;
    }
    
    function loadSingleResult() {
        $cur = $this->executeQuery();
            
        while ($row = mysqli_fetch_object($cur)) {
            return $data = $row;
        }
        mysqli_free_result($cur);
    }
    
    function getFieldsOnOneTable($tbl_name) {
        $this->setQuery("DESCRIBE " . $tbl_name);
        $rows = $this->loadResultList();
        
        $f = array();
        for ($x = 0; $x < count($rows); $x++) {
            $f[] = $rows[$x]->Field;
        }
        
        return $f;
    }    

    public function fetch_array($result) {
        return mysqli_fetch_array($result);
    }
    
    public function fetch_assoc($result) {
        return mysqli_fetch_assoc($result);
    }
    
    public function fetch_object($result) {
        return mysqli_fetch_object($result);
    }
    
    public function num_rows($result) {
        return mysqli_num_rows($result);
    }
    
    public function insert_id() {
        return mysqli_insert_id($this->conn);
    }
    
    public function affected_rows() {
        return mysqli_affected_rows($this->conn);
    }
    
    public function escape_string($string) {
        return mysqli_real_escape_string($this->conn, $string);
    }
    
    public function close_connection() {
        if (isset($this->conn)) {
            mysqli_close($this->conn);
            unset($this->conn);
        }
    }
    
    function __destruct() {
        $this->close_connection();
    }
}

// Database class definition only - no automatic instantiation
?>
