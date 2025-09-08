<?php
require_once 'config.php';
require_once 'db.php';

$sessionId = $_GET['session_id'] ?? 0;
$sessionData = getInterviewSession($sessionId);

if (!$sessionData) {
    die("Invalid session ID");
}

// Generate PDF report
require_once 'vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();

$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Interview Assessment Report</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .section { 
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .score { 
            font-size: 24px; 
            font-weight: bold;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }
        .passed { 
            color: #28a745;
            background: #d4edda;
        }
        .failed { 
            color: #dc3545;
            background: #f8d7da;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Interview Assessment Report</h1>
        <h2>'.$sessionData['candidate_name'].'</h2>
        <p>Position ID: '.$sessionData['position_id'].'</p>
        <p>Date: '.date('Y-m-d H:i:s', $sessionData['created_at']).'</p>
    </div>
    
    <div class="section">
        <h3>Assessment Summary</h3>
        <div class="score '.($sessionData['passed'] ? 'passed' : 'failed').'">
            Overall Score: '.round($sessionData['overall_score']).'/100
        </div>
        <p>Result: <strong>'.($sessionData['passed'] ? 'PASSED' : 'FAILED').'</strong></p>
    </div>
    
    <div class="section">
        <h3>Detailed Breakdown</h3>
        <table>
            <tr>
                <th>Metric</th>
                <th>Score</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Facial Expression Analysis</td>
                <td>'.round($sessionData['expression_score']).'/100</td>
                <td>Measures positive vs negative expressions during answers</td>
            </tr>
            <tr>
                <td>Movement Analysis</td>
                <td>'.round($sessionData['movement_score']).'/100</td>
                <td>Evaluates excessive movement or fidgeting</td>
            </tr>
            <tr>
                <td>Answer Accuracy</td>
                <td>'.round($sessionData['accuracy_score']).'/100</td>
                <td>Assesses correctness and completeness of answers</td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <h3>Video Recording</h3>
        <p>The full interview recording is available at: '.$sessionData['video_path'].'</p>
    </div>
    
    <div class="section">
        <h3>Recommendations</h3>
        <ul>
            <li>Review the video recording for detailed behavioral analysis</li>
            <li>Consider scheduling a follow-up interview if needed</li>
            <li>Share feedback with the candidate within 48 hours</li>
        </ul>
    </div>
</body>
</html>
';

$mpdf->WriteHTML($html);
$reportPath = REPORT_DIR . 'report_' . $sessionId . '.pdf';
$mpdf->Output($reportPath, 'F');

// Send email to company
sendReportToCompany($sessionData, $reportPath);

// Display to user
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="report.pdf"');
readfile($reportPath);

/**
 * Send report to company
 */
function sendReportToCompany($sessionData, $reportPath) {
    $to = 'hiring@company.com';
    $subject = 'Interview Assessment Report: ' . $sessionData['candidate_name'];
    $message = '
    <html>
    <body>
        <h2>Interview Assessment Report</h2>
        <p>Candidate: '.$sessionData['candidate_name'].'</p>
        <p>Overall Score: '.round($sessionData['overall_score']).'/100</p>
        <p>Result: '.($sessionData['passed'] ? 'PASSED' : 'FAILED').'</p>
        <p>The detailed report is attached.</p>
    </body>
    </html>
    ';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: interviewsystem@yourdomain.com\r\n";
    
    $boundary = uniqid();
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
    
    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $message."\r\n\r\n";
    
    // Attach PDF
    $fileContent = file_get_contents($reportPath);
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: application/pdf\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"report_".$sessionData['id'].".pdf\"\r\n\r\n";
    $body .= chunk_split(base64_encode($fileContent))."\r\n";
    
    $body .= "--$boundary--";
    
    mail($to, $subject, $body, $headers);
}
?> 