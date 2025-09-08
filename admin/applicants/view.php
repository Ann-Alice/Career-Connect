<?php 
require_once("../../include/initialize.php");

global $mydb;

// Check if this is an AJAX request
$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

// Get the applicant ID parameter
$applicant_id = isset($_GET['id']) ? $_GET['id'] : '';

// Validate the ID parameter
if (empty($applicant_id) || !is_numeric($applicant_id)) {
	if ($is_ajax) {
		header('Content-Type: application/json');
		echo json_encode([
			'success' => false,
			'message' => 'Invalid or missing applicant ID.'
		]);
		exit;
	}
	echo '<div class="alert alert-danger">Invalid or missing applicant ID.</div>';
	echo '<a href="index.php" class="btn btn-default">Go Back</a>';
	exit;
}

// If AJAX request, fetch applicant directly
if ($is_ajax) {
	$applicant = new Applicants();
	$appl = $applicant->single_applicant($applicant_id);
	
	if (!$appl) {
		header('Content-Type: application/json');
		echo json_encode([
			'success' => false,
			'message' => 'Applicant not found.'
		]);
		exit;
	}
	
	// Return applicant data as JSON
	header('Content-Type: application/json');
	echo json_encode([
		'success' => true,
		'data' => [
			'APPLICANTID' => $appl->APPLICANTID,
			'FNAME' => $appl->FNAME,
			'LNAME' => $appl->LNAME,
			'MNAME' => $appl->MNAME,
			'ADDRESS' => $appl->ADDRESS,
			'SEX' => $appl->SEX,
			'CIVILSTATUS' => $appl->CIVILSTATUS,
			'BIRTHDATE' => $appl->BIRTHDATE,
			'BIRTHPLACE' => $appl->BIRTHPLACE,
			'AGE' => $appl->AGE,
			'EMAILADDRESS' => $appl->EMAILADDRESS,
			'CONTACTNO' => $appl->CONTACTNO,
			'DEGREE' => isset($appl->DEGREE) ? $appl->DEGREE : null
		]
	]);
	exit;
}

// For regular HTTP requests (non-AJAX), continue with original logic
$red_id = $applicant_id; // Use the same ID for job registration lookup

$jobregistration = New JobRegistration();
$jobreg = $jobregistration->single_jobregistration($red_id);
 // `COMPANYID`, `JOBID`, `APPLICANTID`, `APPLICANT`, `REGISTRATIONDATE`, `REMARKS`, `FILEID`, `PENDINGAPPLICATION`

// Check if job registration was found
if (!$jobreg) {
	echo '<div class="alert alert-danger">Job registration not found.</div>';
	echo '<a href="index.php" class="btn btn-default">Go Back</a>';
	exit;
}

$applicant = new Applicants();
$appl = $applicant->single_applicant($jobreg->APPLICANTID);
 // `FNAME`, `LNAME`, `MNAME`, `ADDRESS`, `SEX`, `CIVILSTATUS`, `BIRTHDATE`, `BIRTHPLACE`, `AGE`, `USERNAME`, `PASS`, `EMAILADDRESS`,CONTACTNO

// Check if applicant was found
if (!$appl) {
	echo '<div class="alert alert-danger">Applicant information not found.</div>';
	echo '<a href="index.php" class="btn btn-default">Go Back</a>';
	exit;
}

$jobvacancy = New Jobs();
$job = $jobvacancy->single_job($jobreg->JOBID);
 // `COMPANYID`, `CATEGORY`, `OCCUPATIONTITLE`, `REQ_NO_EMPLOYEES`, `SALARIES`, `DURATION_EMPLOYEMENT`, `QUALIFICATION_WORKEXPERIENCE`, `JOBDESCRIPTION`, `PREFEREDSEX`, `SECTOR_VACANCY`, `JOBSTATUS`, `DATEPOSTED`

// Check if job was found
if (!$job) {
	echo '<div class="alert alert-danger">Job information not found.</div>';
	echo '<a href="index.php" class="btn btn-default">Go Back</a>';
	exit;
}

$company = new Company();
$comp = $company->single_company($jobreg->COMPANYID);
 // `COMPANYNAME`, `COMPANYADDRESS`, `COMPANYCONTACTNO`

// Check if company was found
if (!$comp) {
	echo '<div class="alert alert-danger">Company information not found.</div>';
	echo '<a href="index.php" class="btn btn-default">Go Back</a>';
	exit;
}

// Handle attachment file - STRICT applicant-specific access only
// Following project specification: applicant-specific file access using USERATTACHMENTID
$attachmentfile = null;

// Method 1: Direct link by USERATTACHMENTID (ONLY method - ensures unique files per applicant)
$sql = "SELECT * FROM `tblattachmentfile` WHERE `USERATTACHMENTID` = " . intval($appl->APPLICANTID) . " AND `FILE_NAME` = 'Resume' ORDER BY ID DESC LIMIT 1";
$mydb->setQuery($sql);
$attachmentfile = $mydb->loadSingleResult();

// No fallback methods - this ensures strict applicant-specific file access
// Each applicant must have their own unique resume file linked via USERATTACHMENTID


