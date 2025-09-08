<?php
global $mydb;

// Get all applications with their status
$sql = "SELECT r.*, a.EMAILADDRESS, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, 
        CASE 
            WHEN r.REMARKS = 'Approved' AND r.INTERVIEW_RESULTS IS NULL THEN 'Pending Interview'
            WHEN r.REMARKS = 'Approved' AND r.INTERVIEW_RESULTS IS NOT NULL THEN 'Interview Completed'
            WHEN r.REMARKS = 'Rejected' THEN 'Rejected'
            ELSE 'Pending Review'
        END as STATUS
        FROM tbljobregistration r 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        ORDER BY r.REGISTRATIONDATE DESC";
$mydb->setQuery($sql);
$applications = $mydb->loadResultList();
?>

<style>
/* Clean, professional styling following minimalist UI standards */
.content-wrapper {
    background: #f8fafc;
}

.box {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border-radius: 8px;
}

.box-header {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    padding: 20px;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: #f7fafc;
    border-bottom: 2px solid #e2e8f0;
    color: #4a5568;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.table td {
    border-color: #e2e8f0;
    vertical-align: middle;
}

.label {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
}

.btn-sm {
    padding: 4px 12px;
    font-size: 12px;
    border-radius: 4px;
    margin-right: 5px;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 15px;
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin-top: 15px;
}
</style>

<div class="box">
    <div class="box-header">
        <h3 class="box-title" style="color: #2d3748; font-weight: 600;">AI Interview Management</h3>
        <p style="color: #718096; margin-top: 5px; margin-bottom: 0;">Manage application reviews and send interview invitations</p>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped" id="applications-table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Position</th>
                        <th>Email</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($applications && count($applications) > 0): ?>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($app->FNAME . ' ' . $app->LNAME); ?></strong></td>
                            <td><?php echo htmlspecialchars($app->OCCUPATIONTITLE); ?></td>
                            <td><?php echo htmlspecialchars($app->EMAILADDRESS); ?></td>
                            <td><?php echo date('M d, Y', strtotime($app->REGISTRATIONDATE)); ?></td>
                            <td>
                                <span class="label label-<?php 
                                    echo $app->STATUS == 'Pending Review' ? 'warning' : 
                                        ($app->STATUS == 'Pending Interview' ? 'info' : 
                                        ($app->STATUS == 'Interview Completed' ? 'success' : 'danger')); 
                                ?>">
                                    <?php echo htmlspecialchars($app->STATUS); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($app->STATUS == 'Pending Review'): ?>
                                    <a href="?action=approve&id=<?php echo $app->REGISTRATIONID; ?>" 
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Approve this application?')">
                                        <i class="fa fa-check"></i> Approve
                                    </a>
                                    <a href="?action=reject&id=<?php echo $app->REGISTRATIONID; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Reject this application?')">
                                        <i class="fa fa-times"></i> Reject
                                    </a>
                                <?php elseif ($app->STATUS == 'Pending Interview'): ?>
                                    <a href="?action=send&id=<?php echo $app->REGISTRATIONID; ?>" 
                                       class="btn btn-primary btn-sm"
                                       onclick="return confirm('Send interview invitation?')">
                                        <i class="fa fa-envelope"></i> Send Interview
                                    </a>
                                <?php elseif ($app->STATUS == 'Interview Completed'): ?>
                                    <a href="interview-results.php?id=<?php echo $app->REGISTRATIONID; ?>" 
                                       class="btn btn-info btn-sm">
                                        <i class="fa fa-eye"></i> View Results
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No actions available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 40px;">
                                <div style="color: #a0aec0;">
                                    <i class="fa fa-inbox" style="font-size: 48px; margin-bottom: 15px;"></i>
                                    <h4 style="color: #4a5568;">No Applications Found</h4>
                                    <p>There are no job applications to manage at this time.</p>
                                    <a href="../applicants/" class="btn btn-primary">View All Applicants</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#applications-table').DataTable({
        "order": [[3, "desc"]], // Sort by application date by default
        "pageLength": 25,
        "responsive": true,
        "language": {
            "search": "Search applications:",
            "lengthMenu": "Show _MENU_ applications per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ applications",
            "emptyTable": "No applications available"
        }
    });
});
</script> 