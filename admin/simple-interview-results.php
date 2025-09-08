<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');

if (!isset($_SESSION['ADMINID'])) {
    redirect(web_root . "admin/login.php");
}

$page_title = "Interview Results";
include('header.php');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-chart-line"></i> AI Interview Results
                    </h3>
                </div>
                <div class="box-body">
                    <?php
                    // Get applications with interview results
                    $sql = "SELECT r.*, a.EMAILADDRESS, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, 
                                   r.INTERVIEW_COMPLETED_AT, r.INTERVIEW_STATUS
                            FROM tbljobregistration r 
                            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                            JOIN tbljob j ON r.JOBID = j.JOBID 
                            WHERE r.INTERVIEW_STATUS = 'Completed' 
                            ORDER BY r.INTERVIEW_COMPLETED_AT DESC";
                    $mydb->setQuery($sql);
                    $applications = $mydb->loadResultList();
                    
                    if (empty($applications)) {
                        echo '<div class="alert alert-info">';
                        echo '<h4><i class="fa fa-info-circle"></i> No Interview Results Yet</h4>';
                        echo '<p>Completed interviews will appear here once candidates finish their AI interviews.</p>';
                        echo '</div>';
                    } else {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-striped table-bordered">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Candidate Name</th>';
                        echo '<th>Position</th>';
                        echo '<th>Email</th>';
                        echo '<th>Completed Date</th>';
                        echo '<th>Actions</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        
                        foreach ($applications as $app) {
                            $results = json_decode($app->INTERVIEW_RESULTS, true);
                            $overallScore = isset($results['overall_score']) ? $results['overall_score'] : 0;
                            
                            // Calculate grade
                            $grade = 'F';
                            if ($overallScore >= 90) $grade = 'A';
                            elseif ($overallScore >= 80) $grade = 'B';
                            elseif ($overallScore >= 70) $grade = 'C';
                            elseif ($overallScore >= 60) $grade = 'D';
                            
                            echo '<tr>';
                            echo '<td>' . $app->FNAME . ' ' . $app->LNAME . '</td>';
                            echo '<td>' . $app->OCCUPATIONTITLE . '</td>';
                            echo '<td>' . $app->EMAILADDRESS . '</td>';
                            echo '<td>' . ($app->INTERVIEW_COMPLETED_AT ? date('M d, Y', strtotime($app->INTERVIEW_COMPLETED_AT)) : 'Not recorded') . '</td>';
                            echo '<td>';
                            echo '<span class="label label-primary">Grade ' . $grade . ' (' . $overallScore . '%)</span> ';
                            echo '<a href="?action=view&id=' . $app->REGISTRATIONID . '" class="btn btn-xs btn-info">';
                            echo '<i class="fa fa-eye"></i> View Details';
                            echo '</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?> 