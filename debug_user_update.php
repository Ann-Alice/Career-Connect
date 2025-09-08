<?php
require_once("include/initialize.php");

class DebugUser extends User {
    public function getAttributes() {
        return $this->attributes();
    }
    
    public function getSanitizedAttributes() {
        return $this->sanitized_attributes();
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
    $user = new DebugUser();
    $user = DebugUser::instantiate($user_record);
    
    echo "\nUser object attributes:\n";
    $attributes = $user->getAttributes();
    print_r($attributes);
    
    echo "\nSanitized attributes:\n";
    $sanitized = $user->getSanitizedAttributes();
    print_r($sanitized);
    
    // Try to simulate the update to see what SQL is generated
    echo "\nSimulating update SQL:\n";
    $attribute_pairs = array();
    foreach($sanitized as $key => $value) {
        $attribute_pairs[] = "{$key}='{$value}'";
    }
    $sql = "UPDATE tblusers SET ";
    $sql .= join(", ", $attribute_pairs);
    $sql .= " WHERE USERID='2018001'";
    echo $sql . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Debug complete!\n";
?>