<?php  
      if (!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     }

  $USERID = isset($_GET['id']) ? $_GET['id'] : '';
    if($USERID==''){
  redirect("index.php");
}
  $user = New User();
  $singleuser = $user->single_user($USERID);

?> 

 <form class="form-horizontal span6" action="controller.php?action=edit" method="POST" id="editUserForm" novalidate>

          <fieldset>
            <legend> Update User Account</legend>
                   
                    <!-- <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "user_id">User Id:</label> -->

                      <!-- <div class="col-md-8"> -->
                        
                         <input id="USERID" name="USERID" type="Hidden" value="<?php echo htmlspecialchars($singleuser->USERID); ?>">
                   <!--    </div>
                    </div>
                  </div>      -->      
                  
                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "U_NAME">Name:</label>

                      <div class="col-md-8">
                        <input name="deptid" type="hidden" value="">
                         <input class="form-control input-sm" id="U_NAME" name="U_NAME" placeholder=
                            "Account Name" type="text" value="<?php echo htmlspecialchars($singleuser->FULLNAME); ?>" required minlength="2">
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "U_USERNAME">Username:</label>

                      <div class="col-md-8">
                        <input name="deptid" type="hidden" value="">
                         <input class="form-control input-sm" id="U_USERNAME" name="U_USERNAME" placeholder=
                            "Email Address" type="text" value="<?php echo htmlspecialchars($singleuser->USERNAME); ?>" required minlength="3">
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "U_PASS">Password:</label>

                      <div class="col-md-8">
                        <input name="deptid" type="hidden" value="">
                         <input class="form-control input-sm" id="U_PASS" name="U_PASS" placeholder=
                            "Account Password" type="password" value="" required minlength="3">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-8">
                      <label class="col-md-4 control-label" for=
                      "U_ROLE">Role:</label>

                      <div class="col-md-8">
                       <select class="form-control input-sm" name="U_ROLE" id="U_ROLE" required>
                          <option value="">Select Role</option>
                          <option value="Administrator" <?php echo ($singleuser->ROLE=='Administrator') ? 'selected' : '' ; ?>>Administrator</option>
                          <option value="Staff" <?php echo ($singleuser->ROLE=='Staff') ? 'selected' : '' ; ?>>Staff</option>  
                        </select> 
                      </div>
                    </div>
                  </div>

            
             <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                         <button class="btn btn-primary" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                         <a href="index.php" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
                      </div>
                    </div>
                  </div>

              
          </fieldset> 
 
          
        </form>
      

        </div><!--End of container-->

<script>
// Simple client-side validation for instant feedback
if (window.jQuery) {
    $(function() {
        $('#editUserForm').on('submit', function(e) {
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