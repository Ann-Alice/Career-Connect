<?php
    if (!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     }


  $categoryid = $_GET['id'];
  $category = New Category();
  $singlecategory = $category->single_category($categoryid);

?> 
<form class="form-horizontal span6" action="controller.php?action=edit" method="POST" id="editCategoryForm" novalidate>
    <fieldset>
        <legend>Update Category</legend>
        <div class="form-group">
            <div class="col-md-8">
                <label class="col-md-4 control-label" for="CATEGORY">Category:</label>
                <div class="col-md-8">
                    <input id="CATEGORYID" name="CATEGORYID" type="hidden" value="<?php echo htmlspecialchars($singlecategory->CATEGORYID); ?>">
                    <input class="form-control input-sm" id="CATEGORY" name="CATEGORY" placeholder="Category" type="text" value="<?php echo htmlspecialchars($singlecategory->CATEGORY); ?>" required minlength="2">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-8 col-md-offset-4">
                <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                <a href="index.php" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
            </div>
        </div>
    </fieldset>
</form>
<script>
// Simple client-side validation for instant feedback
if (window.jQuery) {
    $(function() {
        $('#editCategoryForm').on('submit', function(e) {
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

        </div><!--End of container-->
  