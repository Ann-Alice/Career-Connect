<?php 
  $id = isset($_GET['id']) ? $_GET['id'] :0;

$sql="UPDATE `tbljobregistration` SET HVIEW=1 WHERE `REGISTRATIONID`='{$id}'";
$mydb->setQuery($sql);
$mydb->executeQuery();

$sql = "SELECT jr.*, c.COMPANYNAME, j.OCCUPATIONTITLE, f.FEEDBACK 
        FROM tbljobregistration jr 
        JOIN tblcompany c ON jr.COMPANYID = c.COMPANYID 
        JOIN tbljob j ON jr.JOBID = j.JOBID 
        LEFT JOIN tblfeedback f ON jr.REGISTRATIONID = f.REGISTRATIONID 
        WHERE jr.REGISTRATIONID = '{$id}'";
$mydb->setQuery($sql);
$res = $mydb->loadSingleResult();

$applicant = new Applicants();
$appl = $applicant->single_applicant($_SESSION['APPLICANTID']);

// Get interview invitation if exists
$sql = "SELECT * FROM tblinterviewinvitations WHERE REGISTRATIONID = '{$id}' AND EXPIRY_DATE > NOW()";
$mydb->setQuery($sql);
$invitation = $mydb->loadSingleResult();
?> 

<style>
.message-header {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-bottom: 1px solid #dee2e6;
  padding: 25px;
  border-radius: 8px 8px 0 0;
  position: relative;
  overflow: hidden;
}
.message-header::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 150px;
  height: 150px;
  background: linear-gradient(45deg, transparent 0%, rgba(60, 141, 188, 0.1) 100%);
  border-radius: 0 0 0 150px;
}
.message-body {
  padding: 30px;
  background-color: #fff;
  border-radius: 0 0 8px 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.message-meta {
  color: #6c757d;
  font-size: 0.9em;
  display: flex;
  align-items: center;
  gap: 8px;
}
.message-content {
  margin-top: 25px;
  line-height: 1.8;
  color: #495057;
  font-size: 1.05em;
}
.message-content p {
  margin-bottom: 1.2em;
}
.interview-link {
  margin-top: 30px;
  padding: 20px;
  background: linear-gradient(135deg, #e8f4f8 0%, #d1e9f2 100%);
  border-radius: 8px;
  border-left: 4px solid #17a2b8;
  position: relative;
  overflow: hidden;
}
.interview-link::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: linear-gradient(45deg, transparent 0%, rgba(23, 162, 184, 0.1) 100%);
  border-radius: 0 0 0 100px;
}
.interview-link h5 {
  color: #17a2b8;
  font-weight: 600;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.interview-link .btn-primary {
  padding: 10px 25px;
  border-radius: 25px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
}
.interview-link .btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(23, 162, 184, 0.2);
}
.back-button {
  margin-bottom: 25px;
  padding: 8px 20px;
  border-radius: 20px;
  background: #fff;
  border: 2px solid #e9ecef;
  color: #6c757d;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.back-button:hover {
  background: #f8f9fa;
  border-color: #dee2e6;
  color: #495057;
  text-decoration: none;
}
.status-badge {
  padding: 6px 15px;
  border-radius: 20px;
  font-size: 0.9em;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.status-badge i {
  font-size: 0.9em;
}
.status-approved {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.status-rejected {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
.status-interview {
  background-color: #cce5ff;
  color: #004085;
  border: 1px solid #b8daff;
}
.status-pending {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}
.company-info {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
}
.company-logo {
  width: 50px;
  height: 50px;
  background: #fff;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.company-logo i {
  font-size: 1.8em;
  color: #3c8dbc;
}
</style>

  <div class="content-wrapper"> 
    <section class="content">
      <div class="row"> 
        <div class="col-md-12">
        <a href="index.php?view=message" class="back-button">
          <i class="fa fa-arrow-left"></i> Back to Messages
        </a>
        
          <div class="box box-primary">
          <div class="message-header">
            <div class="company-info">
              <div class="company-logo">
                <i class="fa fa-building"></i>
            </div>
              <div>
                <h3 class="box-title mb-1"><?php echo $res->COMPANYNAME; ?></h3>
                <h4 class="mt-0"><?php echo $res->OCCUPATIONTITLE; ?></h4>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6">
                <div class="message-meta">
                  <i class="fa fa-clock-o"></i>
                  <?php echo date('F d, Y h:i A', strtotime($res->DATETIMEAPPROVED)); ?>
                </div>
              </div>
              <div class="col-md-6 text-right">
                <span class="status-badge status-<?php echo strtolower($res->REMARKS); ?>">
                  <i class="fa fa-<?php echo strtolower($res->REMARKS) == 'approved' ? 'check-circle' : 
                    (strtolower($res->REMARKS) == 'rejected' ? 'times-circle' : 
                    (strtolower($res->REMARKS) == 'interview scheduled' ? 'calendar-check-o' : 'clock-o')); ?>"></i>
                  <?php echo ucfirst($res->REMARKS); ?>
                </span>
              </div>
            </div>
                  </div>
          
          <div class="message-body">
            <div class="message-content">
              <p>Hello <?php echo $appl->FNAME; ?>,</p>
              <p><?php echo isset($res->FEEDBACK) ? $res->FEEDBACK : $res->REMARKS; ?></p>
                  </div>
            
            <?php if ($invitation): ?>
            <div class="interview-link">
              <h5>
                <i class="fa fa-calendar-check-o"></i>
                Interview Invitation
              </h5>
              <p>You have been invited to complete an AI interview for this position. Please click the button below to start your interview.</p>
              <?php
              $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
              $host = $_SERVER['HTTP_HOST'];
              $interview_url = $protocol . $host . web_root . "interview.php?token=" . $invitation->TOKEN;
              ?>
              <a href="<?php echo $interview_url; ?>" class="btn btn-primary" target="_blank">
                <i class="fa fa-video-camera"></i> Start Interview
              </a>
              <small class="text-muted d-block mt-3">
                <i class="fa fa-clock-o"></i>
                This invitation expires on <?php echo date('F d, Y', strtotime($invitation->EXPIRY_DATE)); ?>
              </small>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    </section>
  </div>
  