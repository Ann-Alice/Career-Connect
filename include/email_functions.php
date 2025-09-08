<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/email_templates.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(ADMIN_EMAIL, ADMIN_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send a professional interview invitation email
 * 
 * @param string $to Recipient email address
 * @param string $candidateName Candidate's full name
 * @param string $positionTitle Job position title
 * @param string $interviewUrl Interview URL
 * @param string $expiryDate Expiration date
 * @param string $companyName Company name
 * @return bool True if email was sent successfully
 */
function sendInterviewInvitationEmail($to, $candidateName, $positionTitle, $interviewUrl, $expiryDate, $companyName) {
    $emailBody = generateInterviewInvitationEmail($candidateName, $positionTitle, $interviewUrl, $expiryDate, $companyName);
    $subject = "AI Interview Invitation - " . $positionTitle;
    
    return sendEmail($to, $subject, $emailBody);
}

/**
 * Send a professional interview result email
 * 
 * @param string $to Recipient email address
 * @param string $candidateName Candidate's full name
 * @param string $positionTitle Job position title
 * @param string $resultStatus Result status
 * @param string $message Personalized message
 * @param string $companyName Company name
 * @return bool True if email was sent successfully
 */
function sendInterviewResultEmail($to, $candidateName, $positionTitle, $resultStatus, $message, $companyName) {
    $emailBody = generateInterviewResultEmail($candidateName, $positionTitle, $resultStatus, $message, $companyName);
    $subject = "Interview Results - " . $positionTitle;
    
    return sendEmail($to, $subject, $emailBody);
}

/**
 * Send a professional notification email
 * 
 * @param string $to Recipient email address
 * @param string $candidateName Candidate's full name
 * @param string $subject Email subject
 * @param string $message Email message
 * @param string $companyName Company name
 * @return bool True if email was sent successfully
 */
function sendNotificationEmail($to, $candidateName, $subject, $message, $companyName) {
    $emailBody = generateNotificationEmail($candidateName, $subject, $message, $companyName);
    
    return sendEmail($to, $subject, $emailBody);
}
?>