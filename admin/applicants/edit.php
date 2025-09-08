<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/index.php");
}

$red_id = isset($_GET['id']) ? $_GET['id'] : '';
if($red_id==''){
    redirect("index.php");
}

// Get job registration details
$jobregistration = New JobRegistration();
$jobreg = $jobregistration->single_jobregistration($red_id);

if (!$jobreg) {
    echo '<div class="alert alert-danger">Job registration not found.</div>';
    echo '<a href="index.php" class="btn btn-default">Go Back</a>';
    exit;
}

// Get applicant details
$applicant = new Applicants();
$appl = $applicant->single_applicant($jobreg->APPLICANTID);

if (!$appl) {
    echo '<div class="alert alert-danger">Applicant information not found.</div>';
    echo '<a href="index.php" class="btn btn-default">Go Back</a>';
    exit;
}

// Get job details
$jobvacancy = New Jobs();
$job = $jobvacancy->single_job($jobreg->JOBID);

// Get company details  
$company = new Company();
$comp = $company->single_company($jobreg->COMPANYID);

// Create status options
$status_options = array(
    'Pending' => 'Pending',
    'Under Review' => 'Under Review', 
    'Shortlisted' => 'Shortlisted',
    'Interview Scheduled' => 'Interview Scheduled',
    'Interviewed' => 'Interviewed',
    'Hired' => 'Hired',
    'Rejected' => 'Rejected'
);

?>

<style type="text/css">
.content-header {
    min-height: 50px;
    border-bottom: 1px solid #ddd;
    font-size: 15px;
    font-weight: bold;
    padding: 15px;
}
.content-body {
    min-height: 350px;
    padding: 20px;
}
.form-group {
    margin-bottom: 15px;
}
.back-btn { 
    margin-top: 10px; 
    margin-bottom: 10px; 
}
</style>

<div class="col-sm-12 content-header">
    Edit Application Status
    <a href="index.php" class="btn btn-default btn-sm pull-right back-btn">
        <span class="glyphicon glyphicon-arrow-left"></span> Back
    </a>
</div>

