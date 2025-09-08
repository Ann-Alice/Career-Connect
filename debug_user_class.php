<?php
require_once("include/initialize.php");

// Create a debug version of the User class
class DebugUser extends User {
    // Make the protected methods public for debugging
    public function debug_attributes() { 
        global $mydb;
        $attributes = array();
        foreach($this->dbfields() as $field) {
            if(property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }
    
    public function debug_sanitized_attributes() {
        global $mydb;
        $clean_attributes = array();
        $attributes = $this->debug_attributes();
        foreach($attributes as $key => $value){
            $clean_attributes[$key] = $mydb->escape_string($value);
        }
        return $clean_attributes;
    }
    
    public function debug_update($id=0) {
        global $mydb;
        $attributes = $this->debug_sanitized_attributes();
        echo "Attributes for update:\n";
        print_r($attributes);
        
        $attribute_pairs = array();
        foreach($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        echo "Attribute pairs:\n";
        print_r($attribute_pairs);
        
        $sql = "UPDATE ".self::$tblname." SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE USERID=". $id;
        echo "Generated SQL:\n";
        echo $sql . "\n";
        
        // Don't actually execute, just show what would happen
        return $sql;
    }
}

// Connect to database
global $mydb;

echo "Debugging User update process...\n";

try {
    // Get a user record to examine
    $sql = "SELECT * FROM tblusers WHERE USERID = '2018001'";
    $mydb->setQuery($sql);
    $user_record = $mydb->loadSingleResult();
    
    echo "Current user record:\n";
    print_r($user_record);
    
    // Create a User object and examine its attributes
    $user = DebugUser::instantiate($user_record);
    
    echo "\nTesting update simulation:\n";
    $user->debug_update('2018001');
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Debug complete!\n";
?>