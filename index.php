<?php 
require_once("include/initialize.php"); 
?>
<!DOCTYPE html>
<html>
<head>
    <!-- CSS files first -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // Ensure jQuery is loaded
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://code.jquery.com/jquery-3.5.1.min.js"><\/script>');
        }
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.dataTable !== 'undefined') {
            $.fn.dataTable.ext.errMode = 'none';
            var tables = document.getElementsByClassName('table');
            if (tables.length > 0) {
                Array.from(tables).forEach(function(table) {
                    if (!$.fn.DataTable.isDataTable(table)) {
                        $(table).DataTable({
                            "pagingType": "simple_numbers",
                            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                            "pageLength": 10
                        });
                    }
                });
            }
        }
    });
    </script>
</head>
<body>
<?php
$content='home.php';
$view = (isset($_GET['q']) && $_GET['q'] != '') ? $_GET['q'] : '';
switch ($view) { 
	case 'apply' :
        $title="Submit Application";	
		$content='applicationform.php';		
		break;
	case 'login' : 
        $title="Login";	
		$content='login.php';		
		break;
	case 'company' :
        $title="Company";	
		$content='company.php';		
		break;
	case 'hiring' :
		$title = isset($_GET['search']) ? 'Hiring in '.$_GET['search'] :"Hiring"; 
		$content='hirring.php';		
		break;		
	case 'category' :
        $title='Search for '. $_GET['search'];	
		$content='category.php';		
		break;
	case 'viewjob' :
        $title="Job Details";	
		$content='viewjob.php';		
		break;
	case 'success' :
        $title="Success";	
		$content='success.php';		
		break;
	case 'register' :
        $title="Register New Member";	
		$content='register.php';		
		break;
	case 'Contact' :
        $title='Contact Us';	
		$content='Contact.php';		
		break;	
	case 'About' :
        $title='About Us';	
		$content='About.php';		
		break;	
	case 'advancesearch' :
        $title='Advance Search';	
		$content='advancesearch.php';		
		break;	

	case 'result' :
        $title='Advance Search';	
		$content='advancesearchresult.php';		
		break;
	case 'search-company' :
        $title='Search by Company';	
		$content='searchby';		
		break;	
	case 'search-function' :
        $title='Search by Function';	
		$content='searchbyfunction.php';		
		break;	
	case 'search-jobtitle' :
        $title='Search by Job Title';	
		$content='searchbytitle.php';		
		break;						
	default :
	    $active_home='active';
	    $title="Home";	
		$content ='home.php';		
}
require_once("theme/templates.php");
?>