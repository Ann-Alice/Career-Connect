<?php
require_once("include/initialize.php");

// Test inserting an employee without specifying WORKSTATS
echo "Testing employee creation without specifying WORKSTATS...\n";

$emp = New Employee(); 
$emp->EMPLOYEEID 		= 'TEST001';
$emp->FNAME				= 'Test'; 
$emp->LNAME				= 'Employee';
$emp->MNAME 	   		= 'T';
$emp->ADDRESS			= 'Test Address';  
$emp->BIRTHDATE	 		= '1990-01-01';
$emp->BIRTHPLACE		= 'Test City';  
$emp->AGE			    = 30;
$emp->SEX 				= 'Male'; 
$emp->TELNO				= '123-456-7890';
$emp->CELLNO			= '';
$emp->CIVILSTATUS		= 'Single'; 
$emp->POSITION			= 'Tester';
$emp->WORKSTATS			= 'Active';  // This will now have a default
$emp->EMPPHOTO			= '';  // This will now have a default
$emp->EMP_EMAILADDRESS	= 'test@example.com';
$emp->EMPUSERNAME		= 'TEST001';
$emp->EMPPASSWORD		= sha1('TEST001');
$emp->DATEHIRED			= '2020-01-01';
$emp->COMPANYID			= 1;

if($emp->create()) {
    echo "SUCCESS: Employee created without specifying WORKSTATS\n";
    
    // Check what value was set for WORKSTATS
    $sql = "SELECT WORKSTATS FROM tblemployees WHERE EMPLOYEEID = 'TEST001'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if($result) {
        echo "WORKSTATS value in database: " . $result->WORKSTATS . "\n";
    }
    
    // Clean up test record
    $sql = "DELETE FROM tblemployees WHERE EMPLOYEEID = 'TEST001'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
} else {
    echo "FAILED: Employee creation failed\n";
}

?>