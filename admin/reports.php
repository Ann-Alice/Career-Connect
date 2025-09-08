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

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'view':
        viewCandidateReport();
        break;
    case 'message':
        sendMessageToCandidate();
        break;
    case 'update_status':
        updateCandidateStatus();
        break;
    default:
        showCandidateReports();
        break;
}

function showCandidateReports() {
    global $mydb;
    
    // Get all candidates with interview results
    $sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, a.CONTACTNO, j.OCCUPATIONTITLE,
                   JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') as score,
                   JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.expressions.score') as expression_score,
                   JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.movements.score') as movement_score,
                   JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.answers.score') as answer_score
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.INTERVIEW_STATUS = 'Completed' 
            AND r.INTERVIEW_RESULTS IS NOT NULL
            ORDER BY r.INTERVIEW_COMPLETED_AT DESC";
    $mydb->setQuery($sql);
    $candidates = $mydb->loadResultList();
    
    include('header.php');
    ?>
    
    <style>
        .reports-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }
        .candidate-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .candidate-card:hover {
            transform: translateY(-5px);
        }
        .candidate-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .candidate-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .candidate-details h4 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }
        .candidate-position {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }
        .score-overview {
            text-align: right;
        }
        .overall-score {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .score-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }
        .candidate-body {
            padding: 25px;
        }
        .performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .performance-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .performance-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .performance-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }
        .candidate-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .btn-message {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
        }
        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
        }
        .btn-accept {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
            border: none;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #dee2e6;
        }
    </style>
    
    <div class="container-fluid">
        <div class="reports-header text-center">
            <h1><i class="fas fa-user-tie"></i> Candidate Interview Reports</h1>
            <p class="mb-0">Review interview performance and communicate with candidates</p>
        </div>
        
        <?php if (!empty($candidates)) { ?>
            <?php foreach ($candidates as $candidate) { 
                $overallScore = $candidate->score ?? 0;
                $expressionScore = $candidate->expression_score ?? 0;
                $movementScore = $candidate->movement_score ?? 0;
                $answerScore = $candidate->answer_score ?? 0;
                
                // Determine status based on score
                $status = 'pending';
                $statusClass = 'status-pending';
                if ($overallScore >= 80) {
                    $status = 'accepted';
                    $statusClass = 'status-accepted';
                } elseif ($overallScore < 50) {
                    $status = 'rejected';
                    $statusClass = 'status-rejected';
                }
            ?>
            <div class="candidate-card">
                <div class="candidate-header">
                    <div class="candidate-info">
                        <div class="candidate-details">
                            <h4><?php echo $candidate->FNAME . ' ' . $candidate->LNAME; ?></h4>
                            <div class="candidate-position"><?php echo $candidate->OCCUPATIONTITLE; ?></div>
                            <div class="candidate-contact">
                                <small><?php echo $candidate->EMAILADDRESS; ?> | <?php echo $candidate->CONTACTNO; ?></small>
                            </div>
                        </div>
                        <div class="score-overview">
                            <div class="overall-score"><?php echo round($overallScore); ?>%</div>
                            <div class="score-label">Overall Score</div>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="candidate-body">
                    <div class="performance-grid">
                        <div class="performance-item">
                            <div class="performance-value"><?php echo round($expression_score); ?>%</div>
                            <div class="performance-label">Facial Expressions</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-value"><?php echo round($movement_score); ?>%</div>
                            <div class="performance-label">Body Language</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-value"><?php echo round($answer_score); ?>%</div>
                            <div class="performance-label">Answer Quality</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-value"><?php echo $candidate->INTERVIEW_COMPLETED_AT ? date('M d, Y', strtotime($candidate->INTERVIEW_COMPLETED_AT)) : 'N/A'; ?></div>
                            <div class="performance-label">Interview Date</div>
                        </div>
                    </div>
                    
                    <div class="candidate-actions">
                        <a href="interview-results.php?action=view&id=<?php echo $candidate->REGISTRATIONID; ?>" 
                           class="btn btn-action btn-view">
                            <i class="fas fa-eye"></i> View Full Report
                        </a>
                        <a href="?action=message&id=<?php echo $candidate->REGISTRATIONID; ?>" 
                           class="btn btn-action btn-message">
                            <i class="fas fa-envelope"></i> Send Message
                        </a>
                        <a href="?action=update_status&id=<?php echo $candidate->REGISTRATIONID; ?>&status=accepted" 
                           class="btn btn-action btn-accept">
                            <i class="fas fa-check"></i> Accept Candidate
                        </a>
                        <a href="?action=update_status&id=<?php echo $candidate->REGISTRATIONID; ?>&status=rejected" 
                           class="btn btn-action btn-reject">
                            <i class="fas fa-times"></i> Reject Candidate
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>No Interview Reports Yet</h3>
                <p>Completed interviews will appear here once candidates finish their AI interviews.</p>
            </div>
        <?php } ?>
    </div>
    
    <?php
    include('footer.php');
}

