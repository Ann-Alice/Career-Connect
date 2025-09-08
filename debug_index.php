<?php 
// Debug version - add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🐛 Debug Mode - Loading Dashboard</h2>";

try {
    echo "<p>Step 1: Loading initialize.php...</p>";
    require_once("include/initialize.php"); 
    echo "<p style='color: green;'>✅ initialize.php loaded</p>";
    
    echo "<p>Step 2: Setting variables...</p>";
    $content='home.php';
    $view = (isset($_GET['q']) && $_GET['q'] != '') ? $_GET['q'] : '';
    echo "<p>View parameter: '$view'</p>";
    
    echo "<p>Step 3: Processing switch statement...</p>";
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
    
    echo "<p style='color: green;'>✅ Switch statement processed</p>";
    echo "<p>Title: $title</p>";
    echo "<p>Content file: $content</p>";
    echo "<p>Active home: $active_home</p>";
    
    echo "<p>Step 4: Loading theme/templates.php...</p>";
    if (file_exists("theme/templates.php")) {
        require_once("theme/templates.php");
        echo "<p style='color: green;'>✅ templates.php loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ templates.php not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?> 