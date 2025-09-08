<?php
	 if(!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     }

?> 
	<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Employee Management
            <a href="index.php?view=add" class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-plus-circle"></i> Add New Employee
            </a>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-users"></i> List of Employees
                <div class="pull-right">
                    <span class="badge"><?php 
                        $mydb->setQuery("SELECT COUNT(*) as count FROM tblemployees");
                        $result = $mydb->loadSingleResult();
                        echo $result->count . " Total Employees";
                    ?></span>
                </div>
            </div>
            <div class="panel-body">
                <form class="wow fadeInDownaction" action="controller.php?action=delete" Method="POST">   		
							<div class="table-responsive">
								<table id="dash-table" class="table table-striped table-hover table-bordered" style="font-size:12px" cellspacing="0">

								  <thead>
								  	<tr>
								  		<th width="5%">Employee ID</th>
								  		 <th>Name</th>
								  		<th>Address</th>
								  		 <th>Sex</th>
								  		 <th>Age</th>
								  		 <th>Contact No</th>
								  		 <!-- <th>Department</th> -->
								  		 <th>Position</th>
								  		 <!-- <th>Work Status</th> -->
								  	 	<th width="14%" >Action</th> 
								  	</tr>	
								  </thead> 
								  <tbody>
								  	<?php   
								  		// $mydb->setQuery("SELECT * 
											// 			FROM  `tblusers` WHERE TYPE != 'Customer'");
								  		$mydb->setQuery("SELECT * 
														FROM   `tblemployees` ORDER BY EMPLOYEEID DESC");
								  		$cur = $mydb->loadResultList();

									if ($cur) {
										foreach ($cur as $result) { 
								  		echo '<tr>';
								  		// echo '<td width="5%" align="center"></td>';
								  		echo '<td>' . htmlspecialchars($result->EMPLOYEEID) . '</td>';
								  		echo '<td>' . htmlspecialchars($result->LNAME) . ', ' . htmlspecialchars($result->FNAME) . '</td>';
								  		echo '<td>' . htmlspecialchars($result->ADDRESS) . '</td>';
								  		echo '<td>' . htmlspecialchars($result->SEX) . '</td>';
								  		echo '<td>' . htmlspecialchars($result->AGE) . '</td>';
								  		echo '<td>' . htmlspecialchars($result->TELNO) . '</td>';
								  		// echo '<td>'. $result->DEPARTMENT.'</td>';
								  		echo '<td>' . htmlspecialchars($result->POSITION) . '</td>';
								  		// echo '<td>'. $result->WORKSTATS.'</td>'; 
						  				echo '<td align="center" >    
						  		             <a title="Edit" href="index.php?view=edit&id=' . htmlspecialchars($result->EMPLOYEEID) . '" class="btn btn-info btn-xs">
						  		             <span class="fa fa-edit"></span> Edit</a> 
						  		             <a title="Delete" href="controller.php?action=delete&id=' . htmlspecialchars($result->EMPLOYEEID) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this employee?\')">
						  		             <span class="fa fa-trash-o"></span> Delete</a> 
						  					 </td>';
								  		echo '</tr>';
								  	}
								  } else {
								  	echo '<tr><td colspan="8" class="text-center">No employees found</td></tr>';
								  }
								  	?>
								  </tbody>
								
								</table>
							</div>
 
							 
							</form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dash-table').DataTable({
        responsive: true,
        "order": [[0, "desc"]],
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
.panel-heading {
    padding: 15px;
}
.badge {
    font-size: 14px;
    padding: 8px 12px;
}
</style>
 