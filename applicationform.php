<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application</title>
</head>

<section id="content">
  <div class="container content">     
    <?php
    if (isset($_GET['search'])) {
      $jobid = $_GET['search'];
    } else {
      $jobid = '';
    }
    $sql = "SELECT * FROM `tblcompany` c,`tbljob` j WHERE c.`COMPANYID`=j.`COMPANYID` AND JOBID LIKE '%" . $jobid ."%' ORDER BY DATEPOSTED DESC";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    ?> 

    <p><?php check_message();?></p>     
    <?php if (isset($_SESSION['APPLICANTID'])) { ?>
      <div class="col-sm-12">
        <div class="row">
          <div class="job-details-card">
            <div class="job-header">
              <h2><i class="fa fa-briefcase"></i> Job Details</h2>
              <div class="job-title">
                <a href="<?php echo web_root.'index.php?q=viewjob&search='.$result->JOBID;?>">
                  <?php echo $result->OCCUPATIONTITLE;?>
                </a>
              </div> 
            </div>
            <div class="job-body">
              <div class="row contentbody">
                <div class="col-sm-6">
                  <div class="info-section">
                    <h4><i class="fa fa-users"></i> Position Details</h4>
                    <ul class="job-info-list">
                      <li><i class="fa fa-user-plus"></i> Required Employees: <?php echo $result->REQ_NO_EMPLOYEES; ?></li>
                      <li><i class="fa fa-money"></i> Salary: <?php echo number_format($result->SALARIES,2); ?></li>
                      <li><i class="fa fa-clock-o"></i> Duration: <?php echo $result->DURATION_EMPLOYEMENT; ?></li>
                    </ul>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-section">
                    <h4><i class="fa fa-info-circle"></i> Additional Info</h4>
                    <ul class="job-info-list">
                      <li><i class="fa fa-venus-mars"></i> Preferred Sex: <?php echo $result->PREFEREDSEX; ?></li>
                      <li><i class="fa fa-building"></i> Sector: <?php echo $result->SECTOR_VACANCY; ?></li>
                    </ul>
                  </div>
                </div>
                <div class="col-sm-12"> 
                  <div class="info-section">
                    <h4><i class="fa fa-graduation-cap"></i> Requirements</h4>
                    <div class="requirement-content">
                      <?php echo $result->QUALIFICATION_WORKEXPERIENCE; ?>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="info-section">
                    <h4><i class="fa fa-list-alt"></i> Job Description</h4>
                    <div class="description-content">
                      <?php echo $result->JOBDESCRIPTION; ?>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="company-info">
                    <h4><i class="fa fa-building-o"></i> Company Information</h4>
                    <p><strong>Employer:</strong> <?php echo $result->COMPANYNAME; ?></p>
                    <p><strong>Location:</strong> <?php echo $result->COMPANYADDRESS; ?></p>
                  </div>
                </div>
              </div>
            </div> 
            <div class="job-footer">
              <span class="date-posted"><i class="fa fa-calendar"></i> Posted: <?php echo date_format(date_create($result->DATEPOSTED),'M d, Y'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <form class="form-horizontal span6" action="process.php?action=submitapplication&JOBID=<?php echo $result->JOBID; ?>" enctype="multipart/form-data" method="POST">
        <div class="col-sm-12">
          <div class="row">
            <div class="resume-upload-card">
              <div class="upload-header">
                <h3><i class="fa fa-file-text-o"></i> Resume Upload</h3>
                <input name="JOBID" type="hidden" value="<?php echo $_GET['job'];?>">
              </div>
              <div class="upload-body">
                <div class="file-upload-wrapper">
                  <label for="picture" class="file-upload-label">
                    <i class="fa fa-cloud-upload"></i> Choose Resume File
                  </label>
                  <input id="picture" name="picture" type="file" class="file-upload-input" accept=".pdf,.doc,.docx">
                  <input name="MAX_FILE_SIZE" type="hidden" value="1000000"> 
                  <div class="file-info">Supported formats: PDF, DOC, DOCX (Max size: 1MB)</div>
                </div> 
              </div>
            </div> 
          </div> 
        </div>
        <div class="form-group">
          <div class="col-md-12"> 
            <button class="btn btn-primary btn-submit" name="submit" type="submit">
              Submit Application <i class="fa fa-arrow-right"></i>
            </button>
            <a href="index.php" class="btn btn-default btn-back">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>
        </div> 
      </form>
    <?php } else { ?>
      <form class="form-horizontal span6 wow fadeInDown" action="process.php?action=submitapplication&JOBID=<?php echo $result->JOBID; ?>" enctype="multipart/form-data" method="POST">
        <div class="col-sm-8"> 
          <div class="row">
            <div class="personal-info-card">
              <h2><i class="fa fa-user"></i> Personal Information</h2>
              <?php require_once('applicantform.php') ?>   
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="row">
            <div class="job-details-card">
              <div class="job-header">
                <h2><i class="fa fa-briefcase"></i> Job Details</h2>
                <div class="job-title">
                  <a href="<?php echo web_root.'index.php?q=viewjob&search='.$result->JOBID;?>">
                    <?php echo $result->OCCUPATIONTITLE;?>
                  </a>
                </div> 
              </div>
              <div class="job-body">
                <div class="row contentbody">
                  <div class="col-sm-12">
                    <div class="info-section">
                      <h4><i class="fa fa-users"></i> Position Details</h4>
                      <ul class="job-info-list">
                        <li><i class="fa fa-user-plus"></i> Required: <?php echo $result->REQ_NO_EMPLOYEES; ?></li>
                        <li><i class="fa fa-money"></i> Salary: <?php echo number_format($result->SALARIES,2); ?></li>
                        <li><i class="fa fa-clock-o"></i> Duration: <?php echo $result->DURATION_EMPLOYEMENT; ?></li>
                        <li><i class="fa fa-venus-mars"></i> Sex: <?php echo $result->PREFEREDSEX; ?></li>
                        <li><i class="fa fa-building"></i> Sector: <?php echo $result->SECTOR_VACANCY; ?></li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="company-info">
                      <h4><i class="fa fa-building-o"></i> Company</h4>
                      <p><strong><?php echo $result->COMPANYNAME; ?></strong></p>
                      <p><i class="fa fa-map-marker"></i> <?php echo $result->COMPANYADDRESS; ?></p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="job-footer">
                <span class="date-posted"><i class="fa fa-calendar"></i> Posted: <?php echo date_format(date_create($result->DATEPOSTED),'M d, Y'); ?></span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="row">
            <div class="resume-upload-card">
              <div class="upload-header">
                <h3><i class="fa fa-file-text-o"></i> Resume Upload</h3>
              </div>
              <div class="upload-body">
                <div class="file-upload-wrapper">
                  <label for="picture" class="file-upload-label">
                    <i class="fa fa-cloud-upload"></i> Choose Resume File
                  </label>
                  <input id="picture" name="picture" type="file" class="file-upload-input" accept=".pdf,.doc,.docx">
                  <input name="MAX_FILE_SIZE" type="hidden" value="1000000"> 
                  <div class="file-info">Supported formats: PDF, DOC, DOCX (Max size: 1MB)</div>
                </div> 
              </div>
            </div> 
          </div> 
        </div>
        <div class="form-group">
          <div class="col-md-12"> 
            <button class="btn btn-primary btn-submit" name="submit" type="submit">
              Submit Application <i class="fa fa-arrow-right"></i>
            </button>
            <a href="index.php" class="btn btn-default btn-back">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>
        </div>   
      </form> 
    <?php } ?>
  </div> 
</section> 

<style>
/* Card Styles */
.job-details-card, .resume-upload-card, .personal-info-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  overflow: hidden;
}

