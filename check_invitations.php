<?php
require_once('include/initialize.php');

$sql = 'DESCRIBE tblinterviewinvitations';
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "tblinterviewinvitations table structure:\n";
foreach ($result as $row) {
    echo $row->Field . ' - ' . $row->Type . ' - ' . $row->Null . ' - ' . $row->Key . ' - ' . $row->Default . ' - ' . $row->Extra . "\n";
}

// Also check the current data
$sql = "SELECT * FROM tblinterviewinvitations WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$invitations = $mydb->loadResultList();

echo "\nCurrent invitations for registration ID 3:\n";
if ($invitations) {
    foreach ($invitations as $invitation) {
        echo "ID: " . $invitation->INVITATION_ID . ", Token: " . $invitation->TOKEN . ", Expiry: " . $invitation->EXPIRY_DATE . ", Status: " . $invitation->STATUS . "\n";
    }
} else {
    echo "No invitations found for registration ID 3\n";
}
?>