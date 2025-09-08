<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/index.php");
}
  
// $autonum = New Autonumber();
// $res = $autonum->single_autonumber(2);
 $empid = isset($_GET['id']) ? $_GET['id'] : '';
    if($empid==''){
  redirect("index.php");
}
 

  $employee = New Employee();
  $emp = $employee->single_employee($empid);
 

  if ($emp->SEX == 'Male') {
    # code...
   $radio =  '<div class="col-md-8">
             <div class="col-lg-5">
                <div class="radio">
                  <label><input   id="optionsRadios1" name="optionsRadios" type="radio" value="Female">Female</label>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="radio">
                  <label><input id="optionsRadios2"  checked="True" name="optionsRadios" type="radio" value="Male">Male</label>
                </div>
              </div> 
             
          </div>';
  }else{
       $radio =  '<div class="col-md-8">
             <div class="col-lg-5">
                <div class="radio">
                  <label><input   id="optionsRadios1"  checked="True" name="optionsRadios" type="radio" value="Female">Female</label>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="radio">
                  <label><input id="optionsRadios2"  name="optionsRadios" type="radio" value="Male"> Male</label>
                </div>
              </div> 
             
          </div>';

  }

   switch ($emp->CIVILSTATUS) {

     case 'Single':
       # code...
        $civilstatus =' <select class="form-control input-sm" name="CIVILSTATUS" id="CIVILSTATUS">
                                      <option value="none" >Select</option>
                                      <option SELECTED value="Single">Single</option>
                                      <option value="Married">Married</option>
                                      <option value="Widow" >Widow</option>
                                      <!-- <option value="Fourth" >Fourth</option> -->
                                  </select> ';
       break;
     case 'Married':
       # code...
         $civilstatus=' <select class="form-control input-sm" name="CIVILSTATUS" id="CIVILSTATUS">
                                      <option value="none" >Select</option>
                                      <option  value="Single">Single</option>
                                      <option SELECTED value="Married">Married</option>
                                      <option value="Widow" >Widow</option>
                                      <!-- <option value="Fourth" >Fourth</option> -->
                                  </select> ';

       break;
     case 'Widow':
       # code...
       $civilstatus=' <select class="form-control input-sm" name="CIVILSTATUS" id="CIVILSTATUS">
                                      <option value="none" >Select</option>
                                      <option  value="Single">Single</option>
                                      <option  value="Married">Married</option>
                                      <option SELECTED value="Widow" >Widow</option>
                                      <!-- <option value="Fourth" >Fourth</option> -->
                                  </select> ';
       break;
     default:
         $civilstatus=' <select class="form-control input-sm" name="CIVILSTATUS" id="CIVILSTATUS">
                                      <option SELECTED value="none" >Select</option>
                                      <option  value="Single">Single</option>
                                      <option  value="Married">Married</option>
                                      <option  value="Widow" >Widow</option> 
                                  </select> ';
         break;     
       
   }

   switch ($emp->WORKSTATS) {

     case 'Regular':
       # code...
        $workstatus ='
        <select class="form-control input-sm" name="WORKSTATS" id="WORKSTATS">
                                      <option value="none" >Select</option>
                                      <option value="Temporary">Temporary</option>
                                      <option SELECTED  value="Regular">Regular</option>
                                      <option value="Probationary">Probationary</option> 
                                  </select> ';
       break;

     case 'Regular':
       # code...
        $workstatus ='
        <select class="form-control input-sm" name="WORKSTATS" id="WORKSTATS">
                                      <option value="none" >Select</option>
                                      <option value="Temporary">Temporary</option>
                                      <option SELECTED value="Regular">Regular</option>
                                      <option value="Probationary">Probationary</option> 
                                  </select> ';
       break;
     case 'Probationary':
       # code...
         $workstatus='
         <select class="form-control input-sm" name="WORKSTATS" id="WORKSTATS">
                                      <option value="none" >Select</option>
                                      <option value="Temporary">Temporary</option>
                                      <option value="Regular">Regular</option>
                                      <option SELECTED value="Probationary">Probationary</option> 
                                  </select> ';

       break; 
	   
     default:
        $workstatus='
       <select class="form-control input-sm" name="WORKSTATS" id="WORKSTATS">
                                      <option SELECTED value="none" >Select</option>
                                      <option value="Temporary">Temporary</option>
                                      <option value="Regular">Regular</option>
                                      <option value="Probationary">Probationary</option> 
                                  </select> ';
        break;
       
   }

  
 ?> 
 
       <div class="center wow fadeInDown">
             <h2 class="page-header">Update Employee</h2>
            <!-- <p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut <br> et dolore magna aliqua. Ut enim ad minim veniam</p> -->
        </div>
 

                 <form class="form-horizontal span6" action="controller.php?action=edit" method="POST" id="editEmployeeForm" novalidate> 
              

                <input  id="EMPLOYEEID" name="EMPLOYEEID" type="hidden" value="<?php echo htmlspecialchars($emp->EMPLOYEEID);?>"  >
                   
                
                 <div class="form-group">
                      <div class="col-md-8">
                        <label class="col-md-4 control-label" for=
                        "FNAME">Firstname:</label>

                        <div class="col-md-8"> 
                           <input class="form-control input-sm" id="FNAME" name="FNAME" placeholder=
                              "Firstname" type="text" value="<?php echo htmlspecialchars($emp->FNAME);?>"   autocomplete="off" required minlength="2">
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="col-md-8">
                        <label class="col-md-4 control-label" for=
                        "LNAME">Lastname:</label>

                        <div class="col-md-8"> 
                          <input  class="form-control input-sm" id="LNAME" name="LNAME" placeholder=
                              "Lastname"   value="<?php echo htmlspecialchars($emp->LNAME);?>"    autocomplete="off" required minlength="2">
                          </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="col-md-8">
                        <label class="col-md-4 control-label" for=
                        "MNAME">Middle Name:</label>

                        <div class="col-md-8"> 
                          <input  class="form-control input-sm" id="MNAME" name="MNAME" placeholder=
                              "Middle Name"   value="<?php echo htmlspecialchars($emp->MNAME);?>"     autocomplete="off">
                           <!-- <input class="form-control input-sm" id="DEPARTMENT_DESC" name="DEPARTMENT_DESC" placeholder=
                              "Description" type="text" value=""> -->
                        </div>
                      </div>
                    </div> 

                   <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "ADDRESS">Address:</label>

                      <div class="col-md-8">
                        
                         <textarea class="form-control input-sm" id="ADDRESS" name="ADDRESS" placeholder=
                            "Address" type="text" value="" required   autocomplete="off"><?php echo htmlspecialchars($emp->ADDRESS);?></textarea>
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
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "BIRTHDATE">Date of Birth:</label>

                      <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon"> 
                             <i class="fa fa-calendar"></i> 
                            </span>  
                             <input class="form-control input-sm date_picker" id="BIRTHDATE" name="BIRTHDATE" placeholder="Date of Birth" type="text"    value="<?php echo htmlspecialchars(date_format(date_create($emp->BIRTHDATE),'m/d/Y'));?>" required  autocomplete="off">
                        </div>
                      </div>
                    </div>
                  </div> 

                    <div class="form-group">
                                <div class="col-md-8">
                                  <label class="col-md-4 control-label" for=
                                  "BIRTHPLACE">Place of Birth:</label>

                                  <div class="col-md-8">
                                    
                                     <textarea class="form-control input-sm" id="BIRTHPLACE" name="BIRTHPLACE" placeholder=
                                        "Place of Birth" type="text" value="" required   
                                        autocomplete="off"><?php echo htmlspecialchars($emp->BIRTHPLACE);?></textarea>
                                  </div>
                                </div>
                              </div> 


                             <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "TELNO">Conact No.:</label>

                                <div class="col-md-8">
                                  
                                   <input class="form-control input-sm" id="TELNO" name="TELNO" placeholder=
                                      "Conact No." type="text" any value="<?php echo htmlspecialchars($emp->TELNO);?>" required   autocomplete="off" pattern="[0-9\-\+\s]+">
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
                                      "Postion" type="text" any value="<?php echo htmlspecialchars($emp->POSITION);?>" required   autocomplete="off">
                                </div>
                              </div>
                            </div>

                             
                            <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "WORKSTATS">Work Status:</label>

                                <div class="col-md-8">
                                  <?php echo $workstatus; ?>
                                </div>
                              </div>
                            </div>
                             
                            <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "EMP_HIREDDATE">Hired Date:</label> 
                                <div class="col-md-8">
                                    <div class="input-group date  " data-provide="datepicker" data-date="2012-12-21T15:25:00Z">
                                   <input type="input" class="form-control input-sm date_picker" id="HIREDDATE" name="EMP_HIREDDATE" placeholder="mm/dd/yyyy"   autocomplete="false" value="<?php echo htmlspecialchars(date_format(date_create($emp->DATEHIRED),'m/d/Y')); ?>"/> 
                                     <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                 </div>
                                </div>
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <div class="col-md-8">
                                <label class="col-md-4 control-label" for=
                                "EMP_EMAILADDRESS">Email Address:</label> 
                                <div class="col-md-8">
                                   <input type="Email" class="form-control input-sm" id="EMP_EMAILADDRESS" name="EMP_EMAILADDRESS" placeholder="Email Address"   autocomplete="false" value="<?php echo htmlspecialchars($emp->EMP_EMAILADDRESS); ?>"/> 
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


             
<script>
// Simple client-side validation for instant feedback
if (window.jQuery) {
    $(function() {
        $('#editEmployeeForm').on('submit', function(e) {
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
             