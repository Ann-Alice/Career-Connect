<?php
// Interview Notifications Component for Applicant Profile
// Displays interview-related notifications and updates for the logged-in applicant

if (!isset($_SESSION['APPLICANTID'])) {
    return; // Don't show notifications if not logged in
}

// Check if database is available
if (!isset($mydb)) {
    return; // Don't show notifications if database is not available
}

$applicant_id = $_SESSION['APPLICANTID'];

// Get interview-related notifications for this applicant
$interview_notifications = [];

try {
    // Check for pending interview invitations
    $sql = "SELECT j.OCCUPATIONTITLE, c.COMPANYNAME, jr.REGISTRATIONDATE, jr.REMARKS 
            FROM tbljobregistration jr 
            JOIN tbljob j ON jr.JOBID = j.JOBID 
            JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
            WHERE jr.APPLICANTID = '$applicant_id' 
            AND (jr.REMARKS = 'Pending' OR jr.REMARKS LIKE '%Interview%' OR jr.REMARKS LIKE '%Schedule%')
            ORDER BY jr.REGISTRATIONDATE DESC 
            LIMIT 5";
    
    $mydb->setQuery($sql);
    $interview_notifications = $mydb->loadResultList();
} catch (Exception $e) {
    // Silently handle database errors
    $interview_notifications = [];
}

// Only display if there are notifications
if (!empty($interview_notifications)): ?>
<div class="alert alert-info" style="margin-bottom: 20px; border-left: 4px solid #3498db;">
    <h4><i class="fa fa-calendar"></i> Interview Updates</h4>
    <ul style="margin-bottom: 0; padding-left: 20px;">
        <?php foreach ($interview_notifications as $notification): ?>
        <li style="margin-bottom: 8px;">
            <strong><?php echo htmlspecialchars($notification->OCCUPATIONTITLE); ?></strong> 
            at <?php echo htmlspecialchars($notification->COMPANYNAME); ?>
            <br>
            <small class="text-muted">
                Applied: <?php echo date('M d, Y', strtotime($notification->REGISTRATIONDATE)); ?> 
                - Status: <?php echo htmlspecialchars($notification->REMARKS); ?>
            </small>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>