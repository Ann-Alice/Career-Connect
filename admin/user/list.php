<?php
	 if (!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     }

?> 
       	 <div class="col-lg-12">
            <h1 class="page-header">List of Users  <a href="index.php?view=add" class="btn btn-primary btn-xs  ">  <i class="fa fa-plus-circle fw-fa"></i> Add User</a>  </h1>
       		</div>
        	<!-- /.col-lg-12 --> 
   		 	<div class="col-lg-12"> 
				<table id="dash-table" class="table  table-bordered table-hover table-responsive" style="font-size:12px;" cellspacing="0"> 
				  <thead>
				  	<tr>
				  		<th>Account ID</th>
				  		<th> Account Name</th>
				  		<th>Username</th>
				  		<th>Role</th>
				  		<th width="10%" >Action</th>
				 
				  	</tr>	
				  </thead> 
				  <tbody>
				  	<?php 
				  		// $mydb->setQuery("SELECT * 
								// 			FROM  `tblusers` WHERE TYPE != 'Customer'");
				  		$mydb->setQuery("SELECT * 
											FROM  `tblusers`");
				  		$cur = $mydb->loadResultList();

						if ($cur) {
							foreach ($cur as $result) {
								echo '<tr>';
								echo '<td>' . htmlspecialchars($result->USERID) . '</td>';
								echo '<td>' . htmlspecialchars($result->FULLNAME) . '</td>';
								echo '<td>' . htmlspecialchars($result->USERNAME) . '</td>';
								echo '<td>' . htmlspecialchars($result->ROLE) . '</td>';
								$isProtected = ($result->USERID == $_SESSION['ADMIN_USERID'] || $result->ROLE == 'MainAdministrator' || $result->ROLE == 'Administrator');
								$deleteBtn = $isProtected
									? '<a title="Delete (disabled for protected users)" class="btn btn-danger btn-xs disabled" style="pointer-events:none;opacity:0.6;" data-toggle="tooltip" data-placement="top" title="You cannot delete this user."><span class="fa fa-trash-o fw-fa"></span></a>'
									: '<a title="Delete" href="controller.php?action=delete&id=' . htmlspecialchars($result->USERID) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this user?\')"><span class="fa fa-trash-o fw-fa"></span></a>';
								echo '<td align="center" > <a title="Edit" href="index.php?view=edit&id=' . htmlspecialchars($result->USERID) . '"  class="btn btn-primary btn-xs  ">  <span class="fa fa-edit fw-fa"></span></a>
									' . $deleteBtn . '</td>';
								echo '</tr>';
							}
						} else {
							echo '<tr><td colspan="5" class="text-center">No users found</td></tr>';
						}
				  	?>
				  </tbody>
					
				</table>  
			</div> 
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
    $('[data-toggle="tooltip"]').tooltip();
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
 