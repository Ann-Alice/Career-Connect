<?php
/**
 * Professional Email Template System for Eris AI Interview System
 * Provides consistent, branded email templates for all system communications
 */

/**
 * Generate a professional email template
 * 
 * @param string $subject The email subject
 * @param string $greeting The greeting line (e.g., "Dear John Doe")
 * @param string $content The main email content
 * @param string $companyName The company name for the footer
 * @param string $additionalContent Optional additional content section
 * @return string Formatted HTML email template
 */
function generateEmailTemplate($subject, $greeting, $content, $companyName, $additionalContent = '') {
    $html = "
    <html>
    <head>
        <title>" . htmlspecialchars($subject) . "</title>
        <style>
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0; 
                background-color: #f8f9fa; 
            }
            .email-container { 
                max-width: 600px; 
                margin: 20px auto; 
                background: white; 
                border-radius: 10px; 
                overflow: hidden; 
                box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            }
            .header { 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                padding: 30px 20px; 
                text-align: center; 
            }
            .header h1 { 
                color: white; 
                margin: 0; 
                font-size: 24px; 
                font-weight: 600; 
            }
            .content { 
                padding: 30px 25px; 
            }
            .greeting { 
                font-size: 16px; 
                margin-bottom: 20px; 
                color: #2c3e50; 
            }
            .main-content { 
                margin: 25px 0; 
                font-size: 15px; 
                line-height: 1.7; 
                color: #495057; 
            }
            .additional-content { 
                background: #e9f7fe; 
                padding: 20px; 
                border-left: 4px solid #3498db; 
                margin: 25px 0; 
                border-radius: 5px; 
            }
            .additional-content h3 { 
                margin-top: 0; 
                color: #2980b9; 
            }
            .footer { 
                background: #f8f9fa; 
                padding: 25px; 
                text-align: center; 
                border-top: 1px solid #e9ecef; 
            }
            .footer p { 
                margin: 5px 0; 
                color: #6c757d; 
            }
            .company-name { 
                font-weight: 600; 
                color: #495057; 
            }
            .system-note { 
                font-size: 12px; 
                margin-top: 15px; 
                color: #868e96; 
            }
            .button {
                display: inline-block;
                padding: 12px 24px;
                background: #667eea;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: 600;
                margin: 15px 0;
            }
            .button:hover {
                background: #5a6fd8;
            }
            .highlight {
                background-color: #fff8e1;
                padding: 3px 5px;
                border-radius: 3px;
                font-weight: 500;
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>" . htmlspecialchars($subject) . "</h1>
            </div>
            <div class='content'>
                <p class='greeting'><strong>" . htmlspecialchars($greeting) . "</strong></p>";
    
    // Only add main content if it's not empty and doesn't duplicate the greeting
    $cleanContent = trim($content);
    if (!empty($cleanContent)) {
        // Check if content is just the greeting repeated
        $greetingWithoutPunctuation = preg_replace('/[.,:;!?]+$/', '', trim($greeting));
        $contentWithoutGreeting = preg_replace('/^' . preg_quote($greetingWithoutPunctuation, '/') . '[.,:;!?]*\s*/i', '', $cleanContent);
        
        // Only add content if it's different from just the greeting
        if (trim($contentWithoutGreeting) !== '') {
            $html .= "
                <div class='main-content'>
                    " . nl2br(htmlspecialchars($contentWithoutGreeting)) . "
                </div>";
        }
    }
    
    if (!empty($additionalContent)) {
        $html .= "
                <div class='additional-content'>
                    " . $additionalContent . "
                </div>";
    }
    
    $html .= "
            </div>
            <div class='footer'>
                <p><strong>Best regards,</strong></p>
                <p class='company-name'>" . htmlspecialchars($companyName) . " HR Team</p>
                <p class='system-note'>This is an automated message from the ERIS Interview System.</p>
            </div>
        </div>
    </body>
    </html>";
    
    return $html;
}

