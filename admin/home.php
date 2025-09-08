<?php
// Create database connection directly using mysqli
if (!isset($mydb) || !$mydb) {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'erisdb';
    $port = 4306;
    
    $conn = mysqli_connect($host, $username, $password, $database, $port);
    
    if (!$conn) {
        echo "<div class='alert alert-danger'>Database connection failed: " . mysqli_connect_error() . "</div>";
        return;
    }
    
    $mydb = new stdClass();
    $mydb->conn = $conn;
}

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_candidates,
    COUNT(CASE WHEN INTERVIEW_STATUS = 'Completed' THEN 1 END) as completed_interviews,
    COUNT(CASE WHEN ADMIN_GRADE IS NOT NULL THEN 1 END) as graded_interviews,
    AVG(CASE WHEN JSON_EXTRACT(INTERVIEW_RESULTS, '$.overall_score') IS NOT NULL 
        THEN CAST(JSON_EXTRACT(INTERVIEW_RESULTS, '$.overall_score') AS DECIMAL(5,2)) END) as avg_ai_score
FROM tbljobregistration 
WHERE INTERVIEW_STATUS IS NOT NULL";

$stats_result = mysqli_query($mydb->conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

// Get recent interviews
$recent_sql = "SELECT r.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME,
                       JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') as ai_score
                FROM tbljobregistration r 
                JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                JOIN tbljob j ON r.JOBID = j.JOBID 
                JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                WHERE r.INTERVIEW_STATUS = 'Completed' 
                ORDER BY r.INTERVIEW_COMPLETED_AT DESC 
                LIMIT 6";

$recent_result = mysqli_query($mydb->conn, $recent_sql);
?>

<!-- Enhanced Content Header -->
<section class="content-header" style="background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%); padding: 20px; border-radius: 0; margin-bottom: 0; box-shadow: none; position: relative;">
  <div class="row">
    <div class="col-md-8">
      <h1 style="font-size: 24px; color: white; margin: 0; display: flex; align-items: center;">
        <i class="fa fa-tachometer" style="margin-right: 10px;"></i> Dashboard
        <small style="display: block; margin-left: 15px; color: rgba(255,255,255,0.8); font-size: 14px;">
          Control panel
        </small>
      </h1>
    </div>
    <div class="col-md-4 text-right">
      <nav style="color: rgba(255,255,255,0.8); font-size: 14px;">
        <i class="fa fa-home"></i> Home > Dashboard
      </nav>
    </div>
  </div>
</section>

<!-- Main content -->
<section class="content">
  
  <!-- Statistics Dashboard -->
  <div class="row" style="margin-bottom: 25px;">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="padding: 10px;">
      <div class="info-box" style="background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%); border-radius: 15px; box-shadow: 0 8px 25px rgba(108,92,231,0.3); transition: all 0.3s ease; padding: 25px; text-align: center; position: relative;">
        <div style="position: absolute; top: 15px; right: 15px; color: rgba(255,255,255,0.3); font-size: 40px;">
          <i class="fa fa-users"></i>
        </div>
        <div style="color: white; font-size: 36px; font-weight: 700; margin-bottom: 5px;"><?php echo $stats['total_candidates'] ?? 9; ?></div>
        <div style="color: rgba(255,255,255,0.9); font-size: 14px; font-weight: 600;">Total Candidates</div>
        <div style="margin-top: 15px;">
          <a href="#" style="color: rgba(255,255,255,0.8); font-size: 12px; text-decoration: none;">View All <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="padding: 10px;">
      <div class="info-box" style="background: linear-gradient(135deg, #00b894 0%, #00cec9 100%); border-radius: 15px; box-shadow: 0 8px 25px rgba(0,184,148,0.3); transition: all 0.3s ease; padding: 25px; text-align: center; position: relative;">
        <div style="position: absolute; top: 15px; right: 15px; color: rgba(255,255,255,0.3); font-size: 40px;">
          <i class="fa fa-check-circle"></i>
        </div>
        <div style="color: white; font-size: 36px; font-weight: 700; margin-bottom: 5px;"><?php echo $stats['completed_interviews'] ?? 0; ?></div>
        <div style="color: rgba(255,255,255,0.9); font-size: 14px; font-weight: 600;">Completed Interviews</div>
        <div style="margin-top: 15px;">
          <a href="#" style="color: rgba(255,255,255,0.8); font-size: 12px; text-decoration: none;">View Results <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="padding: 10px;">
      <div class="info-box" style="background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%); border-radius: 15px; box-shadow: 0 8px 25px rgba(253,121,168,0.3); transition: all 0.3s ease; padding: 25px; text-align: center; position: relative;">
        <div style="position: absolute; top: 15px; right: 15px; color: rgba(255,255,255,0.3); font-size: 40px;">
          <i class="fa fa-clock-o"></i>
        </div>
        <div style="color: white; font-size: 36px; font-weight: 700; margin-bottom: 5px;"><?php echo $stats['graded_interviews'] ?? 0; ?></div>
        <div style="color: rgba(255,255,255,0.9); font-size: 14px; font-weight: 600;">In Progress</div>
        <div style="margin-top: 15px;">
          <a href="#" style="color: rgba(255,255,255,0.8); font-size: 12px; text-decoration: none;">Monitor <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="padding: 10px;">
      <div class="info-box" style="background: linear-gradient(135deg, #fab1a0 0%, #e17055 100%); border-radius: 15px; box-shadow: 0 8px 25px rgba(250,177,160,0.3); transition: all 0.3s ease; padding: 25px; text-align: center; position: relative;">
        <div style="position: absolute; top: 15px; right: 15px; color: rgba(255,255,255,0.3); font-size: 40px;">
          <i class="fa fa-percent"></i>
        </div>
        <div style="color: white; font-size: 36px; font-weight: 700; margin-bottom: 5px;">
          <?php echo $stats['avg_ai_score'] ? round($stats['avg_ai_score'], 0) . '%' : '0%'; ?>
        </div>
        <div style="color: rgba(255,255,255,0.9); font-size: 14px; font-weight: 600;">Success Rate</div>
        <div style="margin-top: 15px;">
          <a href="#" style="color: rgba(255,255,255,0.8); font-size: 12px; text-decoration: none;">View Analytics <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Action Cards -->
  <div class="row" style="margin-bottom: 25px;">
    <div class="col-md-12">
      <div class="box box-solid" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <div class="box-header with-border" style="border-bottom: 2px solid #dee2e6; padding: 25px 25px 15px 25px;">
          <h3 class="box-title" style="font-size: 1.5rem; color: #495057; font-weight: 600;">
            <i class="fa fa-rocket" style="color: #667eea; margin-right: 10px;"></i>
            Quick Actions & Navigation
          </h3>
        </div>
        <div class="box-body" style="padding: 25px;">
          <div class="row">
            <div class="col-md-4" style="margin-bottom: 20px;">
              <div class="action-card" style="background: white; border-radius: 15px; padding: 30px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <div class="action-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; box-shadow: 0 8px 25px rgba(102,126,234,0.3);">
                  <i class="fa fa-star" style="color: white; font-size: 2rem;"></i>
                </div>
                <h4 style="color: #495057; margin-bottom: 15px; font-weight: 600;">Grade Interviews</h4>
                <p style="color: #6c757d; margin-bottom: 20px; line-height: 1.6;">Review AI analysis results and provide comprehensive grades</p>
                <a href="interview-results.php" class="btn btn-primary btn-lg" style="width: 100%; border-radius: 10px; padding: 12px; font-weight: 600; text-transform: uppercase;">
                  <i class="fa fa-arrow-right"></i> Start Grading
                </a>
              </div>
            </div>
            
            <div class="col-md-4" style="margin-bottom: 20px;">
              <div class="action-card" style="background: white; border-radius: 15px; padding: 30px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <div class="action-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; box-shadow: 0 8px 25px rgba(17,153,142,0.3);">
                  <i class="fa fa-envelope" style="color: white; font-size: 2rem;"></i>
                </div>
                <h4 style="color: #495057; margin-bottom: 15px; font-weight: 600;">Send Invitations</h4>
                <p style="color: #6c757d; margin-bottom: 20px; line-height: 1.6;">Invite qualified candidates to participate in AI interviews</p>
                <a href="interview-invitation.php" class="btn btn-success btn-lg" style="width: 100%; border-radius: 10px; padding: 12px; font-weight: 600; text-transform: uppercase;">
                  <i class="fa fa-arrow-right"></i> Send Invites
                </a>
              </div>
            </div>
            
            <div class="col-md-4" style="margin-bottom: 20px;">
              <div class="action-card" style="background: white; border-radius: 15px; padding: 30px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <div class="action-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; box-shadow: 0 8px 25px rgba(79,172,254,0.3);">
                  <i class="fa fa-arrow-left" style="color: white; font-size: 2rem;"></i>
                </div>
                <h4 style="color: #495057; margin-bottom: 15px; font-weight: 600;">Main Dashboard</h4>
                <p style="color: #6c757d; margin-bottom: 20px; line-height: 1.6;">Return to the main admin panel for comprehensive management</p>
                <a href="../" class="btn btn-info btn-lg" style="width: 100%; border-radius: 10px; padding: 12px; font-weight: 600; text-transform: uppercase;">
                  <i class="fa fa-arrow-right"></i> Go to Dashboard
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Two Column Layout: Candidates Progress Tracking & Alerts -->
  <div class="row">
    <!-- Left Column: Candidates Progress Tracking -->
    <div class="col-md-8">
      <div class="box box-solid" style="background: white; border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 25px;">
        <div class="box-header with-border" style="border-bottom: 2px solid #dee2e6; padding: 25px 25px 15px 25px;">
          <h3 class="box-title" style="font-size: 1.5rem; color: #495057; font-weight: 600;">
            <i class="fa fa-users" style="color: #667eea; margin-right: 10px;"></i>
            Candidates Progress Tracking
          </h3>
          <div class="box-tools pull-right">
            <a href="interview-results.php" class="btn btn-primary" style="border-radius: 10px; padding: 8px 20px; font-weight: 600;">
              <i class="fa fa-eye"></i> View All
            </a>
          </div>
        </div>
        <div class="box-body" style="padding: 25px;">
          <?php if (!$recent_result || mysqli_num_rows($recent_result) == 0): ?>
            <div class="text-center" style="padding: 40px 20px;">
              <i class="fa fa-inbox" style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;"></i>
              <h4 style="color: #6c757d; margin-bottom: 15px;">No Candidates Yet</h4>
              <p style="color: #6c757d; margin-bottom: 25px;">Start tracking candidate progress by inviting them to interviews.</p>
              <a href="interview-invitation.php" class="btn btn-primary btn-lg" style="border-radius: 10px; padding: 12px 25px;">
                <i class="fa fa-plus"></i> Invite Candidates
              </a>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover" style="margin-bottom: 0;">
                <thead>
                  <tr style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <th style="border: none; padding: 15px; font-weight: 600; color: #495057;">Candidate</th>
                    <th style="border: none; padding: 15px; font-weight: 600; color: #495057;">Position</th>
                    <th style="border: none; padding: 15px; font-weight: 600; color: #495057;">Progress</th>
                    <th style="border: none; padding: 15px; font-weight: 600; color: #495057;">Score</th>
                    <th style="border: none; padding: 15px; font-weight: 600; color: #495057;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $count = 0;
                  while (($interview = mysqli_fetch_object($recent_result)) && $count < 5): 
                    $ai_score = $interview->ai_score ? round($interview->ai_score, 1) . '%' : 'N/A';
                    $admin_grade = json_decode($interview->ADMIN_GRADE, true);
                    $status = $admin_grade ? 'Completed' : 'In Review';
                    $status_class = $admin_grade ? 'success' : 'warning';
                    $count++;
                  ?>
                    <tr style="transition: all 0.3s ease;">
                      <td style="padding: 15px; border: none;">
                        <div style="display: flex; align-items: center;">
                          <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <span style="color: white; font-weight: 600; font-size: 14px;">
                              <?php echo strtoupper(substr($interview->FNAME, 0, 1) . substr($interview->LNAME, 0, 1)); ?>
                            </span>
                          </div>
                          <div>
                            <strong style="color: #495057; display: block;"><?php echo $interview->FNAME . ' ' . $interview->LNAME; ?></strong>
                            <small style="color: #6c757d;"><?php echo $interview->COMPANYNAME; ?></small>
                          </div>
                        </div>
                      </td>
                      <td style="padding: 15px; border: none;">
                        <span style="color: #495057; font-weight: 500;"><?php echo $interview->OCCUPATIONTITLE; ?></span>
                      </td>
                      <td style="padding: 15px; border: none;">
                        <span class='label label-<?php echo $status_class; ?>' style='padding: 8px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;'>
                          <?php echo $status; ?>
                        </span>
                      </td>
                      <td style="padding: 15px; border: none;">
                        <span class='label label-info' style='padding: 8px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;'>
                          <?php echo $ai_score; ?>
                        </span>
                      </td>
                      <td style='padding: 15px; border: none;'>
                        <a href='interview-results.php?action=view&id=<?php echo $interview->REGISTRATIONID; ?>' 
                           class='btn btn-xs btn-primary' style='border-radius: 8px; padding: 6px 12px;'>
                          <i class='fa fa-eye'></i> View
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Right Column: Alerts & Notifications -->
    <div class="col-md-4">
      <div class="box box-solid" style="background: white; border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 25px;">
        <div class="box-header with-border" style="border-bottom: 2px solid #dee2e6; padding: 25px 25px 15px 25px;">
          <h3 class="box-title" style="font-size: 1.5rem; color: #495057; font-weight: 600;">
            <i class="fa fa-bell" style="color: #667eea; margin-right: 10px;"></i>
            Alerts & Notifications
          </h3>
        </div>
        <div class="box-body" style="padding: 25px;">
          <!-- Notification Item 1 -->
          <div class="notification-item" style="background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%); border-radius: 15px; padding: 20px; margin-bottom: 15px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
            <div style="display: flex; align-items: center;">
              <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <i class="fa fa-user-plus" style="font-size: 20px;"></i>
              </div>
              <div>
                <h5 style="margin: 0 0 5px 0; font-weight: 600;">New Candidate</h5>
                <small style="opacity: 0.9;">John Doe applied for Software Developer</small>
              </div>
            </div>
          </div>

          <!-- Notification Item 2 -->
          <div class="notification-item" style="background: linear-gradient(135deg, #00b894 0%, #00cec9 100%); border-radius: 15px; padding: 20px; margin-bottom: 15px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
            <div style="display: flex; align-items: center;">
              <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <i class="fa fa-check-circle" style="font-size: 20px;"></i>
              </div>
              <div>
                <h5 style="margin: 0 0 5px 0; font-weight: 600;">Interview Completed</h5>
                <small style="opacity: 0.9;">Sarah Smith finished AI interview</small>
              </div>
            </div>
          </div>

          <!-- Notification Item 3 -->
          <div class="notification-item" style="background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%); border-radius: 15px; padding: 20px; margin-bottom: 15px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
            <div style="display: flex; align-items: center;">
              <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <i class="fa fa-star" style="font-size: 20px;"></i>
              </div>
              <div>
                <h5 style="margin: 0 0 5px 0; font-weight: 600;">Grading Required</h5>
                <small style="opacity: 0.9;">5 interviews need admin review</small>
              </div>
            </div>
          </div>

          <!-- Notification Item 4 -->
          <div class="notification-item" style="background: linear-gradient(135deg, #fab1a0 0%, #e17055 100%); border-radius: 15px; padding: 20px; margin-bottom: 15px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
            <div style="display: flex; align-items: center;">
              <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <i class="fa fa-calendar" style="font-size: 20px;"></i>
              </div>
              <div>
                <h5 style="margin: 0 0 5px 0; font-weight: 600;">System Update</h5>
                <small style="opacity: 0.9;">New AI features available</small>
              </div>
            </div>
          </div>

          <!-- View All Link -->
          <div class="text-center" style="margin-top: 20px;">
            <a href="#" style="color: #667eea; text-decoration: none; font-weight: 600;">
              View All Notifications <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<style>
.action-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.info-box:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.table tbody tr:hover {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  transform: scale(1.01);
}

.notification-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
</style>

<script>
$(document).ready(function() {
  // Enhanced hover effects
  $('.action-card').hover(
    function() {
      $(this).find('.action-icon').css('transform', 'scale(1.1) rotate(5deg)');
    },
    function() {
      $(this).find('.action-icon').css('transform', 'scale(1) rotate(0deg)');
    }
  );
  
  // Notification hover effects
  $('.notification-item').hover(
    function() {
      $(this).css('transform', 'translateY(-2px) scale(1.02)');
    },
    function() {
      $(this).css('transform', 'translateY(0) scale(1)');
    }
  );
});
</script>

<?php
// Include footer
include('footer.php');
?>
  