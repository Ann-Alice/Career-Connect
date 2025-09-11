<?php
/**
 * Fetch Video Recordings
 * Retrieves video file paths from database and outputs playable <video> tags
 */

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');

try {
    // Database connection - adjust path as needed
    require_once('include/initialize.php');
    
    // Get registration ID from query parameter
    $registration_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($registration_id <= 0) {
        throw new Exception('Invalid registration ID');
    }
    
    /**
     * Get video MIME type based on file extension
     */
    function getVideoMimeType($file_path) {
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        $mime_types = [
            'mp4'  => 'video/mp4',
            'webm' => 'video/webm',
            'avi'  => 'video/x-msvideo',
            'mov'  => 'video/quicktime'
        ];
        
        return $mime_types[$extension] ?? 'video/mp4';
    }
    
    // Look up recordings in the database
    $sql = "SELECT RECORDING_ID, FILE_PATH, DURATION, RECORDED_AT 
            FROM tblinterviewrecordings 
            WHERE REGISTRATIONID = ? 
            ORDER BY RECORDED_AT ASC";
    
    $mydb->setQuery($sql);
    $mydb->bind_param('i', $registration_id);
    $recordings = $mydb->loadResultList();
    
    if (empty($recordings)) {
        echo "<p>No recordings found for this registration ID.</p>";
        exit;
    }
    
    // Output video tags for each recording
    foreach ($recordings as $recording) {
        // Check if file exists
        if (!file_exists($recording->FILE_PATH)) {
            echo "<p>Video file not found: " . htmlspecialchars($recording->FILE_PATH) . "</p>";
            continue;
        }
        
        // Get MIME type
        $mime_type = getVideoMimeType($recording->FILE_PATH);
        
        // Output video tag
        echo '<div style="margin-bottom: 20px;">';
        echo '<h4>Recording #' . $recording->RECORDING_ID . ' (Duration: ' . $recording->DURATION . 's)</h4>';
        echo '<video width="100%" height="400" controls style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: #000;">';
        echo '<source src="' . htmlspecialchars($recording->FILE_PATH) . '" type="' . $mime_type . '">';
        echo 'Your browser does not support the video tag.';
        echo '</video>';
        echo '</div>';
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>