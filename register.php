<section id="content">
    <div class="container content">    
     <p> <?php check_message();?></p>
     <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default registration-panel">
                <div class="panel-heading text-center">
                    <h3 class="panel-title"><i class="fa fa-user-plus"></i> Create Your Account</h3>
                    <p class="text-muted">Join our community by completing this registration form</p>
                </div>
                <div class="panel-body">
                    <!-- Form Steps Indicator -->
                    <div class="registration-steps">
                        <ul class="nav nav-pills nav-justified step-tabs">
                            <li class="active"><a href="#step1" data-toggle="tab"><i class="fa fa-user"></i> Personal</a></li>
                            <li><a href="#step2" data-toggle="tab"><i class="fa fa-map-marker"></i> Contact</a></li>
                            <li><a href="#step3" data-toggle="tab"><i class="fa fa-lock"></i> Account</a></li>
                        </ul>
                    </div>
                    
                    <form id="registrationForm" class="form-horizontal wow fadeInDown" action="process.php?action=register" method="POST">
                        <div class="tab-content">
                            <!-- Step 1: Personal Information -->
                            <div class="tab-pane fade in active" id="step1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-primary section-title"><i class="fa fa-user"></i> Personal Information</h4>
                                        <hr>
                                    </div>
                                </div>
                                
                                <div class="row"> 
                                    <div class="form-group">
                                        <div class="col-md-8">
                                            <label class="col-md-4 control-label" for="FNAME">Firstname <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                              <input name="JOBID" type="hidden" value="<?php echo isset($_GET['job']) ? $_GET['job'] : ''; ?>">
                                              <div class="input-group">
                                                  <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                  <input class="form-control input-sm" id="FNAME" name="FNAME" placeholder="Enter your firstname" type="text" value="" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="form-group">
                                        <div class="col-md-8">
                                            <label class="col-md-4 control-label" for="LNAME">Lastname <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                              <div class="input-group">
                                                  <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                  <input class="form-control input-sm" id="LNAME" name="LNAME" placeholder="Enter your lastname" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="form-group">
                                        <div class="col-md-8">
                                            <label class="col-md-4 control-label" for="MNAME">Middle Name</label>
                                            <div class="col-md-8">
                                              <div class="input-group">
                                                  <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                  <input class="form-control input-sm" id="MNAME" name="MNAME" placeholder="Enter your middle name" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off">
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-md-8">
                                            <label class="col-md-4 control-label" for="Gender">Sex <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                             <div class="col-lg-5">
                                                <div class="radio">
                                                  <label><input checked id="optionsRadios1" name="optionsRadios" type="radio" value="Female"><i class="fa fa-female"></i> Female</label>
                                                </div>
                                              </div>
                                              <div class="col-lg-4">
                                                <div class="radio">
                                                  <label><input id="optionsRadios2" name="optionsRadios" type="radio" value="Male"><i class="fa fa-male"></i> Male</label>
                                                </div>
                                              </div> 
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="form-group">
                                      <div class="rows">
                                        <div class="col-md-8"> 
                                          <label class="col-md-4 control-label">Date of Birth <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <div class="row">
                                                  <div class="col-xs-4">
                                                    <select class="form-control input-sm" name="month" required>
                                                      <option value="">Month</option>
                                                      <?php
                                                         $mon = array('Jan' => 1 ,'Feb'=> 2,'Mar' => 3 ,'Apr'=> 4,'May' => 5 ,'Jun'=> 6,'Jul' => 7 ,'Aug'=> 8,'Sep' => 9 ,'Oct'=> 10,'Nov' => 11 ,'Dec'=> 12 );    
                                                        foreach ($mon as $month => $value ) {
                                                               echo '<option value='.$value.'>'.$month.'</option>';
                                                            } 
                                                      ?>
                                                    </select>
                                                  </div>
                                                  <div class="col-xs-3">
                                                    <select class="form-control input-sm" name="day" required>
                                                      <option value="">Day</option>
                                                    <?php 
                                                      $d = range(1, 31);
                                                      foreach ($d as $day) {
                                                        echo '<option value='.$day.'>'.$day.'</option>';
                                                      }
                                                    ?>
                                                    </select>
                                                  </div>
                                                  <div class="col-xs-5">
                                                    <select class="form-control input-sm" name="year" required>
                                                      <option value="">Year</option>
                                                    <?php 
                                                      $years = range(date('Y')-15, date('Y')-70);
                                                      foreach ($years as $yr) {
                                                        echo '<option value='.$yr.'>'.$yr.'</option>';
                                                      }
                                                    ?>
                                                    </select>
                                                  </div>
                                              </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                      <div class="col-md-8">
                                        <label class="col-md-4 control-label" for="CIVILSTATUS">Civil Status <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                          <select class="form-control input-sm" name="CIVILSTATUS" id="CIVILSTATUS" required>
                                              <option value="" >Select Status</option>
                                              <option value="Single">Single</option>
                                              <option value="Married">Married</option>
                                              <option value="Widow">Widow</option>
                                              <option value="Separated">Separated</option>
                                              <option value="Divorced">Divorced</option>
                                          </select> 
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-md-8">
                                          <div class="col-md-offset-4 col-md-8">
                                              <button type="button" class="btn btn-primary next-step" data-next="step2">Continue to Contact Information <i class="fa fa-arrow-right"></i></button>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 2: Contact Information -->
                            <div class="tab-pane fade" id="step2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-primary section-title"><i class="fa fa-map-marker"></i> Contact Information</h4>
                                        <hr>
                                    </div>
                                </div>
                                
                                <div class="row"> 
                                    <div class="form-group">
                                        <div class="col-md-8">
                                            <label class="col-md-4 control-label" for="ADDRESS">Address <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                             <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                                                <textarea class="form-control input-sm" id="ADDRESS" name="ADDRESS" placeholder="Enter your complete address" rows="3" required onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off"></textarea>
                                             </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-md-8">
                                          <label class="col-md-4 control-label" for="BIRTHPLACE">Place of Birth <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                             <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                                <textarea class="form-control input-sm" id="BIRTHPLACE" name="BIRTHPLACE" placeholder="Enter place of birth" rows="2" required onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off"></textarea>
                                             </div>
                                          </div>
                                        </div>
                                    </div>
                
                                    <div class="form-group">
                                      <div class="col-md-8">
                                        <label class="col-md-4 control-label" for="TELNO">Contact No. <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                           <div class="input-group">
                                               <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                               <input class="form-control input-sm" id="TELNO" name="TELNO" placeholder="Enter your contact number" type="text" value="" required>
                                           </div>
                                           <small class="text-muted">Format: 123-456-7890</small>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                      <div class="col-md-8">
                                        <label class="col-md-4 control-label" for="EMAILADDRESS">Email Address <span class="text-danger">*</span></label> 
                                        <div class="col-md-8">
                                           <div class="input-group">
                                               <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                               <input type="email" class="form-control input-sm" id="EMAILADDRESS" name="EMAILADDRESS" placeholder="Enter your email address" required autocomplete="off"/> 
                                           </div>
                                           <small class="text-muted">This will be used for communication and password recovery</small>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 mb-2">
                                                    <button type="button" class="btn btn-info btn-block prev-step" data-prev="step1">
                                                        <i class="fa fa-arrow-left"></i> Back to Personal Information
                                                    </button>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 mb-2">
                                                    <button type="button" class="btn btn-primary btn-block next-step" data-next="step3">
                                                        Continue to Account Setup <i class="fa fa-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 3: Account Information -->
                            <div class="tab-pane fade" id="step3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-primary section-title"><i class="fa fa-lock"></i> Account Information</h4>
                                        <hr>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                      <div class="col-md-8">
                                        <label class="col-md-4 control-label" for="USERNAME">Username <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                              <input class="form-control input-sm" id="USERNAME" name="USERNAME" placeholder="Choose a username" autocomplete="off" required>
                                          </div>
                                          <small class="text-muted">Username must be at least 5 characters</small>
                                        </div>
                                      </div>
                                    </div>
                
                                    <div class="form-group">
                                      <div class="col-md-8">
                                        <label class="col-md-4 control-label" for="PASS">Password <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                              <input class="form-control input-sm" id="PASS" name="PASS" placeholder="Create a password" type="password" required autocomplete="off">
                                          </div>
                                          <small class="text-muted">Password must be at least 8 characters with letters and numbers</small>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                      <div class="col-md-8">
                                        <label class="col-md-4 control-label" for="CONFIRMPASS">Confirm Password <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                              <input class="form-control input-sm" id="CONFIRMPASS" name="CONFIRMPASS" placeholder="Confirm your password" type="password" required autocomplete="off">
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                      <div class="col-md-8">
                                        <label class="col-md-4 control-label" for="DEGREE">Educational Attainment <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                          <select class="form-control input-sm" id="DEGREE" name="DEGREE" required>
                                            <option value="">Select Highest Education</option>
                                            <option value="Elementary">Elementary</option>
                                            <option value="High School">High School</option>
                                            <option value="Vocational">Vocational/Technical</option>
                                            <option value="Associate's Degree">Associate's Degree</option>
                                            <option value="Bachelor's Degree">Bachelor's Degree</option>
                                            <option value="Master's Degree">Master's Degree</option>
                                            <option value="Doctorate Degree">Doctorate Degree</option>
                                            <option value="Professional Degree">Professional Degree</option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-md-8">
                                          <label class="col-md-4 control-label" for=""></label>  
                                          <div class="col-md-8"> 
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" id="termsCheckbox" required> 
                                                        I have read and agree to the <a href="#" data-toggle="modal" data-target="#termsModal">terms and conditions</a>
                                                    </label>
                                                </div>
                                         </div>
                                        </div>
                                    </div>    
                                    
                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 mb-2">
                                                    <button type="button" class="btn btn-info btn-block prev-step" data-prev="step2">
                                                        <i class="fa fa-arrow-left"></i> Back to Contact Information
                                                    </button>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 mb-2">
                                                    <button class="btn btn-success btn-block" name="btnRegister" type="submit" id="submitBtn">
                                                        <span class="fa fa-check-circle"></span> Complete Registration
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
     </div>
    </div>
    
    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="termsModalLabel">Terms and Conditions</h4>
          </div>
          <div class="modal-body">
            <h5>User Agreement</h5>
            <p>Please read these terms and conditions carefully before registering. By registering for an account, you agree to be bound by the following terms:</p>
            <ol>
              <li><strong>Account Information:</strong> All information provided during registration must be accurate, current, and complete.</li>
              <li><strong>Privacy Policy:</strong> Your personal information will be collected, stored, and used in accordance with our privacy policy.</li>
              <li><strong>Account Security:</strong> You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</li>
              <li><strong>Account Termination:</strong> We reserve the right to suspend or terminate accounts that violate our terms or engage in inappropriate behavior.</li>
              <li><strong>Communication:</strong> By creating an account, you consent to receive communications from us related to your account and our services.</li>
              <li><strong>User Conduct:</strong> You agree not to use the service for any illegal or unauthorized purpose.</li>
              <li><strong>Modifications:</strong> We reserve the right to modify these terms at any time. Continued use after changes constitutes acceptance of the modified terms.</li>
            </ol>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="agreeBtn">I Agree</button>
          </div>
        </div>
      </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Custom JavaScript for form validation and multi-step functionality -->
    <script>
    $(document).ready(function() {
        // Show first tab by default
        $('.tab-pane').removeClass('active in');
        $('#step1').addClass('active in');
        $('.step-tabs li').removeClass('active');
        $('.step-tabs li:first-child').addClass('active');

        // Handle next step button
        $(document).on('click', '.next-step', function(e) {
            e.preventDefault();
            var currentTab = $(this).closest('.tab-pane');
            var nextTabId = $(this).data('next');
            var nextTab = $('#' + nextTabId);
            
            // Validate current step
            var isValid = true;
            currentTab.find('input[required], select[required], textarea[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid').closest('.form-group').addClass('has-error');
                } else {
                    $(this).removeClass('is-invalid').closest('.form-group').removeClass('has-error');
                }
            });

            if (isValid) {
                // Hide current tab
                currentTab.removeClass('active in');
                // Show next tab
                nextTab.addClass('active in');
                // Update step indicator
                $('.step-tabs li').removeClass('active');
                $('.step-tabs a[href="#' + nextTabId + '"]').parent().addClass('active');
                
                // Scroll to top
                $('html, body').animate({
                    scrollTop: $('.registration-panel').offset().top - 20
                }, 500);
            } else {
                alert('Please fill in all required fields before proceeding.');
            }
        });

        // Handle previous step button
        $(document).on('click', '.prev-step', function(e) {
            e.preventDefault();
            var currentTab = $(this).closest('.tab-pane');
            var prevTabId = $(this).data('prev');
            var prevTab = $('#' + prevTabId);
            
            // Hide current tab
            currentTab.removeClass('active in');
            // Show previous tab
            prevTab.addClass('active in');
            // Update step indicator
            $('.step-tabs li').removeClass('active');
            $('.step-tabs a[href="#' + prevTabId + '"]').parent().addClass('active');
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $('.registration-panel').offset().top - 20
            }, 500);
        });

        // "I Agree" button in terms modal
        $('#agreeBtn').click(function() {
            $('#termsCheckbox').prop('checked', true);
        });

        // Form submission validation
        $('#registrationForm').submit(function(e) {
            var pass = $('#PASS').val();
            var confirmPass = $('#CONFIRMPASS').val();
            
            if (pass !== confirmPass) {
                e.preventDefault();
                alert('Passwords do not match!');
                $('#PASS, #CONFIRMPASS').addClass('is-invalid').closest('.form-group').addClass('has-error');
                return false;
            }
            
            if (pass.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters!');
                $('#PASS').addClass('is-invalid').closest('.form-group').addClass('has-error');
                return false;
            }
            
            if (!$('#termsCheckbox').is(':checked')) {
                e.preventDefault();
                alert('You must agree to the terms and conditions!');
                return false;
            }
            
            return true;
        });
    });
    </script>

    <style>
    .registration-panel {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        border: none;
    }
    
    .registration-panel .panel-heading {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 20px;
    }
    
    .registration-steps {
        margin: 20px 0;
    }
    
    .step-tabs {
        margin-bottom: 30px;
    }
    
    .step-tabs > li > a {
        border-radius: 30px;
        padding: 10px 15px;
        margin-right: 5px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .step-tabs > li.active > a {
        background-color: #337ab7;
        color: white;
    }
    
    .section-title {
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .has-error .form-control {
        border-color: #a94442;
    }
    
    .has-error .control-label {
        color: #a94442;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .form-horizontal .control-label {
            text-align: left;
            margin-bottom: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .step-tabs > li > a {
            font-size: 12px;
            padding: 8px 10px;
        }
    }
    .mb-2 {
        margin-bottom: 12px;
    }
    @media (min-width: 768px) {
        .mb-2:not(:last-child) {
            margin-right: 10px;
        }
    }
    </style>
</section>