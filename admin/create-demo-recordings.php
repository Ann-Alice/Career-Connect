<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

echo "<h2>Creating Demo Interview Recordings</h2>";

// Get all completed interviews
$sql = "SELECT r.REGISTRATIONID, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME
        FROM tbljobregistration r 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
        WHERE (r.INTERVIEW_STATUS = 'Completed' OR r.INTERVIEW_STATUS = 'completed')
        ORDER BY r.REGISTRATIONDATE DESC";

$mydb->setQuery($sql);
$interviews = $mydb->loadResultList();

if ($interviews) {
    $recording_dir = "../interview-recordings/";
    
    // Create directory if it doesn't exist
    if (!is_dir($recording_dir)) {
        mkdir($recording_dir, 0777, true);
        echo "<p>✅ Created recordings directory: $recording_dir</p>";
    }
    
    $created_count = 0;
    
    foreach ($interviews as $interview) {
        $recording_file = $recording_dir . "interview_{$interview->REGISTRATIONID}.mp4";
        
        if (!file_exists($recording_file)) {
            // Create demo recording content
            $demo_content = "DEMO INTERVIEW RECORDING\n";
            $demo_content .= "========================\n\n";
            $demo_content .= "Registration ID: {$interview->REGISTRATIONID}\n";
            $demo_content .= "Candidate: {$interview->FNAME} {$interview->LNAME}\n";
            $demo_content .= "Position: {$interview->OCCUPATIONTITLE}\n";
            $demo_content .= "Company: {$interview->COMPANYNAME}\n";
            $demo_content .= "Recording Date: " . date('Y-m-d H:i:s') . "\n\n";
            $demo_content .= "INTERVIEW TRANSCRIPT:\n";
            $demo_content .= "=====================\n\n";
            $demo_content .= "AI: Good morning, {$interview->FNAME}. Thank you for joining us today.\n";
            $demo_content .= "Candidate: Thank you for having me.\n\n";
            $demo_content .= "AI: Can you tell me about your experience with {$interview->OCCUPATIONTITLE}?\n";
            $demo_content .= "Candidate: I have extensive experience in this field...\n\n";
            $demo_content .= "AI: What are your strengths that make you suitable for this role?\n";
            $demo_content .= "Candidate: My key strengths include...\n\n";
            $demo_content .= "AI: Do you have any questions about the company or role?\n";
            $demo_content .= "Candidate: Yes, I'd like to know more about...\n\n";
            $demo_content .= "AI: Thank you for your time today. We'll be in touch soon.\n";
            $demo_content .= "Candidate: Thank you for the opportunity.\n\n";
            $demo_content .= "=== END OF INTERVIEW ===\n\n";
            $demo_content .= "Note: This is a demonstration file. In production, this would be\n";
            $demo_content .= "an actual video recording (.mp4) of the AI interview session.\n\n";
            $demo_content .= "File created: " . date('Y-m-d H:i:s') . "\n";
            $demo_content .= "Created by: Admin User (Demo Mode)\n";
            
            file_put_contents($recording_file, $demo_content);
            chmod($recording_file, 0644);
            
            echo "<p>✅ Created: interview_{$interview->REGISTRATIONID}.mp4 for {$interview->FNAME} {$interview->LNAME}</p>";
            $created_count++;
        } else {
            echo "<p>⚪ Already exists: interview_{$interview->REGISTRATIONID}.mp4</p>";
        }
    }
    
    echo "<hr>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>✅ Demo Recording Creation Complete</h4>";
    echo "<p><strong>Total interviews found:</strong> " . count($interviews) . "</p>";
    echo "<p><strong>New recordings created:</strong> $created_count</p>";
    echo "<p><strong>Recording directory:</strong> $recording_dir</p>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h5>📝 What's Next?</h5>";
    echo "<ul>";
    echo "<li>Go back to <a href='interview-results.php'>Interview Results</a></li>";
    echo "<li>Try the 'Download Recording' and 'Watch Recording' buttons</li>";
    echo "<li>The demo files contain interview transcripts and details</li>";
    echo "<li>In production, these would be actual MP4 video files</li>";
    echo "</ul>";
    echo "</div>";
    
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>⚠️ No Completed Interviews Found</h4>";
    echo "<p>No interviews with 'Completed' status were found in the database.</p>";
    echo "<p>Make sure you have:</p>";
    echo "<ul>";
    echo "<li>Applicants in the system</li>";
    echo "<li>Job registrations with INTERVIEW_STATUS = 'Completed'</li>";
    echo "<li>AI interview results data</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='interview-results.php' class='btn btn-primary'>← Back to Interview Results</a></p>";
?>