?> 
<style type="text/css">
.content-header {
	min-height: 50px;
	border-bottom: 1px solid #ddd;
	font-size: 15px;
	font-weight: bold;
}
.content-body {
	min-height: 350px;
	/*border-bottom: 1px solid #ddd;*/
}
.content-body >p {
	padding:10px;
	font-size: 12px;
	font-weight: bold;
	border-bottom: 1px solid #ddd;
}
.content-footer {
	min-height: 100px;
	border-top: 1px solid #ddd;

}
.content-footer > p {
	padding:5px;
	font-size: 15px;
	font-weight: bold; 
}
 
.content-footer textarea {
	width: 100%;
	height: 200px;
}
.content-footer  .submitbutton{  
	margin-top: 20px;
	/*padding: 0;*/

}
.back-btn { margin-top: 20px; margin-bottom: 10px; }
</style>
<form action="controller.php?action=approve" method="POST">
<div class="col-sm-12 content-header">View Applicant Details
    <a href="index.php" class="btn btn-default btn-sm pull-right back-btn"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
</div>
<div class="col-sm-6 content-body" > 
	<p>Job Details</p> 
	<h3><?php echo htmlspecialchars($job->OCCUPATIONTITLE); ?></h3>
	<input type="hidden" name="JOBREGID" value="<?php echo htmlspecialchars($jobreg->REGISTRATIONID); ?>">
	<input type="hidden" name="APPLICANTID" value="<?php echo htmlspecialchars($appl->APPLICANTID); ?>">

	<div class="col-sm-6">
		<ul>
            <li>Required No. of Employees: <?php echo htmlspecialchars($job->REQ_NO_EMPLOYEES); ?></li>
            <li>Salary: <?php echo number_format($job->SALARIES,2); ?></li>
            <li>Duration of Employment: <?php echo htmlspecialchars($job->DURATION_EMPLOYEMENT); ?></li>
        </ul>
	</div> 
	<div class="col-sm-6">
		<ul> 
            <li>Preferred Sex: <?php echo htmlspecialchars($job->PREFEREDSEX); ?></li>
            <li>Sector of Vacancy: <?php echo htmlspecialchars($job->SECTOR_VACANCY); ?></li>
        </ul>
	</div>
	<div class="col-sm-12">
		<p>Job Description:</p>   
		<p style="margin-left: 15px;"><?php echo nl2br(htmlspecialchars($job->JOBDESCRIPTION)); ?></p>
	</div>
	<div class="col-sm-12"> 
		<p>Qualification/Work Experience:</p>
		<p style="margin-left: 15px;"><?php echo nl2br(htmlspecialchars($job->QUALIFICATION_WORKEXPERIENCE)); ?></p>
	</div>
	<div class="col-sm-12"> 
		<p>Employer:</p>
		<p style="margin-left: 15px;"><b><?php echo htmlspecialchars($comp->COMPANYNAME); ?></b></p> 
		<p style="margin-left: 15px;">@ <?php echo htmlspecialchars($comp->COMPANYADDRESS); ?></p>
	</div>
</div>
<div class="col-sm-6 content-body" >
	<p>Applicant Information</p> 
	<h3><?php echo htmlspecialchars($appl->LNAME . ', ' . $appl->FNAME . ' ' . $appl->MNAME); ?></h3>
	<ul> 
		<li>Address: <?php echo htmlspecialchars($appl->ADDRESS); ?></li>
		<li>Contact No.: <?php echo htmlspecialchars($appl->CONTACTNO); ?></li>
		<li>Email Address: <?php echo htmlspecialchars($appl->EMAILADDRESS); ?></li>
		<li>Sex: <?php echo htmlspecialchars($appl->SEX); ?></li>
		<li>Age: <?php echo htmlspecialchars($appl->AGE); ?></li> 
	</ul>
	<div class="col-sm-12"> 
		<p>Educational Attainment:</p>
		<p style="margin-left: 15px;"><?php echo htmlspecialchars($appl->DEGREE); ?></p>
	</div>


</div> 
<div class="col-sm-12 content-footer">
<p><i class="fa fa-paperclip"></i>  Attachment Files</p>
	<div class="col-sm-12 slider">
		<?php if ($attachmentfile && !empty($attachmentfile->FILE_LOCATION)): ?>
			<h3>Download Resume 
				<a href="download-resume.php?id=<?php echo htmlspecialchars($appl->APPLICANTID); ?>" class="btn btn-primary btn-sm"><i class="fa fa-file-pdf-o"></i> Download</a>
			</h3>
		<?php else: ?>
			<h3>No resume file attached</h3>
		<?php endif; ?>
	</div>

 

	<div class="col-sm-12">
		<p>Feedback</p>
		<textarea class="input-group" name="REMARKS"><?php echo isset($jobreg->REMARKS) ? htmlspecialchars($jobreg->REMARKS) : ""; ?></textarea>
	</div>
	<div class="col-sm-12  submitbutton "> 
		<button type="submit" name="submit" class="btn btn-primary">Send</button>
	</div> 
</div>
</form>