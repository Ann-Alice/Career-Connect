<?php
require_once('include/initialize.php');

// Check the full table structure
$sql = 'DESCRIBE tblinterviewinvitations';
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "tblinterviewinvitations table structure:\n";
foreach ($result as $row) {
    echo $row->Field . ' - ' . $row->Type . ' - ' . $row->Null . ' - ' . $row->Key . ' - ' . $row->Default . ' - ' . $row->Extra . "\n";
}

// Check if there's a STATUS column
$has_status = false;
foreach ($result as $row) {
    if ($row->Field === 'STATUS') {
        $has_status = true;
        break;
    }
}

if (!$has_status) {
    echo "\nSTATUS column is missing. Adding it...\n";
    $sql = "ALTER TABLE tblinterviewinvitations ADD COLUMN STATUS enum('pending','completed','expired') NOT NULL DEFAULT 'pending'";
    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        echo "STATUS column added successfully.\n";
    } else {
        echo "Failed to add STATUS column: " . $mydb->error_msg . "\n";
    }
}

// Update the existing invitation to have a proper status
$sql = "UPDATE tblinterviewinvitations SET STATUS = 'pending' WHERE REGISTRATIONID = '3' AND (STATUS IS NULL OR STATUS = '')";
$mydb->setQuery($sql);
if ($mydb->executeQuery()) {
    echo "Invitation status updated for registration ID 3.\n";
} else {
    echo "Failed to update invitation status: " . $mydb->error_msg . "\n";
}

// Check the current data
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

// Also check the job registration status
$sql = "SELECT INTERVIEW_STATUS, INTERVIEW_RESULTS FROM tbljobregistration WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$registration = $mydb->loadSingleResult();

if ($registration) {
    echo "\nRegistration ID 3 status:\n";
    echo "Interview Status: " . $registration->INTERVIEW_STATUS . "\n";
    echo "Interview Results: " . ($registration->INTERVIEW_RESULTS ? "Exists" : "None") . "\n";
} else {
    echo "\nNo registration found for ID 3\n";
}
?>