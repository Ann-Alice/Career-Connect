<?php
require_once("include/initialize.php");

global $mydb;

// Create a debug version of the User class
class DebugUser extends User {
    public function debug_dbfields() {
        global $mydb;
        return $mydb->getfieldsononetable(self::$tblname);
    }
    
    public function debug_attributes() { 
        global $mydb;
        $attributes = array();
        foreach($this->debug_dbfields() as $field) {
            if(property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            } else if (isset($this->$field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }
}

echo "Detailed debugging of User update process...\n";

try {
    // Get a user record to examine
    $sql = "SELECT * FROM tblusers WHERE USERID = '2018001'";
    $mydb->setQuery($sql);
    $user_record = $mydb->loadSingleResult();
    
    echo "Database record:\n";
    print_r($user_record);
    
    // Create a User object and examine its attributes
    $user = DebugUser::instantiate($user_record);
    
    echo "\nDebug user object:\n";
    echo "DB Fields: ";
    print_r($user->debug_dbfields());
    
    echo "Object attributes: ";
    print_r($user->debug_attributes());
    
    // Now set the properties as in the controller
    $user->FULLNAME = 'Chambe Narciso';
    $user->USERNAME = 'Narciso';
    $user->PASS     = sha1('2018001');
    
    echo "\nAfter setting properties:\n";
    print_r($user->debug_attributes());
    
    // Check what the sanitized attributes look like
    $reflection = new ReflectionClass($user);
    $method = $reflection->getMethod('sanitized_attributes');
    $method->setAccessible(true);
    $sanitized = $method->invoke($user);
    
    echo "\nSanitized attributes:\n";
    print_r($sanitized);
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Debug complete!\n";
?>