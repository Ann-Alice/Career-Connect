<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])){
  redirect(web_root."admin/index.php");
 }


$jobid = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;
if (!$jobid) {
    echo '<div class="alert alert-danger">Invalid job ID.</div>';
    exit;
}

$job = new Jobs();
$res = $job->single_job($jobid);
if (!$res) {
    echo '<div class="alert alert-danger">Job not found.</div>';
    exit;
}

?> 
<form class="form-horizontal span6" action="controller.php?action=edit" method="POST" id="editJobForm" novalidate>

  <div class="row">
                   <div class="col-lg-12">
                      <h1 class="page-header">Update Job Vacancy</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                 </div> 

                 <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "COMPANYNAME">Company Name:</label>

                      <div class="col-md-8">
                        <input type="hidden" name="JOBID" value="<?php echo htmlspecialchars($res->JOBID); ?>">
                        <select class="form-control input-sm" id="COMPANYID" name="COMPANYID" required>
                          <option value="">Select</option>
                          <?php 
                            $sql = "SELECT * FROM tblcompany ORDER BY COMPANYNAME";
                            $mydb->setQuery($sql);
                            $companies = $mydb->loadResultList();
                            foreach ($companies as $company) {
                              $selected = ($company->COMPANYID == $res->COMPANYID) ? 'selected' : '';
                              echo '<option value="' . htmlspecialchars($company->COMPANYID) . '" ' . $selected . '>' . htmlspecialchars($company->COMPANYNAME) . '</option>';
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>  
                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "CATEGORY">Category:</label>

                      <div class="col-md-8"> 
                        <select class="form-control input-sm" id="CATEGORY" name="CATEGORY" required>
                          <option value="">Select</option>
                          <?php 
                            $sql = "SELECT * FROM tblcategory ORDER BY CATEGORY";
                            $mydb->setQuery($sql);
                            $categories = $mydb->loadResultList();
                            foreach ($categories as $cat) {
                              $selected = ($cat->CATEGORY == $res->CATEGORY) ? 'selected' : '';
                              echo '<option value="' . htmlspecialchars($cat->CATEGORY) . '" ' . $selected . '>' . htmlspecialchars($cat->CATEGORY) . '</option>';
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "OCCUPATIONTITLE">Occupation Title:</label> 
                      <div class="col-md-8">
                         <input class="form-control input-sm" id="OCCUPATIONTITLE" name="OCCUPATIONTITLE" placeholder="Occupation Title"   autocomplete="none" required minlength="3" value="<?php echo htmlspecialchars($res->OCCUPATIONTITLE); ?>"/> 
                      </div>
                    </div>
                  </div>  

                    <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "REQ_NO_EMPLOYEES">Required no. of Employees:</label> 
                      <div class="col-md-8">
                         <input type="number" class="form-control input-sm" id="REQ_NO_EMPLOYEES" name="REQ_NO_EMPLOYEES" placeholder="Required no. of Employees"   autocomplete="none" required min="1" value="<?php echo htmlspecialchars($res->REQ_NO_EMPLOYEES); ?>"/> 
                      </div>
                    </div>
                  </div>  

                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "SALARIES">Salary:</label> 
                      <div class="col-md-8">
                         <input type="number" class="form-control input-sm" id="SALARIES" name="SALARIES" placeholder="Salary (numeric only)" min="0" step="0.01" autocomplete="none" required value="<?php echo htmlspecialchars($res->SALARIES); ?>"/> 
                      </div>
                    </div>
                  </div>  

                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "DURATION_EMPLOYEMENT">Duration of Employment:</label> 
                      <div class="col-md-8">
                         <input class="form-control input-sm" id="DURATION_EMPLOYEMENT" name="DURATION_EMPLOYEMENT" placeholder="Duration of Employment"   autocomplete="none" required value="<?php echo htmlspecialchars($res->DURATION_EMPLOYEMENT); ?>"/> 
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "QUALIFICATION_WORKEXPERIENCE">Qualification/Work Experience:</label> 
                      <div class="col-md-8">
                        <textarea class="form-control input-sm" id="QUALIFICATION_WORKEXPERIENCE" name="QUALIFICATION_WORKEXPERIENCE" placeholder="Qualification/Work Experience"   autocomplete="none" required rows="2"><?php echo htmlspecialchars($res->QUALIFICATION_WORKEXPERIENCE); ?></textarea> 
                      </div>
                    </div>
                  </div> 

                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "JOBDESCRIPTION">Job Description:</label> 
                      <div class="col-md-8">
                        <textarea class="form-control input-sm" id="JOBDESCRIPTION" name="JOBDESCRIPTION" placeholder="Job Description"   autocomplete="none" required rows="2"><?php echo htmlspecialchars($res->JOBDESCRIPTION); ?></textarea> 
                      </div>
                    </div>
                  </div>  

                 <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "PREFEREDSEX">Prefered Sex:</label> 
                      <div class="col-md-8">
                          <select class="form-control input-sm" id="PREFEREDSEX" name="PREFEREDSEX" required>
                          <option value="">Select</option>
                           <option value="Male" <?php echo ($res->PREFEREDSEX=='Male') ? 'selected' : ''; ?>>Male</option>
                           <option value="Female" <?php echo ($res->PREFEREDSEX=='Female') ? 'selected' : ''; ?>>Female</option>
                           <option value="Male/Female" <?php echo ($res->PREFEREDSEX=='Male/Female') ? 'selected' : ''; ?>>Male/Female</option>
                        </select>
                      </div>
                    </div>
                  </div>  

                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "SECTOR_VACANCY">Sector of Vacancy:</label> 
                      <div class="col-md-8">
                        <textarea class="form-control input-sm" id="SECTOR_VACANCY" name="SECTOR_VACANCY" placeholder="Sector of Vacancy"   autocomplete="none" required rows="2"><?php echo htmlspecialchars($res->SECTOR_VACANCY); ?></textarea> 
                      </div>
                    </div>
                  </div>  
                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "JOBSTATUS">Job Status:</label> 
                      <div class="col-md-8">
                          <select class="form-control input-sm" id="JOBSTATUS" name="JOBSTATUS" required>
                          <option value="">Select</option>
                           <option value="Open" <?php echo ($res->JOBSTATUS=='Open') ? 'selected' : ''; ?>>Open</option>
                           <option value="Closed" <?php echo ($res->JOBSTATUS=='Closed') ? 'selected' : ''; ?>>Closed</option>
                           <option value="On Hold" <?php echo ($res->JOBSTATUS=='On Hold') ? 'selected' : ''; ?>>On Hold</option>
                           <option value="Filled" <?php echo ($res->JOBSTATUS=='Filled') ? 'selected' : ''; ?>>Filled</option>
                        </select>
                      </div>
                    </div>
                  </div>  
 
                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "idno"></label>  

                      <div class="col-md-8">
                         <button class="btn btn-primary btn-sm" name="save" type="submit" ><span class="fa fa-save fw-fa"></span> Save</button>
                         <a href="index.php" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
                     </div>
                    </div>
                  </div> 



</form>

<script>
// Simple client-side validation for instant feedback
if (window.jQuery) {
    $(function() {
        $('#editJobForm').on('submit', function(e) {
            var valid = true;
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
}
</script>
<style>
.is-invalid { border: 1px solid #d9534f; background: #f2dede; }
.form-group { margin-bottom: 15px; }
.btn { margin-right: 5px; }
</style>
       