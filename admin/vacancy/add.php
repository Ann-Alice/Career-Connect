<?php
     if (!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     }

?>
<form class="form-horizontal span6" action="controller.php?action=add" method="POST" id="addJobForm" novalidate>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add New Job Vacancy</h1>
        </div>
    </div>
    <!-- Company Name -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="COMPANYID">Company Name:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" id="COMPANYID" name="COMPANYID" required>
                    <option value="">Select</option>
                    <?php
                    $sql = "SELECT * FROM tblcompany ORDER BY COMPANYNAME";
                    $mydb->setQuery($sql);
                    $res = $mydb->loadResultList();
                    foreach ($res as $row) {
                        echo '<option value="' . htmlspecialchars($row->COMPANYID) . '">' . htmlspecialchars($row->COMPANYNAME) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <!-- Category -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="CATEGORY">Category :</label>
            <div class="col-md-8">
                <select class="form-control input-sm" id="CATEGORY" name="CATEGORY" required>
                    <option value="">Select</option>
                    <?php
                    $sql = "SELECT * FROM tblcategory ORDER BY CATEGORY";
                    $mydb->setQuery($sql);
                    $res = $mydb->loadResultList();
                    foreach ($res as $row) {
                        echo '<option value="' . htmlspecialchars($row->CATEGORY) . '">' . htmlspecialchars($row->CATEGORY) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <!-- Occupation Title -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="OCCUPATIONTITLE">Occupation Title:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="OCCUPATIONTITLE" name="OCCUPATIONTITLE" placeholder="Occupation Title" required minlength="3" autocomplete="off" />
            </div>
        </div>
    </div>
    <!-- Required Employees -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="REQ_NO_EMPLOYEES">Required no. of Employees:</label>
            <div class="col-md-8">
                <input type="number" class="form-control input-sm" id="REQ_NO_EMPLOYEES" name="REQ_NO_EMPLOYEES" placeholder="Required no. of Employees" required min="1" autocomplete="off" />
            </div>
        </div>
    </div>
    <!-- Salary -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="SALARIES">Salary:</label>
            <div class="col-md-8">
                <input type="number" class="form-control input-sm" id="SALARIES" name="SALARIES" placeholder="Salary (numeric only)" required min="0" step="0.01" autocomplete="off" />
            </div>
        </div>
    </div>
    <!-- Duration of Employment -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="DURATION_EMPLOYEMENT">Duration of Employment:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="DURATION_EMPLOYEMENT" name="DURATION_EMPLOYEMENT" placeholder="Duration of Employment" required autocomplete="off" />
            </div>
        </div>
    </div>
    <!-- Qualification/Work Experience -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="QUALIFICATION_WORKEXPERIENCE">Qualification/Work Experience:</label>
            <div class="col-md-8">
                <textarea class="form-control input-sm" id="QUALIFICATION_WORKEXPERIENCE" name="QUALIFICATION_WORKEXPERIENCE" placeholder="Qualification/Work Experience" required rows="2"></textarea>
            </div>
        </div>
    </div>
    <!-- Job Description -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="JOBDESCRIPTION">Job Description:</label>
            <div class="col-md-8">
                <textarea class="form-control input-sm" id="JOBDESCRIPTION" name="JOBDESCRIPTION" placeholder="Job Description" required rows="2"></textarea>
            </div>
        </div>
    </div>
    <!-- Preferred Sex -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="PREFEREDSEX">Preferred Sex:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" id="PREFEREDSEX" name="PREFEREDSEX" required>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Male/Female">Male/Female</option>
                </select>
            </div>
        </div>
    </div>
    <!-- Sector of Vacancy -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="SECTOR_VACANCY">Sector of Vacancy:</label>
            <div class="col-md-8">
                <textarea class="form-control input-sm" id="SECTOR_VACANCY" name="SECTOR_VACANCY" placeholder="Sector of Vacancy" required rows="2"></textarea>
            </div>
        </div>
    </div>
    <!-- Job Status -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="JOBSTATUS">Job Status:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" id="JOBSTATUS" name="JOBSTATUS" required>
                    <option value="Open">Open</option>
                    <option value="Closed">Closed</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Filled">Filled</option>
                </select>
            </div>
        </div>
    </div>
    <!-- Form Actions -->
    <div class="form-group">
        <div class="col-md-8">
            <div class="col-md-8 col-md-offset-4">
                <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                <a href="index.php" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
            </div>
        </div>
    </div>
</form>
<script>
// Simple client-side validation for instant feedback
if (window.jQuery) {
    $(function() {
        $('#addJobForm').on('submit', function(e) {
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
 