<div class="col-sm-12 content-body">
    <form action="controller.php?action=edit" method="POST">
        <input type="hidden" name="REGISTRATIONID" value="<?php echo htmlspecialchars($jobreg->REGISTRATIONID); ?>">
        
        <!-- Applicant Information -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Applicant Information</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($appl->FNAME . ' ' . $appl->LNAME); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($appl->EMAILADDRESS); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($appl->CONTACTNO); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($appl->ADDRESS); ?></p>
                        <p><strong>Age:</strong> <?php echo htmlspecialchars($appl->AGE); ?></p>
                        <p><strong>Degree:</strong> <?php echo htmlspecialchars($appl->DEGREE); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Job Information -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Job Information</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Job Title:</strong> <?php echo htmlspecialchars($job->OCCUPATIONTITLE); ?></p>
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($comp->COMPANYNAME); ?></p>
                        <p><strong>Salary:</strong> <?php echo number_format($job->SALARIES, 2); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Applied Date:</strong> <?php echo date('M d, Y', strtotime($jobreg->REGISTRATIONDATE)); ?></p>
                        <p><strong>Required Employees:</strong> <?php echo htmlspecialchars($job->REQ_NO_EMPLOYEES); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($job->DURATION_EMPLOYEMENT); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Application Status -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Application Status</h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="STATUS">Status:</label>
                    <div class="col-md-9">
                        <select class="form-control" name="STATUS" id="STATUS" required>
                            <?php foreach ($status_options as $value => $label): ?>
                                <option value="<?php echo htmlspecialchars($value); ?>"
                                    <?php echo ($jobreg->REMARKS == $value) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label" for="REMARKS">Feedback/Remarks:</label>
                    <div class="col-md-9">
                        <textarea class="form-control" name="REMARKS" id="REMARKS" rows="4" 
                                  placeholder="Enter feedback or remarks for the applicant..."><?php echo isset($jobreg->REMARKS) ? htmlspecialchars($jobreg->REMARKS) : ''; ?></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" name="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Status
                        </button>
                        <a href="index.php" class="btn btn-default">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div> 

                   <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "ADDRESS">Address:</label>

                      <div class="col-md-8">
                        
                         <textarea class="form-control input-sm" id="ADDRESS" name="ADDRESS" placeholder=
                            "Address" type="text" value="" required  onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off"><?php echo $emp->ADDRESS;?></textarea>
                      </div>
                    </div>
                  </div> 


                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "Gender">Sex:</label>

                      <?php
                        echo $radio;
                      ?>
 
                    </div>
                  </div>  

                  <div class="form-group">
                      <div class="rows">
                        <div class="col-md-8">
                          <h4>
                          <div class="col-md-4">
                            <label class="col-lg-12 control-label">Date of Birth</label>
                          </div>

                          <div class="col-lg-3">
                            <select class="form-control input-sm" name="month">
                              <option>Month</option>
                              <?php


                                 echo '<option SELECTED value='.$mv.'>'.$m.'</option>';

                                 $mon = array('Jan' => 1 ,'Feb'=> 2,'Mar' => 3 ,'Apr'=> 4,'May' => 5 ,'Jun'=> 6,'Jul' => 7 ,'Aug'=> 8,'Sep' => 9 ,'Oct'=> 10,'Nov' => 11 ,'Dec'=> 12 );    
                                
                            
                                foreach ($mon as $month => $value ) { 
                                # code...
                               
                                echo '<option value='.$value.'>'.$month.'</option>';
                                }
                              
                                   
                              ?>
                            </select>
                          </div>
 
                          <div class="col-lg-2">
                            <select class="form-control input-sm" name="day">
                              <option>Day</option>
                            <?php 
                             echo '<option SELECTED value='.$d.'>'.$d.'</option>';
                              $d = range(1, 31);
                              foreach ($d as $day) {
                                echo '<option value='.$day.'>'.$day.'</option>';
                              }
                            
                            ?>
                              
                            </select>
                          </div>

                          <div class="col-lg-3">
                            <select class="form-control input-sm" name="year">
                              <option>Year</option>
                            <?php 
                                echo '<option SELECTED value='.$y.'>'.$y.'</option>';
                                $years = range(2010, 1900);
                                foreach ($years as $yr) {
                                echo '<option value='.$yr.'>'.$yr.'</option>';
                                }
                            
                            ?>
                            
                            </select>
                          </div>
                          </h4>
                        </div>
                      </div>
                    </div> 

                    <div class="form-group">
                                <div class="col-md-8">
                                  <label class="col-md-4 control-label" for=
                                  "BIRTHPLACE">Place of Birth:</label>

                                  <div class="col-md-8">
                                    
                                     <textarea class="form-control input-sm" id="BIRTHPLACE" name="BIRTHPLACE" placeholder=
                                        "Place of Birth" type="text" value="" required  onkeyup="javascript:capitalize(this.id, this.value);" 
                                        autocomplete="off"><?php echo $emp->BIRTHPLACE;?></textarea>
                                  </div>
                                </div>
                              </div> 


                             <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "TELNO">Conact No.:</label>

                                <div class="col-md-8">
                                  
                                   <input class="form-control input-sm" id="TELNO" name="TELNO" placeholder=
                                      "Conact No." type="text" any value="<?php echo $emp->TELNO;?>" required  onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off">
                                </div>
                              </div>
                            </div> 

                             <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "CIVILSTATUS">Civil Status:</label>

                                <div class="col-md-8">
                                  <?php echo $civilstatus; ?>
                                </div>
                              </div>
                            </div> 

                            <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "POSITION">Postion:</label>

                                <div class="col-md-8">
                                  
                                   <input class="form-control input-sm" id="POSITION" name="POSITION" placeholder=
                                      "Postion" type="text" any value="<?php echo $emp->POSITION;?>" required  onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off">
                                </div>
                              </div>
                            </div>


                             

                            <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "DATEHIRED">Hired Date:</label>

                                <div class="col-md-8">
                                  <div class="input-group " > 
                                      <!-- <label><?php echo date_format(date_create($emp->BIRTHDATE),'m/d/Y'); ?> </label>  
                                      <i class="fa fa-calendar"></i>  -->
                                        <div class="input-group-addon"> 
                                              <i class="fa fa-calendar"></i>
                                            </div>
                                            <input id="datemask2" name="DATEHIRED"  value="<?php echo date_format(date_create($emp->DATEHIRED),'m/d/Y'); ?>" type="text" class="form-control input-sm datemask2"   data-inputmask="'alias': 'mm/dd/yyyy'" data-mask required>
                                       
                                   </div>       
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "EMP_EMAILADDRESS">Email Address:</label> 
                                <div class="col-md-8">
                                   <input type="Email" class="form-control input-sm" id="EMP_EMAILADDRESS" name="EMP_EMAILADDRESS" placeholder="Email Address"   autocomplete="false" value="<?php echo  $emp->EMP_EMAILADDRESS; ?>"/> 
                                </div>
                              </div>
                            </div>  


                         <div class="form-group">
                            <div class="col-md-8">
                              <label class="col-md-4 control-label" for=
                              "COMPANYNAME">Company Name:</label>

                              <div class="col-md-8"> 
                                <select class="form-control input-sm" id="COMPANYID" name="COMPANYID">
                                  <option value="None">Select</option>
                                  <?php 
                                    $sql ="Select * From tblcompany WHERE COMPANYID=".$emp->COMPANYID;
                                    $mydb->setQuery($sql);
                                    $result  = $mydb->loadResultList();
                                    foreach ($result as $row) {
                                      # code...
                                      echo '<option SELECTED value='.$row->COMPANYID.'>'.$row->COMPANYNAME.'</option>';
                                    }
                                    $sql ="Select * From tblcompany WHERE COMPANYID!=".$emp->COMPANYID;
                                    $mydb->setQuery($sql);
                                    $result  = $mydb->loadResultList();
                                    foreach ($result as $row) {
                                      # code...
                                      echo '<option value='.$row->COMPANYID.'>'.$row->COMPANYNAME.'</option>';
                                    }

                                  ?>
                                </select>
                              </div>
                            </div>
                          </div>  
 
               
                  
             <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "idno"></label>

                      <div class="col-md-8">
                       <button class="btn btn-primary btn-sm" name="save" type="submit" ><span class="fa fa-save fw-fa"></span>  Save</button> 
                          <!-- <a href="index.php" class="btn btn-info"><span class="fa fa-arrow-circle-left fw-fa"></span></span>&nbsp;<strong>List of Users</strong></a> -->
                       </div>
                    </div>
                  </div> 
        </form>


             