/* Header Styles */
.job-header, .upload-header {
  background: linear-gradient(135deg, #3498db, #2980b9);
  color: white;
  padding: 20px;
}

.job-header h2, .upload-header h3 {
  margin: 0;
  font-size: 1.5em;
  font-weight: 600;
}

.job-header h2 i, .upload-header h3 i {
  margin-right: 10px;
}

.job-title {
  margin-top: 10px;
  font-size: 1.2em;
}

.job-title a {
  color: white;
  text-decoration: none;
  transition: color 0.3s ease;
}

.job-title a:hover {
  color: #ecf0f1;
}

/* Body Styles */
.job-body, .upload-body {
  padding: 20px;
}

.info-section {
  margin-bottom: 20px;
}

.info-section h4 {
  color: #2c3e50;
  font-size: 1.1em;
  margin-bottom: 15px;
  padding-bottom: 5px;
  border-bottom: 2px solid #3498db;
}

.info-section h4 i {
  color: #3498db;
  margin-right: 8px;
}

.job-info-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.job-info-list li {
  margin-bottom: 10px;
  color: #34495e;
}

.job-info-list li i {
  color: #3498db;
  margin-right: 8px;
  width: 20px;
  text-align: center;
}

.requirement-content, .description-content {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 4px;
  border-left: 4px solid #3498db;
}

.company-info {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 4px;
}

.company-info h4 {
  color: #2c3e50;
  margin-bottom: 10px;
}

.company-info p {
  margin: 5px 0;
  color: #34495e;
}

/* Footer Styles */
.job-footer {
  background: #f8f9fa;
  padding: 15px 20px;
  border-top: 1px solid #eee;
}

.date-posted {
  color: #7f8c8d;
  font-size: 0.9em;
}

.date-posted i {
  margin-right: 5px;
}

/* File Upload Styles */
.file-upload-wrapper {
  text-align: center;
  padding: 20px;
  border: 2px dashed #3498db;
  border-radius: 4px;
  background: #f8f9fa;
  transition: all 0.3s ease;
}

.file-upload-wrapper:hover {
  border-color: #2980b9;
  background: #ecf0f1;
}

.file-upload-label {
  display: inline-block;
  padding: 10px 20px;
  background: #3498db;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.file-upload-label:hover {
  background: #2980b9;
}

.file-upload-input {
  display: none;
}

.file-info {
  margin-top: 10px;
  color: #7f8c8d;
  font-size: 0.9em;
}

/* Button Styles */
.btn-submit, .btn-back {
  padding: 10px 20px;
  border-radius: 4px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-submit {
  background: linear-gradient(135deg, #3498db, #2980b9);
  border: none;
  color: white;
}

.btn-submit:hover {
  background: linear-gradient(135deg, #2980b9, #2c3e50);
  transform: translateY(-2px);
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.btn-back {
  background: #ecf0f1;
  color: #34495e;
  margin-right: 10px;
}

.btn-back:hover {
  background: #bdc3c7;
  color: #2c3e50;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .job-header, .upload-header {
    padding: 15px;
  }
  
  .job-body, .upload-body {
    padding: 15px;
  }
  
  .file-upload-wrapper {
    padding: 15px;
  }
  
  .btn-submit, .btn-back {
    width: 100%;
    margin-bottom: 10px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // File upload preview
  const fileInput = document.querySelector('.file-upload-input');
  const fileLabel = document.querySelector('.file-upload-label');
  const fileInfo = document.querySelector('.file-info');

  fileInput.addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
      fileLabel.innerHTML = `<i class="fa fa-file"></i> ${fileName}`;
      fileInfo.style.color = '#27ae60';
      fileInfo.innerHTML = 'File selected successfully';
    }
  });

  // Form validation
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
    const fileInput = document.getElementById('picture');
    if (!fileInput.files.length) {
      e.preventDefault();
      alert('Please select a resume file to upload');
    }
  });
});
</script>
  