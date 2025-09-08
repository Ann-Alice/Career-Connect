<?php 
	  if (!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     } 
?>
	<div class="row" style="margin-top: 18px;">
       	 <div class="col-lg-12">
            <div class="panel panel-default" style="border-radius: 12px; box-shadow: 0 4px 18px rgba(0,0,0,0.08); padding: 18px 24px 10px 24px; background: #f7fafd;">
              <div class="panel-heading" style="display: flex; align-items: center; justify-content: space-between; background: none; border: none; padding: 0 0 12px 0;">
                <h1 class="page-header" style="margin: 0; font-size: 2rem; color: #337ab7; font-weight: 600;">
                  <i class="fa fa-building"></i> List of Companies
                </h1>
                <a href="index.php?view=add" class="btn btn-primary btn-md" style="border-radius: 25px; font-weight: 500;">
                  <i class="fa fa-plus-circle"></i> Add Company
                </a>
              </div>
              <form action="controller.php?action=delete" Method="POST">  	
			     <div class="table-responsive">					
				<table id="dash-table" class="table table-striped table-bordered table-hover company-table" style="font-size:13px; background: #fff; border-radius: 8px; overflow: hidden;">
				
				  <thead style="background: #e3f2fd;">
				  	<tr>
				  		<!-- <th>No.</th> -->
				  		<th>Name</th> 
				  		<th>Address</th> 
				  		<th>Contact No.</th> 
				  		 <th width="12%" align="center">Action</th>
				  	</tr>	
				  </thead> 
				  <tbody>
				  	<?php 
				  		$mydb->setQuery("SELECT * FROM `tblcompany`");
				  		$cur = $mydb->loadResultList(); 
						foreach ($cur as $result) {
				  		echo '<tr>';
				  		// echo '<td width="5%" align="center"></td>';
				  		// echo '<td>
				  		//      <input type="checkbox" name="selector[]" id="selector[]" value="'.$result->CATEGORYID. '"/>
				  		// 		' . $result->CATEGORIES.'</a></td>';
				  			echo '<td>' . $result->COMPANYNAME.'</td>';
				  			echo '<td>' . $result->COMPANYADDRESS.'</td>';
				  			echo '<td>' . $result->COMPANYCONTACTNO.'</td>';
				  		echo '<td align="center">
				  			<a title="Edit" href="index.php?view=edit&id='.$result->COMPANYID.'" class="btn btn-primary btn-xs" style="border-radius: 18px; margin-right: 4px;" data-toggle="tooltip" data-placement="top" title="Edit Company">
				  				<span class="fa fa-edit"></span>
				  			</a>
				  			<a title="Delete" href="controller.php?action=delete&id='.$result->COMPANYID.'" class="btn btn-danger btn-xs" style="border-radius: 18px;" data-toggle="tooltip" data-placement="top" title="Delete Company" onclick="return confirm(\'Are you sure you want to delete this company?\');">
				  				<span class="fa fa-trash-o"></span>
				  			</a>
				  		</td>';
				  		// echo '<td></td>';
				  		echo '</tr>';
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

<style>
.company-table th, .company-table td {
  vertical-align: middle !important;
}
.company-table tbody tr:hover {
  background: #f1f8ff;
  transition: background 0.2s;
}
@media (max-width: 767px) {
  .panel.panel-default {
    padding: 8px 2px 2px 2px !important;
  }
  .company-table th, .company-table td {
    font-size: 12px;
    padding: 6px 4px;
  }
  .panel-heading h1 {
    font-size: 1.2rem !important;
  }
  .btn.btn-primary.btn-md {
    font-size: 13px;
    padding: 6px 12px;
  }
}
</style>