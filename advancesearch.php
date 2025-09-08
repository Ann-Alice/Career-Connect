<style type="text/css">
#content {
	min-height: 500px;
	padding: 60px 0;
	background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.search-container {
	max-width: 800px;
	margin: 0 auto;
}

.search-card {
	background: #fff;
	border-radius: 12px;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
	overflow: hidden;
	transition: transform 0.3s ease;
}

.search-card:hover {
	transform: translateY(-5px);
}

.search-header {
	background: linear-gradient(135deg, #3498db, #2980b9);
	padding: 25px 30px;
	color: white;
	position: relative;
	overflow: hidden;
}

.search-header::after {
	content: '';
	position: absolute;
	top: 0;
	right: 0;
	width: 150px;
	height: 150px;
	background: rgba(255, 255, 255, 0.1);
	border-radius: 50%;
	transform: translate(30%, -30%);
}

.search-header h3 {
	margin: 0;
	font-size: 28px;
	font-weight: 600;
	display: flex;
	align-items: center;
}

.search-header h3 i {
	margin-right: 12px;
	font-size: 24px;
}

.search-body {
	padding: 30px;
}

.search-group {
	margin-bottom: 25px;
	position: relative;
}

.search-group label {
	display: block;
	font-size: 15px;
	font-weight: 600;
	color: #2c3e50;
	margin-bottom: 10px;
	padding-left: 5px;
}

.search-group label i {
	color: #3498db;
	margin-right: 8px;
}

.search-input {
	position: relative;
}

.search-input input,
.search-input select {
	width: 100%;
	padding: 12px 15px;
	padding-left: 40px;
	font-size: 15px;
	border: 2px solid #e9ecef;
	border-radius: 8px;
	transition: all 0.3s ease;
	background: #f8f9fa;
}

.search-input input:focus,
.search-input select:focus {
	border-color: #3498db;
	background: #fff;
	box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
	outline: none;
}

.search-input i {
	position: absolute;
	left: 15px;
	top: 50%;
	transform: translateY(-50%);
	color: #95a5a6;
	transition: color 0.3s ease;
}

.search-input input:focus + i,
.search-input select:focus + i {
	color: #3498db;
}

.search-button {
	text-align: right;
	margin-top: 10px;
}

.btn-search {
	background: linear-gradient(135deg, #2ecc71, #27ae60);
	color: white;
	border: none;
	padding: 12px 35px;
	font-size: 16px;
	font-weight: 600;
	border-radius: 8px;
	cursor: pointer;
	transition: all 0.3s ease;
	display: inline-flex;
	align-items: center;
	gap: 8px;
}

.btn-search:hover {
	background: linear-gradient(135deg, #27ae60, #219a52);
	transform: translateY(-2px);
	box-shadow: 0 4px 15px rgba(46, 204, 113, 0.2);
}

.btn-search:active {
	transform: translateY(0);
}

.search-tips {
	margin-top: 20px;
	padding: 15px;
	background: #f8f9fa;
	border-radius: 8px;
	border-left: 4px solid #3498db;
}

.search-tips h4 {
	color: #2c3e50;
	font-size: 16px;
	margin: 0 0 10px 0;
	display: flex;
	align-items: center;
	gap: 8px;
}

.search-tips ul {
	margin: 0;
	padding-left: 20px;
	color: #7f8c8d;
	font-size: 14px;
}

.search-tips li {
	margin-bottom: 5px;
}

@media (max-width: 768px) {
	#content {
		padding: 30px 0;
	}
	
	.search-header {
		padding: 20px;
	}
	
	.search-body {
		padding: 20px;
	}
	
	.btn-search {
		width: 100%;
		justify-content: center;
	}
}
</style>

<form action="index.php?q=result&searchfor=advancesearch" method="POST"> 
<section id="content">
	<div class="container">
		<div class="search-container">
			<div class="search-card">
				<div class="search-header">
					<h3><i class="fa fa-search"></i> Advanced Job Search</h3>
				</div>
				<div class="search-body">
					<div class="search-group">
						<label><i class="fa fa-key"></i> Search Keywords</label>
						<div class="search-input">
							<input type="text" name="SEARCH" placeholder="Enter job title, skills, or keywords">
							<i class="fa fa-search"></i>
						</div>
					</div>
					
					<div class="search-group">
						<label><i class="fa fa-building"></i> Company</label>
						<div class="search-input">
							<select name="COMPANY">
								<option value="">All Companies</option>
								<?php
									$sql = "SELECT * FROM tblcompany";
									$mydb->setQuery($sql);
									$res = $mydb->loadResultList();
									foreach ($res as $row) { 
										echo '<option>'.$row->COMPANYNAME.'</option>';
									}
								?>
							</select>
							<i class="fa fa-building"></i>
						</div>
					</div>
					
					<div class="search-group">
						<label><i class="fa fa-briefcase"></i> Job Function</label>
						<div class="search-input">
							<select name="CATEGORY">
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
							<i class="fa fa-briefcase"></i>
						</div>
					</div>
					
					<div class="search-button">
						<button type="submit" name="submit" class="btn-search">
							<i class="fa fa-search"></i> Search Jobs
						</button>
					</div>
					
					<div class="search-tips">
						<h4><i class="fa fa-lightbulb-o"></i> Search Tips</h4>
						<ul>
							<li>Use specific keywords related to your desired position</li>
							<li>Try different variations of job titles</li>
							<li>Filter by company to find opportunities at specific organizations</li>
							<li>Select a job function to narrow down your search</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Add animation to search card
	const searchCard = document.querySelector('.search-card');
	searchCard.style.opacity = '0';
	searchCard.style.transform = 'translateY(20px)';
	
	setTimeout(() => {
		searchCard.style.transition = 'all 0.5s ease';
		searchCard.style.opacity = '1';
		searchCard.style.transform = 'translateY(0)';
	}, 100);

	// Add focus effects to inputs
	const inputs = document.querySelectorAll('.search-input input, .search-input select');
	inputs.forEach(input => {
		input.addEventListener('focus', function() {
			this.parentElement.style.transform = 'scale(1.02)';
		});
		
		input.addEventListener('blur', function() {
			this.parentElement.style.transform = 'scale(1)';
		});
	});

	// Form validation
	const form = document.querySelector('form');
	form.addEventListener('submit', function(e) {
		const searchInput = document.querySelector('input[name="SEARCH"]');
		if (!searchInput.value.trim()) {
			e.preventDefault();
			searchInput.style.borderColor = '#e74c3c';
			searchInput.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.1)';
			
			setTimeout(() => {
				searchInput.style.borderColor = '#e9ecef';
				searchInput.style.boxShadow = 'none';
			}, 2000);
		}
	});
});
</script>