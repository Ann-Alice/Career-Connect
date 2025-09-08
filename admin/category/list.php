<?php 
	  if (!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     } 
?>
	<div class="row">
       	 <div class="col-lg-12">
            <h1 class="page-header">List of Categories  <a href="index.php?view=add" class="btn btn-primary btn-xs  ">  <i class="fa fa-plus-circle fw-fa"></i> Add Category</a>  </h1>
       		</div>
        	<!-- /.col-lg-12 -->
   		 </div>
	 		    <form action="controller.php?action=delete" Method="POST">  	
			     <div class="table-responsive">					
				<table id="dash-table" class="table table-striped table-bordered table-hover"  style="font-size:12px" cellspacing="0">
				
				  <thead>
				  	<tr>
				  		<!-- <th>No.</th> -->
				  		<th>
				  		 <!-- <input type="checkbox" name="chkall" id="chkall" onclick="return checkall('selector[]');">  -->
				  		 Category</th> 
				  		 <th width="10%" align="center">Action</th>
				  	</tr>	
				  </thead> 
				  <tbody>
				  	<?php 
				  		$mydb->setQuery("SELECT * FROM `tblcategory`");
				  		$cur = $mydb->loadResultList();

						if ($cur) {
							foreach ($cur as $result) {
								echo '<tr>';
								// echo '<td width="5%" align="center"></td>';
								// echo '<td>
								//      <input type="checkbox" name="selector[]" id="selector[]" value="'.$result->CATEGORYID. '"/>
								// 		' . $result->CATEGORIES.'</a></td>';
								echo '<td>' . htmlspecialchars($result->CATEGORY) . '</td>';
								echo '<td align="center"><a title="Edit" href="index.php?view=edit&id=' . htmlspecialchars($result->CATEGORYID) . '" class="btn btn-primary btn-xs"><span class="fa fa-edit fw-fa"></span></a> '
									. '<a title="Delete" href="controller.php?action=delete&id=' . htmlspecialchars($result->CATEGORYID) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this category?\')"><span class="fa fa-trash-o fw-fa"></span></a></td>';
								// echo '<td></td>';
								echo '</tr>';
							}
						} else {
							echo '<tr><td colspan="2" class="text-center">No categories found</td></tr>';
						}
				  	?>
				  </tbody>
					
				</table>
						<div class="btn-group">
				 <!--  <a href="index.php?view=add" class="btn btn-default">New</a> -->
					<?php
					if($_SESSION['ADMIN_ROLE']=='Administrator'){
					// echo '<button type="submit" class="btn btn-default" name="delete"><span class="glyphicon glyphicon-trash"></span> Delete Selected</button'
					; }?>
				</div>
			
			
				</form>
	
 <div class="table-responsive">	 
<script>
$(document).ready(function() {
    $('#dash-table').DataTable({
        responsive: true,
        "order": [[0, "asc"]],
        "pageLength": 10,
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });
});
</script>
<style>
.table > tbody > tr > td {
    vertical-align: middle;
}
.btn {
    margin-right: 5px;
}
</style>