function sendMessageToCandidate() {
    global $mydb;
    
    if (!isset($_GET['id'])) {
        redirect('reports.php');
    }
    
    $registration_id = $_GET['id'];
    
    // Get candidate details
    $sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $candidate = $mydb->loadSingleResult();
    
    if (!$candidate) {
        redirect('reports.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = $_POST['message'];
        $subject = $_POST['subject'];
        
        // Send email to candidate
        $to = $candidate->EMAILADDRESS;
        $headers = "From: " . ADMIN_EMAIL . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $emailBody = "
        <html>
        <body>
            <h2>Interview Update - {$candidate->OCCUPATIONTITLE}</h2>
            <p>Dear {$candidate->FNAME} {$candidate->LNAME},</p>
            <p>{$message}</p>
            <p>Best regards,<br>Hiring Team</p>
        </body>
        </html>
        ";
        
        // Use the new professional email template system
        if (sendNotificationEmail($to, $candidate->FNAME . ' ' . $candidate->LNAME, $subject, $message, 'Hiring Team')) {
            message("Message sent successfully to {$candidate->FNAME} {$candidate->LNAME}", "success");
        } else {
            message("Failed to send message", "error");
        }
        
        redirect('reports.php');
    }
    
    include('header.php');
    ?>
    
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-envelope"></i> Send Message to Candidate</h3>
                    </div>
                    <div class="card-body">
                        <h4><?php echo $candidate->FNAME . ' ' . $candidate->LNAME; ?></h4>
                        <p><strong>Position:</strong> <?php echo $candidate->OCCUPATIONTITLE; ?></p>
                        <p><strong>Email:</strong> <?php echo $candidate->EMAILADDRESS; ?></p>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" name="subject" class="form-control" required 
                                       value="Interview Update - <?php echo $candidate->OCCUPATIONTITLE; ?>">
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="message" class="form-control" rows="8" required 
                                          placeholder="Enter your message to the candidate..."></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Send Message</button>
                                <a href="reports.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    include('footer.php');
}

function updateCandidateStatus() {
    global $mydb;
    
    if (!isset($_GET['id']) || !isset($_GET['status'])) {
        redirect('reports.php');
    }
    
    $registration_id = $_GET['id'];
    $status = $_GET['status'];
    
    // Update candidate status
    $sql = "UPDATE tbljobregistration SET CANDIDATE_STATUS = '{$status}' WHERE REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Get candidate details for email
    $sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $candidate = $mydb->loadSingleResult();
    
    if ($candidate) {
        // Send status update email using professional template
        $to = $candidate->EMAILADDRESS;
        $candidateName = $candidate->FNAME . ' ' . $candidate->LNAME;
        $positionTitle = $candidate->OCCUPATIONTITLE;
        $subject = "Interview Status Update - {$positionTitle}";
        
        $message = $status === 'accepted' ? 
            "Congratulations! We are pleased to inform you that you have been selected for the {$positionTitle} position." :
            "Thank you for your interest in the {$positionTitle} position. After careful consideration, we regret to inform you that we will not be moving forward with your application.";
        
        // Use the new professional email template system
        if (sendNotificationEmail($to, $candidateName, $subject, $message, 'Hiring Team')) {
            message("Candidate status updated to " . ucfirst($status), "success");
        } else {
            message("Failed to send status update email", "error");
        }
    }
    
    redirect('reports.php');
}

function viewCandidateReport() {
    // This would show detailed individual candidate report
    // Implementation similar to interview-results.php
    redirect('interview-results.php' . $_SERVER['QUERY_STRING']);
}
?> 