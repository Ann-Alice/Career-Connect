<?php 
if (!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/index.php");
}
$autonum = New Autonumber();
$res = $autonum->set_autonumber('userid');
?>
<div class="col-lg-12">
    <h1 class="page-header">Add New User</h1>
</div>
<form class="form-horizontal span6" action="controller.php?action=add" method="POST" id="addUserForm" novalidate>
    <input id="user_id" name="user_id" type="hidden" value="<?php echo htmlspecialchars($res->AUTO); ?>">
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="U_NAME">Name:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="U_NAME" name="U_NAME" placeholder="User Fullname" type="text" value="" required minlength="2">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="U_USERNAME">Username:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="U_USERNAME" name="U_USERNAME" placeholder="Account Username" type="text" value="" required minlength="3">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="U_PASS">Password:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="U_PASS" name="U_PASS" placeholder="Account Password" type="password" value="" required minlength="3">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="U_ROLE">Role:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" name="U_ROLE" id="U_ROLE" required>
                    <option value="">Select Role</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
            <a href="index.php" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
        </div>
    </div>
</form>
<script>
// Simple client-side validation for instant feedback
if (window.jQuery) {
    $(function() {
        $('#addUserForm').on('submit', function(e) {
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