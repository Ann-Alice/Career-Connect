<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

$applicant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

?>
<!DOCTYPE html>
<html>
<head>
    <title>PDF Diagnostic Tool</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }
        .diagnostic-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .status-good { color: #28a745; font-weight: bold; }
        .status-bad { color: #dc3545; font-weight: bold; }
        .status-warning { color: #ffc107; font-weight: bold; }
        .file-header { 
            font-family: monospace; 
            background: #f8f9fa; 
            padding: 10px; 
            border-radius: 4px; 
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="diagnostic-card">
            <h2><i class="fa fa-file-pdf-o"></i> PDF Diagnostic Tool</h2>
            
            <?php if ($applicant_id <= 0): ?>
                <div class="alert alert-danger">
                    <strong>Error:</strong> Invalid applicant ID. Please provide a valid applicant ID.
                </div>
                <p><a href="../dashboard.php" class="btn btn-primary">← Back to Dashboard</a></p>
            <?php else: ?>
                
                <?php
                // Get resume file from database
                $sql = "SELECT af.*, a.FNAME, a.LNAME FROM tblattachmentfile af 
                        INNER JOIN tblapplicants a ON af.USERATTACHMENTID = a.APPLICANTID
                        WHERE af.USERATTACHMENTID = $applicant_id AND af.FILE_NAME = 'Resume' AND a.APPLICANTID = $applicant_id
                        ORDER BY af.ID DESC 
                        LIMIT 1";

                $mydb->setQuery($sql);
                $attachment = $mydb->loadSingleResult();

                if (!$attachment): ?>
                    <div class="alert alert-warning">
                        <strong>No Resume Found:</strong> No resume file found for applicant ID: <?php echo $applicant_id; ?>
                    </div>
                    <p><a href="../dashboard.php" class="btn btn-primary">← Back to Dashboard</a></p>
                
                <?php else: ?>
                    
                    <div class="alert alert-info">
                        <strong>Diagnosing Resume for:</strong> <?php echo htmlspecialchars($attachment->FNAME . ' ' . $attachment->LNAME); ?>
                    </div>
                    
                    <?php
                    // Build file path
                    $file_location = $attachment->FILE_LOCATION;
                    $file_path = null;
                    
                    // Try different path strategies
                    $possible_paths = [
                        "../../applicant/" . $file_location,
                        "../../applicant/photos/" . $applicant_id . "/" . basename($file_location),
                        "../../applicant/photos/" . $applicant_id . "/" . $attachment->FILE_NAME
                    ];
                    
                    foreach ($possible_paths as $path) {
                        if (file_exists($path)) {
                            $file_path = $path;
                            break;
                        }
                    }
                    ?>
                    
                    <h3>📁 File Location Analysis</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td><strong>Database Location</strong></td>
                            <td><?php echo htmlspecialchars($file_location); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Resolved Path</strong></td>
                            <td><?php echo $file_path ? htmlspecialchars($file_path) : '<span class="status-bad">File not found</span>'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>File Exists</strong></td>
                            <td><?php echo $file_path && file_exists($file_path) ? '<span class="status-good">✅ Yes</span>' : '<span class="status-bad">❌ No</span>'; ?></td>
                        </tr>
                    </table>
                    
                    <?php if ($file_path && file_exists($file_path)): ?>
                        
                        <?php
                        $file_size = filesize($file_path);
                        $file_info = pathinfo($file_path);
                        $is_readable = is_readable($file_path);
                        ?>
                        
                        <h3>📊 File Properties</h3>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>File Size</strong></td>
                                <td><?php echo round($file_size / 1024, 2); ?> KB (<?php echo number_format($file_size); ?> bytes)</td>
                            </tr>
                            <tr>
                                <td><strong>File Extension</strong></td>
                                <td><?php echo strtoupper($file_info['extension']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Readable</strong></td>
                                <td><?php echo $is_readable ? '<span class="status-good">✅ Yes</span>' : '<span class="status-bad">❌ No</span>'; ?></td>
                            </tr>
                        </table>
                        
                        <?php if ($is_readable): ?>
                            
                            <?php
                            // Read file header
                            $file_handle = fopen($file_path, 'rb');
                            $first_32_bytes = fread($file_handle, 32);
                            fclose($file_handle);
                            
                            $mime_type = function_exists('mime_content_type') ? mime_content_type($file_path) : 'Unknown';
                            $is_pdf = substr($first_32_bytes, 0, 4) === '%PDF';
                            ?>
                            
                            <h3>🔍 File Content Analysis</h3>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Detected MIME Type</strong></td>
                                    <td><?php echo htmlspecialchars($mime_type); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>File Header (Hex)</strong></td>
                                    <td class="file-header"><?php echo bin2hex(substr($first_32_bytes, 0, 16)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>File Header (Text)</strong></td>
                                    <td class="file-header"><?php echo htmlspecialchars(substr($first_32_bytes, 0, 16)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Valid PDF Header</strong></td>
                                    <td><?php echo $is_pdf ? '<span class="status-good">✅ Yes (%PDF)</span>' : '<span class="status-bad">❌ No</span>'; ?></td>
                                </tr>
                            </table>
                            
                            <?php if (!$is_pdf && strtolower($file_info['extension']) === 'pdf'): ?>
                                <div class="alert alert-danger">
                                    <h4><i class="fa fa-exclamation-triangle"></i> PDF Corruption Detected!</h4>
                                    <p><strong>Problem:</strong> This file has a .pdf extension but does not contain valid PDF data.</p>
                                    <p><strong>Solution:</strong> The file needs to be re-uploaded or replaced with a valid PDF.</p>
                                </div>
                            <?php endif; ?>
                            
                            <h3>🧪 Download Tests</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="download-resume.php?id=<?php echo $applicant_id; ?>" class="btn btn-primary btn-block" target="_blank">
                                        <i class="fa fa-download"></i> Test Download (Fixed)
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="<?php echo web_root; ?>applicant/<?php echo htmlspecialchars($attachment->FILE_LOCATION); ?>" class="btn btn-secondary btn-block" target="_blank">
                                        <i class="fa fa-external-link"></i> Direct File Access
                                    </a>
                                </div>
                            </div>
                            
                        <?php endif; ?>
                        
                    <?php else: ?>
                        
                        <div class="alert alert-danger">
                            <h4><i class="fa fa-exclamation-triangle"></i> File Not Found!</h4>
                            <p><strong>Problem:</strong> The resume file referenced in the database cannot be found on the server.</p>
                            <p><strong>Checked Locations:</strong></p>
                            <ul>
                                <?php foreach ($possible_paths as $path): ?>
                                    <li><code><?php echo htmlspecialchars($path); ?></code></li>
                                <?php endforeach; ?>
                            </ul>
                            <p><strong>Solution:</strong> The file needs to be re-uploaded or the database record needs to be corrected.</p>
                        </div>
                        
                    <?php endif; ?>
                    
                <?php endif; ?>
                
                <hr>
                <div class="text-center">
                    <a href="../dashboard.php" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="resume-tools.html" class="btn btn-info">
                        <i class="fa fa-wrench"></i> Resume Tools
                    </a>
                </div>
                
            <?php endif; ?>
            
        </div>
    </div>
</body>
</html>