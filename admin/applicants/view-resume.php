<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

// Get applicant ID
$applicant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($applicant_id <= 0) {
    echo "<div style='text-align: center; padding: 50px;'>";
    echo "<h3>Invalid Applicant ID</h3>";
    echo "<p>The applicant ID is missing or invalid.</p>";
    echo "<button onclick='window.close()'>Close Window</button>";
    echo "</div>";
    exit;
}

// Get resume file from database - STRICT applicant-specific access only
$sql = "SELECT af.* FROM tblattachmentfile af 
        INNER JOIN tblapplicants a ON af.USERATTACHMENTID = a.APPLICANTID
        WHERE af.USERATTACHMENTID = $applicant_id AND af.FILE_NAME = 'Resume' AND a.APPLICANTID = $applicant_id
        ORDER BY af.ID DESC 
        LIMIT 1";

$mydb->setQuery($sql);
$attachment = $mydb->loadSingleResult();

if (!$attachment) {
    echo "<div style='text-align: center; padding: 50px;'>";
    echo "<h3>Resume Not Found</h3>";
    echo "<p>No resume file found for this specific applicant.</p>";
    echo "<p style='color: #666; font-size: 14px; margin-top: 20px;'>This ensures each applicant has their own unique resume file as per system specifications.</p>";
    echo "<p><a href='fix-resume-issues.php' style='color: #3498db;'>Run Resume Issues Diagnostic</a></p>";
    echo "<button onclick='window.close()' style='padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 15px;'>Close Window</button>";
    echo "</div>";
    exit;
}

// Build file path - handle both direct and photos subdirectory paths
$file_location = $attachment->FILE_LOCATION;

// If the path already includes photos/, use it as is
if (strpos($file_location, 'photos/') === 0) {
    $file_path = "../../applicant/" . $file_location;
} else {
    // Try to find the file in the applicant's directory
    $applicant_dir = "../../applicant/photos/" . $applicant_id . "/";
    $file_name = basename($file_location);
    $file_path = $applicant_dir . $file_name;
    
    // Check if file exists in applicant directory
    if (!file_exists($file_path)) {
        // Try common variations
        $file_variations = [
            $file_name,
            $attachment->FILE_NAME,
            "Resume".$file_extension
        ];
        
        foreach ($file_variations as $variation) {
            $variation_path = $applicant_dir . $variation;
            if (file_exists($variation_path)) {
                $file_path = $variation_path;
                // Update the database to include photos/ prefix
                $sql = "UPDATE tblattachmentfile SET FILE_LOCATION = 'photos/" . $applicant_id . "/" . $variation . "' WHERE ID = " . $attachment->ID;
                $mydb->setQuery($sql);
                $mydb->executeQuery();
                break;
            }
        }
    }
}

// Check if file exists
if (!file_exists($file_path)) {
    echo "<div style='text-align: center; padding: 50px;'>";
    echo "<h3>File Not Found</h3>";
    echo "<p>The resume file could not be found on the server.</p>";
    echo "<p><strong>Searched in:</strong> " . htmlspecialchars(dirname($file_path)) . "</p>";
    echo "<p style='color: #666; margin-top: 20px;'>The file may have been moved or deleted. Please contact the administrator or ask the applicant to re-upload their resume.</p>";
    echo "<button onclick='window.close()' style='padding: 10px 20px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 15px;'>Close Window</button>";
    echo "</div>";
    exit;
}

// Get file info
$file_info = pathinfo($file_path);
$file_extension = strtolower($file_info['extension']);

// Get applicant name for display
$applicant_sql = "SELECT FNAME, LNAME FROM tblapplicants WHERE APPLICANTID = $applicant_id";
$mydb->setQuery($applicant_sql);
$applicant = $mydb->loadSingleResult();

