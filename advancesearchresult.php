<?php 
	$searchfor = (isset($_GET['searchfor']) && $_GET['searchfor'] != '') ? $_GET['searchfor'] : '';
	
?>
<style type="text/css">
	/*    --------------------------------------------------
	:: General
	-------------------------------------------------- */
body {
	font-family: 'Open Sans', sans-serif;
	color: #2c3e50;
	background-color: #f8f9fa;
}
.content {
	padding: 40px 0;
	min-height: 500px;
}
.content h1 {
	text-align: center;
	color: #2c3e50;
	margin-bottom: 30px;
	font-weight: 600;
}
.content .content-footer p {
	color: #6d6d6d;
    font-size: 12px;
    text-align: center;
}
.content .content-footer p a {
	color: inherit;
	font-weight: bold;
}

/*	--------------------------------------------------
	:: Table Filter
	-------------------------------------------------- */
.panel {
	border: none;
	background-color: #fff;
	border-radius: 8px;
	box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
	margin-bottom: 30px;
}
.panel .btn-group {
	margin: 15px 0 30px;
} 
.table-filter {
	background-color: #fff;
	border: none;
}
.table-filter tbody tr {
	border-bottom: 1px solid #eee;
	transition: all 0.3s ease;
}
.table-filter tbody tr:hover {
	cursor: pointer;
	background-color: #f8f9fa;
	transform: translateY(-2px);
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.table-filter tbody tr td {
	padding: 20px;
	vertical-align: middle;
	border-top: none;
}
.table-filter tbody tr.selected td {
	background-color: #eee;
}
.table-filter tr td:first-child {
	width: 38px;
}
.table-filter tr td:nth-child(2) {
	width: 35px;
}
 
.table-filter .star {
	color: #ccc;
	text-align: center;
	display: block;
}
.table-filter .star.star-checked {
	color: #F0AD4E;
}
.table-filter .star:hover {
	color: #ccc;
}
.table-filter .star.star-checked:hover {
	color: #F0AD4E;
}
.table-filter .media-photo {
	width: 35px;
}
.table-filter .media-body {
    display: block;
    /* Had to use this style to force the div to expand (wasn't necessary with my bootstrap version 3.3.6) */
}
.table-filter .media-meta {
	font-size: 11px;
	color: #999;
}
.table-filter .media .title {
	color: #2BBCDE;
	font-size: 14px;
	font-weight: bold;
	line-height: normal;
	margin: 0;
}
.table-filter .media .title span {
	font-size: .8em;
	margin-right: 20px;
}
.table-filter .media .title span.pagado {
	color: #5cb85c;
}
.table-filter .media .title span.pendiente {
	color: #f0ad4e;
}
.table-filter .media .title span.cancelado {
	color: #d9534f;
}
.table-filter .media .summary {
	font-size: 14px;
}

.search-summary {
	background-color: #f8f9fa;
	padding: 15px 20px;
	border-radius: 6px;
	margin-bottom: 25px;
	border-left: 4px solid #3498db;
}

.search-summary p {
	margin: 0;
	color: #2c3e50;
	font-size: 16px;
}

.table-container {
	margin-top: 20px;
}

.media {
	display: flex;
	align-items: flex-start;
}

.media .fa-building-o {
	font-size: 24px;
	color: #3498db;
	background: #e8f4fc;
	padding: 15px;
	border-radius: 8px;
	margin-right: 20px;
}

.media-body {
	flex: 1;
}

.media .title {
	margin: 0 0 10px 0;
}

.media .title a {
	color: #2c3e50;
	font-size: 18px;
	font-weight: 600;
	text-decoration: none;
	transition: color 0.3s ease;
}

.media .title a:hover {
	color: #3498db;
}

.media .title span {
	font-size: 14px;
	color: #7f8c8d;
	font-weight: normal;
}

.media-meta {
	font-size: 13px;
	color: #95a5a6;
	margin-bottom: 5px;
}

.summary {
	color: #34495e;
	font-size: 14px;
	line-height: 1.6;
	margin: 0;
}

.no-results {
	text-align: center;
	padding: 40px;
	color: #7f8c8d;
	font-size: 16px;
}

.no-results i {
	font-size: 48px;
	color: #bdc3c7;
	margin-bottom: 15px;
	display: block;
}
</style>
<div class="container">
	<div class="row">

		<section class="content">
			 
			<div class="col-md-12 ">
				<div class="panel">
					<div class="panel-body">
						<div class="search-summary">
							<?php 
							$search = isset($_POST['SEARCH']) ? ($_POST['SEARCH']!='') ? $_POST['SEARCH'] : 'All' : 'All';
							$company = isset($_POST['COMPANY']) ? ($_POST['COMPANY']!='') ? $_POST['COMPANY'] : 'All' : 'All';
							$category = isset($_POST['CATEGORY']) ? ($_POST['CATEGORY']!='') ? $_POST['CATEGORY'] : 'All' : 'All';

							switch ($searchfor) {
								case 'bycompany':
									echo '<p><i class="fa fa-search"></i> Showing results for: <strong>' . $search . '</strong> in company: <strong>' . $company . '</strong></p>';
									break;
								case 'advancesearch':
									echo '<p><i class="fa fa-search"></i> Showing results for: <strong>' . $search . '</strong> in company: <strong>' . $company . '</strong> and function: <strong>' . $category . '</strong></p>';
									break;
								case 'byfunction':
									echo '<p><i class="fa fa-search"></i> Showing results for: <strong>' . $search . '</strong> in function: <strong>' . $category . '</strong></p>';
									break;
								case 'bytitle':
									echo '<p><i class="fa fa-search"></i> Showing results for: <strong>' . $search . '</strong></p>';
									break;
								default:
									break;
							}
							?>
						</div>
						<div class="table-container">
							<table class="table table-filter">
								<tbody>
									<?php 
									$search = isset($_POST['SEARCH']) ? $_POST['SEARCH'] : '';
									$company = isset($_POST['COMPANY']) ? $_POST['COMPANY'] : '';
									$category = isset($_POST['CATEGORY']) ? $_POST['CATEGORY'] : '';

									// Sanitize inputs
									$search = addslashes($search);
									$company = addslashes($company);
									$category = addslashes($category);

									$sql = "SELECT * FROM `tbljob` j, `tblcompany` c 
									WHERE j.`COMPANYID`=c.`COMPANYID` AND c.`COMPANYNAME` LIKE '%{$company}%' AND j.`CATEGORY` LIKE '%{$category}%' AND (j.`OCCUPATIONTITLE` LIKE '%{$search}%' OR j.`JOBDESCRIPTION` LIKE '%{$search}%' OR j.`QUALIFICATION_WORKEXPERIENCE` LIKE '%{$search}%')";
									$mydb->setQuery($sql);
									$cur = $mydb->executeQuery();
									$maxrow = $mydb->num_rows($cur);

									if ($maxrow > 0) {
										$res = $mydb->loadResultList();
										foreach ($res as $row) { 
									?>
									<tr>  
										<td> 
											<div class="media">
												<div class="fa fa-building-o"></div>
												<div class="media-body">
													<span class="media-meta"><?php echo $row->OCCUPATIONTITLE; ?></span>
													<h4 class="title">
														<a href="index.php?q=viewjob&search=<?php echo $row->JOBID ?>">
															<?php echo $row->OCCUPATIONTITLE; ?> 
														</a>
														<span class="pull-right"><?php echo $row->COMPANYNAME ?></span>
													</h4>
													<p class="summary"><?php echo $row->JOBDESCRIPTION; ?></p>
												</div>
											</div> 
										</td>
									</tr>
								<?php } } else { ?>
								<tr>
									<td>
										<div class="no-results">
											<i class="fa fa-search"></i>
											<p>No results found matching your search criteria.</p>
											<p>Try adjusting your search terms or filters.</p>
										</div>
									</td>
								</tr>
								<?php } ?>
								 
								</tbody>
							</table>
						</div>
					</div>
				</div> 
			</div>
		</section>
		
	</div>
</div>