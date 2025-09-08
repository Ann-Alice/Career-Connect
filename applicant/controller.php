<?php
require_once ("../../include/initialize.php");

if(!isset($_SESSION['ADMIN_USERID'])){
     redirect(web_root."admin/index.php");
    }

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add' :
	doInsert();
	break;
	
	case 'edit' :
	doEdit();
	break; 
	
	case 'delete' :
	doDelete();
	break;

	case 'photos' :
	doupdateimage();
	break;
	
	
	case 'addfiles' :
	doAddFiles();
	break;

	case 'approve' :
	doApproved();
	break;

	case 'checkid' :
	Check_StudentID();
	break;
	

	}
   
	function doInsert(){
		global $mydb;
		if(isset($_POST['SAVE'])){
			$birthdate =  date_format(date_create($_POST['BIRTHDATE']),'Y-m-d');
			$age = date_diff(date_create($birthdate),date_create('today'))->y;

			if ($age < 20 ){
		       message("Invalid age. 20 years old and above is allowed.", "error");
		       redirect("index.php?view=accounts");

		    }else{ 
					$applicant =New Applicants(); 
					$applicant->FNAME = $_POST['FNAME'];
					$applicant->LNAME = $_POST['LNAME'];
					$applicant->MNAME = $_POST['MNAME'];
					$applicant->ADDRESS = $_POST['ADDRESS'];
					$applicant->SEX = $_POST['optionsRadios'];
					$applicant->CIVILSTATUS = $_POST['CIVILSTATUS'];
					$applicant->BIRTHDATE = $birthdate;
					$applicant->BIRTHPLACE = $_POST['BIRTHPLACE'];
					$applicant->AGE = $age; 
					$applicant->EMAILADDRESS = $_POST['EMAILADDRESS'];
					$applicant->CONTACTNO = $_POST['TELNO'];
					$applicant->DEGREE = $_POST['DEGREE'];
					$applicant->create();

					message("New Account has been created!", "success");
					redirect("index.php?view=accounts");
	    	}
		}
	}

	function doEdit(){
		$birthdate =  date_format(date_create($_POST['BIRTHDATE']),'Y-m-d');

			$age = date_diff(date_create($birthdate),date_create('today'))->y;
		 	if ($age < 20 ){
		       message("Invalid age. 20 years old and above is allowed.", "error");
		       redirect("index.php?view=accounts");

		    }else{ 
					$applicant =New Applicants(); 
					$applicant->FNAME = $_POST['FNAME'];
					$applicant->LNAME = $_POST['LNAME'];
					$applicant->MNAME = $_POST['MNAME'];
					$applicant->ADDRESS = $_POST['ADDRESS'];
					$applicant->SEX = $_POST['optionsRadios'];
					$applicant->CIVILSTATUS = $_POST['CIVILSTATUS'];
					$applicant->BIRTHDATE = $birthdate;
					$applicant->BIRTHPLACE = $_POST['BIRTHPLACE'];
					$applicant->AGE = $age; 
					$applicant->EMAILADDRESS = $_POST['EMAILADDRESS'];
					$applicant->CONTACTNO = $_POST['TELNO'];
					$applicant->DEGREE = $_POST['DEGREE'];
					$applicant->update($_POST['APPLICANTID']);

					message("Account has been updated!", "success");
					redirect("index.php?view=accounts");
	    	}
	}
   
	function doupdateimage(){
 
			$errofile = $_FILES['photo']['error'];
			$type = $_FILES['photo']['type'];
			$temp = $_FILES['photo']['tmp_name'];
			$myfile =$_FILES['photo']['name'];
		 	$location="photos/".$myfile;


		if ( $errofile > 0) {
				message("No Image Selected!", "error");
				redirect("index.php?view=view&id=". $_GET['id']);
		}else{
	 
				@$file=$_FILES['photo']['tmp_name'];
				@$image= addslashes(file_get_contents($_FILES['photo']['tmp_name']));
				@$image_name= addslashes($_FILES['photo']['name']); 
				@$image_size= getimagesize($_FILES['photo']['tmp_name']);

			if ($image_size==FALSE ) {
				message("Uploaded file is not an image!", "error");
				redirect("index.php?view=view&id=". $_GET['id']);
			}else{
					//uploading the file
					move_uploaded_file($temp,"photos/" . $myfile);
		 	
					 

						$applicant = New Applicants();
						$applicant->APPLICANTPHOTO 			= $location;
						$applicant->update($_GET['id']);
						redirect(web_root."applicant/");
						 
							
					}
			}
			 
		}



function doAddFiles(){
	global $mydb;
	// `JOBID`, `FILE_NAME`, `FILE_LOCATION`, `USERATTACHMENTID`
	$picture = UploadImage();
	
	// Create applicant-specific directory if it doesn't exist
	$applicant_id = $_SESSION['APPLICANTID'];
	$applicant_dir = "photos/" . $applicant_id;
	if (!file_exists($applicant_dir)) {
		mkdir($applicant_dir, 0777, true);
	}
	
	// Move uploaded file to applicant-specific directory
	$location = $applicant_dir . "/" . $picture;

	$sql = "INSERT INTO `tblattachmentfile` (`JOBID`, `FILE_NAME`, `FILE_LOCATION`, `USERATTACHMENTID`) 
		VALUES ('".$_SESSION['APPLICANTID']."','','Resume','{$location}','".$_SESSION['APPLICANTID']."')";
	$mydb->setQuery($sql); 
	$res = $mydb->executeQuery();

	message("File has been uploaded!", "success");
	redirect("index.php?tab=files");
	 



} 

function UploadImage(){
	// Check if user is logged in as applicant
	if (!isset($_SESSION['APPLICANTID'])) {
        message("You must be logged in to upload files.", "error");
        redirect("index.php");
        return false;
    }
    
    $applicant_id = $_SESSION['APPLICANTID'];
    
    // Create base upload directory if it doesn't exist
    $base_dir = "photos/";
    if (!file_exists($base_dir)) {
        if (!mkdir($base_dir, 0777, true)) {
            message("Failed to create base directory.", "error");
            return false;
        }
    }
    
    // Create applicant-specific directory if it doesn't exist
    $applicant_dir = $base_dir . $applicant_id . "/";
    if (!file_exists($applicant_dir)) {
        if (!mkdir($applicant_dir, 0777, true)) {
            message("Failed to create applicant directory.", "error");
            return false;
        }
    }
    
    // Handle file upload
    if (!isset($_FILES['picture']) || !is_uploaded_file($_FILES['picture']['tmp_name'])) {
        message("No file was uploaded.", "error");
        return false;
    }
    
    $file = $_FILES['picture'];
    $filename = $file['name'];
    $filetmp = $file['tmp_name'];
    $filesize = $file['size'];
    $filetype = $file['type'];
    
    // Get file extension
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    // Generate unique filename
    $unique_filename = date('dmYhis') . "_" . uniqid() . "." . $ext;
    
    // Check file size - 10MB max
    if ($filesize > 10000000) {
        message("File size too large. Max 10MB allowed.", "error");
        return false;
    }
    
    // Check file type
    if ($ext != "pdf" && $ext != "doc" && $ext != "docx" && $ext != "txt") {
        message("Only PDF, DOC, DOCX and TXT files are allowed for resumes.", "error");
        return false;
    }
    
    // Move uploaded file to applicant-specific directory
    $target = $applicant_dir . $unique_filename;
    
    if (!move_uploaded_file($filetmp, $target)) {
        message("Error moving uploaded file.", "error");
        return false;
    }
    
    return $unique_filename;
}
 

?>