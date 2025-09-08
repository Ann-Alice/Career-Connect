<?php
require_once("include/initialize.php");

// Simulate what happens in the controller
echo "Testing user update that causes the DECIMAL error...\n";

try {
    $user = New User(); 
    $u_res = $user->single_user('2018001');
    
    if (isset($u_res)) {
        echo "User found, updating...\n";
        // This is what the controller does
        $user->FULLNAME = 'Chambe Narciso';  // $_POST['FNAME'] . ' ' .$_POST['LNAME']
        $user->USERNAME = 'Narciso';         // $_POST['LNAME']
        $user->PASS     = sha1('2018001');   // sha1($_POST['EMPLOYEEID'])
        
        echo "Calling update method...\n";
        $result = $user->update('2018001');
        echo "Update result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    } else {
        echo "User not found\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "Test complete!\n";
?>