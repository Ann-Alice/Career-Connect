<style type="text/css">
#content {
	min-height: 500px; 
	padding: 40px 0;
	background-color: #f8f9fa;
}

.container {
	max-width: 1000px;
	margin: 0 auto;
}

.search-header {
	text-align: center;
	margin-bottom: 30px;
}

.search-header h2 {
	color: #2c3e50;
	font-size: 28px;
	font-weight: 600;
	margin-bottom: 15px;
}

.search-header p {
	color: #7f8c8d;
	font-size: 16px;
	margin-bottom: 0;
}

#custom-search-input {
	padding: 0;
	border: none;
	border-radius: 8px;
	background-color: #fff;
	box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
	transition: all 0.3s ease;
}

#custom-search-input:focus-within {
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
	transform: translateY(-2px);
}

#custom-search-input input {
	border: 0;
	box-shadow: none;
	height: 60px;
	font-size: 16px;
	padding: 0 20px;
	color: #2c3e50;
}

#custom-search-input input::placeholder {
	color: #95a5a6;
}

#custom-search-input input:focus {
	box-shadow: none;
	outline: none;
}

#custom-search-input button {
	margin: 0;
	background: #3498db;
	box-shadow: none;
	border: 0;
	color: #fff;
	padding: 0 25px;
	height: 60px;
	border-radius: 0 8px 8px 0;
	transition: all 0.3s ease;
}

#custom-search-input button:hover {
	background: #2980b9;
	color: #fff;
}

#custom-search-input .glyphicon-search {
	font-size: 20px;
}

.input-group {
	display: flex;
	align-items: center;
}

.input-group-btn {
	display: flex;
	align-items: center;
}

@media (max-width: 768px) {
	#custom-search-input input {
		height: 50px;
		font-size: 14px;
	}
	
	#custom-search-input button {
		height: 50px;
		padding: 0 20px;
	}
	
	.search-header h2 {
		font-size: 24px;
	}
}
</style>
<form action="index.php?q=result&searchfor=bytitle" method="POST"> 
 <section id="content">
 <div class="container">
	<div class="row">
		<div class="col-md-2"></div>
        <div class="col-md-8">
    		<div class="search-header">
    			<h2>Search Jobs by Title</h2>
    			<p>Find the perfect job by searching through our extensive database of job titles</p>
    		</div>
            <div id="custom-search-input">
                <div class="input-group">
                    <input type="text" name="SEARCH" class="form-control" placeholder="Enter job title, position, or role..." />
                    <span class="input-group-btn">
                        <button class="btn btn-info" type="submit">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div> 
		<div class="col-md-2"></div>
	</div>
</div>
 </section>
 </form>