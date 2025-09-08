<style type="text/css">
#content {
	min-height: 500px;
	padding: 40px 0;
	background-color: #f8f9fa;
}

#content .panel {
	padding: 25px;
	background: #fff;
	border-radius: 8px;
	box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
}

.panel-header {
	margin-bottom: 20px;
	border-bottom: 2px solid #eee;
	padding-bottom: 15px;
}

.panel-header h3 {
	color: #2c3e50;
	font-size: 24px;
	margin: 0;
	font-weight: 600;
}

.panel-body label {
	font-size: 16px;
	font-weight: 600;
	color: #2c3e50;
	margin-bottom: 8px;
}

.panel-body input,
.panel-body select {
	font-size: 14px;
	padding: 10px 15px;
	border: 1px solid #ddd;
	border-radius: 4px;
	transition: all 0.3s ease;
}

.panel-body input:focus,
.panel-body select:focus {
	border-color: #3498db;
	box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
	outline: none;
}

.panel-body > .row {
	margin-bottom: 20px;
}

.search1 {
	display: flex;
	align-items: center;
}

.btn-success {
	background-color: #2ecc71;
	border: none;
	padding: 12px 30px;
	font-size: 16px;
	font-weight: 600;
	border-radius: 4px;
	transition: all 0.3s ease;
}

.btn-success:hover {
	background-color: #27ae60;
	transform: translateY(-2px);
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.form-control {
	height: auto;
	line-height: 1.5;
}

.form-control:focus {
	border-color: #3498db;
	box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
}
</style>

<form action="index.php?q=result&searchfor=byfunction" method="POST"> 
<section id="content">
	<div class="container content">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
			<div class="panel">
				<div class="panel-header">
					<h3>Search by Job Function</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12 search1">
							<label class="col-sm-3">Search Keywords:</label>
							<div class="col-sm-9">
								<input class="form-control" type="text" name="SEARCH" placeholder="Enter job title, skills, or keywords">
							</div>
						</div>
					</div>   
					<div class="row">
						<div class="col-sm-12 search1">
							<label class="col-sm-3">Job Function:</label>
							<div class="col-sm-9">
								<select class="form-control" name="CATEGORY">
									<option value="">All Functions</option>
									<?php
										$sql = "SELECT * FROM `tblcategory`";
										$mydb->setQuery($sql);
										$res = $mydb->loadResultList();
										foreach ($res as $row) { 
											echo '<option>'.$row->CATEGORY.'</option>';
										}
									?>
								</select>
							</div>
						</div>
					</div>  
					<div class="row">
						<div class="col-sm-12 search1">
							<label class="col-sm-3"></label>
							<div class="col-sm-9">
								<input type="submit" name="submit" value="Search Jobs" class="btn btn-success">
							</div>
						</div>
					</div>  
				</div>
			</div> 
		</div>
		<div class="col-sm-2"></div> 
	</div>
</section>
</form>