/**
 * Generate an interview invitation email template
 * 
 * @param string $candidateName The candidate's full name
 * @param string $positionTitle The job position title
 * @param string $interviewUrl The URL for the interview
 * @param string $expiryDate The expiration date for the invitation
 * @param string $companyName The company name
 * @return string Formatted HTML email template
 */
function generateInterviewInvitationEmail($candidateName, $positionTitle, $interviewUrl, $expiryDate, $companyName) {
    $subject = "AI Interview Invitation - " . $positionTitle;
    $greeting = "Dear " . $candidateName . ",";
    $content = "Congratulations! Your application for the position of " . $positionTitle . " has been approved.\n\n" .
               "You are invited to complete an AI-powered interview to further assess your candidacy.\n\n" .
               "Please click the button below to start your interview:";
    
    $additionalContent = "
        <p style='text-align: center;'>
            <a href='" . htmlspecialchars($interviewUrl) . "' class='button'>Start Interview</a>
        </p>
        <p style='text-align: center; font-size: 14px;'>
            Alternatively, copy and paste this link into your browser:<br>
            <span class='highlight'>" . htmlspecialchars($interviewUrl) . "</span>
        </p>
        <p><strong>Note:</strong> This invitation will expire on " . date('F d, Y', strtotime($expiryDate)) . ".</p>";
    
    return generateEmailTemplate($subject, $greeting, $content, $companyName, $additionalContent);
}

/**
 * Generate an interview result email template
 * 
 * @param string $candidateName The candidate's full name
 * @param string $positionTitle The job position title
 * @param string $resultStatus The result status (e.g., "Congratulations!", "Under Consideration")
 * @param string $message The personalized message
 * @param string $companyName The company name
 * @return string Formatted HTML email template
 */
function generateInterviewResultEmail($candidateName, $positionTitle, $resultStatus, $message, $companyName) {
    $subject = "Interview Results - " . $positionTitle;
    $greeting = "Dear " . $candidateName . ",";
    
    // Clean and validate input to prevent duplication
    $clean_result_status = trim($resultStatus);
    $clean_email_message = trim($message);
    
    // If message contains the candidate name and result status, we need to avoid duplication
    // Check if the message already contains the result status
    $content = '';
    if (!empty($clean_email_message)) {
        // Remove the candidate name from the beginning if it exists
        $greetingWithoutPunctuation = preg_replace('/[.,:;!?]+$/', '', trim($greeting));
        $contentWithoutGreeting = preg_replace('/^' . preg_quote($greetingWithoutPunctuation, '/') . '[.,:;!?]*\s*/i', '', $clean_email_message);
        
        // Check if the cleaned message contains the result status
        if (!empty($clean_result_status) && stripos($contentWithoutGreeting, $clean_result_status) !== false) {
            // If message already contains the result status, just use the message
            $content = $contentWithoutGreeting;
        } else {
            // Otherwise, use the message as is
            $content = $contentWithoutGreeting;
        }
    }
    
    $additionalContent = "";
    if (!empty($clean_result_status) && !in_array(strtolower($clean_result_status), ['select result...', '', 'test email subject'])) {
        $additionalContent = "
        <h3 style='margin-top: 0; color: #2980b9;'>" . htmlspecialchars($clean_result_status) . "</h3>";
    }
    
    return generateEmailTemplate($subject, $greeting, $content, $companyName, $additionalContent);
}

/**
 * Generate a generic notification email template
 * 
 * @param string $candidateName The candidate's full name
 * @param string $subject The email subject
 * @param string $message The email message
 * @param string $companyName The company name
 * @return string Formatted HTML email template
 */
function generateNotificationEmail($candidateName, $subject, $message, $companyName) {
    $greeting = "Dear " . $candidateName . ",";
    $content = $message;
    
    return generateEmailTemplate($subject, $greeting, $content, $companyName);
}

?>