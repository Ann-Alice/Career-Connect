<?php
     if (!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     }

?>
<div class="row" style="margin-top: 24px;">
  <div class="col-lg-8 col-md-10 col-sm-12" style="margin: 0 auto;">
    <div class="panel panel-default" style="border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,0.08); padding: 28px 32px 18px 32px; background: #f7fafd;">
      <div class="panel-heading" style="background: none; border: none; padding: 0 0 18px 0;">
        <h1 class="page-header" style="margin: 0; font-size: 2rem; color: #337ab7; font-weight: 600; display: flex; align-items: center; gap: 10px;">
          <i class="fa fa-building"></i> Add New Company
        </h1>
      </div>
      <form class="form-horizontal" action="controller.php?action=add" method="POST" style="margin-top: 10px;">
        <div class="form-group">
          <label class="col-md-4 control-label" for="COMPANYNAME" style="font-weight: 500;">Company Name <span style="color: #d9534f;">*</span></label>
          <div class="col-md-8">
            <input class="form-control input-lg" id="COMPANYNAME" name="COMPANYNAME" placeholder="Enter company name" type="text" value="" autocomplete="off" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="COMPANYADDRESS" style="font-weight: 500;">Company Address <span style="color: #d9534f;">*</span></label>
          <div class="col-md-8">
            <textarea class="form-control input-lg" id="COMPANYADDRESS" name="COMPANYADDRESS" placeholder="Enter company address" required style="min-height: 60px;" autocomplete="off"></textarea>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="COMPANYCONTACTNO" style="font-weight: 500;">Company Contact No. <span style="color: #d9534f;">*</span></label>
          <div class="col-md-8">
            <input class="form-control input-lg" id="COMPANYCONTACTNO" name="COMPANYCONTACTNO" placeholder="Enter contact number" type="text" value="" autocomplete="off" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="COMPANYSTATUS" style="font-weight: 500;">Company Status</label>
          <div class="col-md-8">
            <select class="form-control input-lg" id="COMPANYSTATUS" name="COMPANYSTATUS">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
              <option value="Suspended">Suspended</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="COMPANYMISSION" style="font-weight: 500;">Company Mission</label>
          <div class="col-md-8">
            <textarea class="form-control input-lg" id="COMPANYMISSION" name="COMPANYMISSION" placeholder="Enter company mission (optional)" style="min-height: 80px;" autocomplete="off"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-8 col-md-offset-4" style="display: flex; gap: 10px;">
            <button class="btn btn-primary btn-lg" name="save" type="submit" style="border-radius: 25px; min-width: 120px;">
              <span class="fa fa-save"></span> Save
            </button>
            <a href="index.php" class="btn btn-default btn-lg" style="border-radius: 25px; min-width: 120px;">
              <span class="fa fa-arrow-left"></span> Back
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
@media (max-width: 991px) {
  .panel.panel-default {
    padding: 10px 2px 2px 2px !important;
  }
  .form-control.input-lg {
    font-size: 15px;
    height: 40px;
  }
  .panel-heading h1 {
    font-size: 1.2rem !important;
  }
  .btn.btn-primary.btn-lg, .btn.btn-default.btn-lg {
    font-size: 14px;
    padding: 8px 10px;
    min-width: 90px;
  }
}
.form-control.input-lg, textarea.form-control.input-lg {
  font-size: 16px;
  height: 46px;
  border-radius: 8px;
}
textarea.form-control.input-lg {
  min-height: 60px;
  resize: vertical;
}
</style>
 