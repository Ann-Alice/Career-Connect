<?php
require_once('include/initialize.php');

global $mydb;
$mydb->setQuery('DESCRIBE tblusers');
$result = $mydb->loadResultList();

echo "Current tblusers structure:\n";
foreach($result as $row) {
    echo $row->Field . " | " . $row->Type . " | " . $row->Null . " | " . ($row->Default === null ? 'NULL' : $row->Default) . "\n";
}
?>