$applicant_name = $applicant ? $applicant->FNAME . ' ' . $applicant->LNAME : 'Unknown Applicant';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resume - <?php echo htmlspecialchars($applicant_name); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h3 {
            margin: 0;
            font-weight: 600;
        }
        .file-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .file-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        .file-info {
            margin-bottom: 30px;
        }
        .file-info h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .file-info p {
            color: #6c757d;
            margin: 5px 0;
        }
        .btn-group {
            margin-top: 20px;
        }
        .btn-action {
            padding: 12px 24px;
            margin: 0 5px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-download {
            background: #27ae60;
            color: white;
        }
        .btn-download:hover {
            background: #229954;
            color: white;
            text-decoration: none;
        }
        .btn-close {
            background: #e74c3c;
            color: white;
        }
        .btn-close:hover {
            background: #c0392b;
        }
        .preview-frame {
            width: 100%;
            height: 600px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3><i class="fa fa-file-text"></i> Resume Preview - <?php echo htmlspecialchars($applicant_name); ?></h3>
    </div>
    
    <div class="file-container">
        <?php if (in_array($file_extension, ['pdf'])): ?>
            <?php
            // Validate PDF file header
            $file_handle = fopen($file_path, 'rb');
            $first_bytes = fread($file_handle, 4);
            fclose($file_handle);
            $is_valid_pdf = ($first_bytes === '%PDF');
            ?>
            
            <?php if ($is_valid_pdf): ?>
                <iframe class="preview-frame" src="serve-pdf.php?id=<?php echo $applicant_id; ?>" type="application/pdf"></iframe>
                <div style="margin-top: 15px; color: #666; font-size: 12px;">
                    <p><i class="fa fa-info-circle"></i> If the PDF doesn't display properly, try downloading it instead.</p>
                </div>
            <?php else: ?>
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 30px; border-radius: 8px; margin: 20px 0; text-align: center;">
                    <i class="fa fa-exclamation-triangle" style="font-size: 3rem; color: #f39c12; margin-bottom: 15px;"></i>
                    <h4 style="color: #856404;">PDF File Corrupted</h4>
                    <p style="color: #856404; margin-bottom: 15px;">This PDF file appears to be corrupted and cannot be previewed.</p>
                    <a href="download-resume.php?id=<?php echo $applicant_id; ?>" class="btn" style="background: #f39c12; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                        <i class="fa fa-download"></i> Try Download (may still work)
                    </a>
                    <p style="margin-top: 15px; font-size: 12px; color: #856404;">
                        <a href="pdf-diagnostic.php?id=<?php echo $applicant_id; ?>" style="color: #856404;">Run Diagnostic Test</a>
                    </p>
                </div>
            <?php endif; ?>
        <?php elseif (in_array($file_extension, ['doc', 'docx', 'txt'])): ?>
            <div style="background: #f8f9fa; padding: 30px; border-radius: 8px; margin: 20px 0; text-align: center;">
                <i class="fa fa-file-o" style="font-size: 3rem; color: #3498db; margin-bottom: 15px;"></i>
                <p style="margin-bottom: 15px;">This file type cannot be previewed in the browser.</p>
                <a href="download-resume.php?id=<?php echo $applicant_id; ?>" class="btn" style="background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                    <i class="fa fa-external-link"></i> Open File in New Tab
                </a>
            </div>
        <?php else: ?>
            <div style="background: #f8f9fa; padding: 30px; border-radius: 8px; margin: 20px 0; text-align: center;">
                <img src="<?php echo web_root; ?>applicant/<?php echo htmlspecialchars($attachment->FILE_LOCATION); ?>" style="max-width: 100%; max-height: 600px; border: 1px solid #ddd; border-radius: 8px;" alt="Resume Image">
            </div>
        <?php endif; ?>
        
        <div class="btn-group">
            <a href="download-resume.php?id=<?php echo $applicant_id; ?>" class="btn-action btn-download">
                <i class="fa fa-download"></i> Download Resume
            </a>
            <button onclick="window.close()" class="btn-action btn-close">
                <i class="fa fa-times"></i> Close
            </button>
        </div>
    </div>
</body>
</html>

<?php
// Helper function to format file size
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>