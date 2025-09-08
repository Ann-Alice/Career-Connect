<?php
	 if(!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     }

?> 
	<div class="row">
    <div class="col-lg-12">
            <h1 class="page-header">List of Applicant's   </h1>
       		</div>
        	<!-- /.col-lg-12 -->
   		 </div>
                
 
						<form class="wow fadeInDownaction" action="controller.php?action=delete" Method="POST">   		
							<div class="table-responsive">
								<table id="dash-table" class="table table-striped table-hover table-bordered" style="font-size:12px" cellspacing="0">

								  <thead>
								  	<tr>
										<th>Applicant</th>
										<th>Job Title</th>
										<th>Company</th>
										<th>Applied Date</th> 
										<th>Remarks</th>
										<th width="14%" >Action</th> 
								  	</tr>	
								  </thead> 
								  <tbody>
								  	<?php   
								  		// $mydb->setQuery("SELECT * 
											// 			FROM  `tblusers` WHERE TYPE != 'Customer'");
								  		$mydb->setQuery("SELECT * FROM `tblcompany` c  , `tbljobregistration` j, `tbljob` j2, `tblapplicants` a WHERE c.`COMPANYID`=j.`COMPANYID` AND  j.`JOBID`=j2.`JOBID` AND j.`APPLICANTID`=a.`APPLICANTID` ");
								  		$cur = $mydb->loadResultList();

										if ($cur) {
											foreach ($cur as $result) { 
										  		echo '<tr>';
										  		// echo '<td width="5%" align="center"></td>';
										  		echo '<td>' . htmlspecialchars($result->APPLICANT) . '</td>';
										  		echo '<td>' . htmlspecialchars($result->OCCUPATIONTITLE) . '</td>';
										  		echo '<td>' . htmlspecialchars($result->COMPANYNAME) . '</td>'; 
										  		echo '<td>' . htmlspecialchars($result->REGISTRATIONDATE) . '</td>';
										  		echo '<td>' . htmlspecialchars($result->REMARKS) . '</td>';  
									  				echo '<td align="center" >    
									  		             <a title="View" href="index.php?view=view&id=' . htmlspecialchars($result->REGISTRATIONID) . '" class="btn btn-info btn-xs"><span class="fa fa-info fw-fa"></span> View</a> 
									  		             <a title="Remove" href="index.php?view=delete&id=' . htmlspecialchars($result->REGISTRATIONID) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to remove this applicant?\')"><span class="fa fa-trash-o fw-fa"></span> Remove</a> 
									  					 </td>';
										  		echo '</tr>';
										  	} 
										} else {
											echo '<tr><td colspan="6" class="text-center">No applicants found</td></tr>';
										}
								  	?>
								  </tbody>
								
								</table>
							</div>
 
							 
							</form>
       
                 
 
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
</style>
 