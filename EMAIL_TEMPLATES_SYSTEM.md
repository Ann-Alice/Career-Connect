# 📧 Professional Email Template System for Eris AI Interview System

## Overview
This document describes the new professional email template system implemented for the Eris AI Interview System. All emails sent by the system now use consistent, branded templates that provide a more professional appearance and improved user experience.

## Features
- **Consistent Branding**: All emails use the same professional design and styling
- **Responsive Design**: Emails look good on all devices (desktop, tablet, mobile)
- **Branded Templates**: Professional color scheme and typography
- **Clear Visual Hierarchy**: Proper spacing and organization of content
- **Company Branding**: Automatic inclusion of company name in footer
- **System Identification**: Note indicating the email is from the ERIS system
- **Duplication Prevention**: Smart logic to prevent content duplication in emails

## Email Template Functions

### 1. Interview Invitation Email
```php
sendInterviewInvitationEmail($to, $candidateName, $positionTitle, $interviewUrl, $expiryDate, $companyName)
```
- Used when sending interview invitations to candidates
- Includes a prominent "Start Interview" button
- Displays interview expiration date
- Provides both button and direct link options

### 2. Interview Result Email
```php
sendInterviewResultEmail($to, $candidateName, $positionTitle, $resultStatus, $message, $companyName)
```
- Used for sending interview results to candidates
- Highlights the result status in a special content box
- Includes personalized message content
- **Smart duplication prevention** to avoid repeating candidate names and status

### 3. Generic Notification Email
```php
sendNotificationEmail($to, $candidateName, $subject, $message, $companyName)
```
- Used for general notifications and status updates
- Flexible for various types of messages

### 4. Base Template Function
```php
generateEmailTemplate($subject, $greeting, $content, $companyName, $additionalContent = '')
```
- Base function for creating custom email templates
- Can be used for specialized email types not covered by the above functions
- **Smart content filtering** to prevent duplication of greetings

## Implementation Files

### `include/email_templates.php`
Contains all the HTML template generation functions:
- `generateEmailTemplate()` - Base template function
- `generateInterviewInvitationEmail()` - Interview invitation template
- `generateInterviewResultEmail()` - Interview result template with duplication prevention
- `generateNotificationEmail()` - Generic notification template

### `include/email_functions.php`
Contains all the email sending functions:
- `sendEmail()` - Base email sending function (using PHPMailer)
- `sendInterviewInvitationEmail()` - Sends interview invitation with template
- `sendInterviewResultEmail()` - Sends interview result with template
- `sendNotificationEmail()` - Sends notification with template

## Usage Examples

### Sending an Interview Invitation
```php
// Get company name
$sql = "SELECT COMPANYNAME FROM tblcompany c 
        JOIN tbljob j ON c.COMPANYID = j.COMPANYID 
        WHERE j.JOBID = '{$application->JOBID}'";
$mydb->setQuery($sql);
$company = $mydb->loadSingleResult();
$companyName = $company ? $company->COMPANYNAME : 'Our Company';

// Send professional email template
if (sendInterviewInvitationEmail(
    $to, 
    $candidateName, 
    $positionTitle, 
    $interview_url, 
    $expiry_date, 
    $companyName
)) {
    // Success
} else {
    // Handle error
}
```

### Sending an Interview Result
```php
// Send professional email template
if (sendInterviewResultEmail(
    $to,
    $candidateName,
    $positionTitle,
    $resultStatus,
    $message,
    $companyName
)) {
    // Success
} else {
    // Handle error
}
```

## Template Design Elements

### Color Scheme
- **Header Gradient**: Purple to blue gradient for professional appearance
- **Content Area**: Clean white background
- **Accent Colors**: Blue for important information boxes
- **Footer**: Light gray background for subtle separation

### Typography
- **Font Family**: Segoe UI, Tahoma, Geneva, Verdana, sans-serif (system fonts for best compatibility)
- **Font Sizes**: 
  - Header: 24px
  - Content: 15px
  - Footer: 12px (system note)

### Layout Features
- **Centered Container**: 600px max-width for optimal readability
- **Shadow Box**: Subtle shadow for depth
- **Rounded Corners**: Modern appearance
- **Responsive Spacing**: Proper padding and margins

### Special Elements
- **Buttons**: Prominent call-to-action buttons with hover effects
- **Information Boxes**: Highlighted sections for important content
- **Code Highlighting**: For technical information display
- **Divider Lines**: Visual separation between sections

## Duplication Prevention Features

### Smart Content Filtering
- Automatically removes duplicated candidate names from message content
- Prevents result status from appearing twice when it's already in the message
- Filters out redundant greetings and sign-offs

### Content Analysis
- Compares message content with result status to avoid repetition
- Removes leading greetings that match the email greeting
- Ensures clean, professional formatting without duplication

## Testing the Templates

A test page is available at `admin/test_professional_email.php` which displays examples of all email templates:
- Interview invitation template
- Interview result template
- Generic notification template

Another test page at `admin/test_email_fix.php` specifically tests the duplication fix with various scenarios.

## Benefits

### For Candidates
- **Professional Appearance**: Emails look polished and company-branded
- **Clear Information**: Easy to understand content hierarchy
- **Mobile-Friendly**: Good experience on all devices
- **Actionable**: Clear buttons and links for next steps
- **No Duplication**: Clean, non-repetitive content

### For Administrators
- **Consistency**: All system emails have the same professional look
- **Branding**: Company name automatically included in all emails
- **Maintainability**: Centralized template system for easy updates
- **Extensibility**: Easy to add new template types

### For Developers
- **Modular Design**: Separate template generation from email sending
- **Reusable Components**: Base template can be extended for new use cases
- **Error Handling**: Proper error handling for email sending failures
- **Documentation**: Clear function documentation and examples

## Customization

To customize the email templates:
1. Modify the CSS styles in `generateEmailTemplate()` function in `include/email_templates.php`
2. Update the color scheme by changing the gradient values
3. Adjust typography by modifying font families and sizes
4. Add new template functions for specialized use cases

## Troubleshooting

### Emails Not Sending
1. Check SMTP configuration in `include/config.php`
2. Verify email function includes in your script
3. Check server error logs for PHPMailer errors

### Template Display Issues
1. Ensure HTML is properly escaped using `htmlspecialchars()`
2. Check that CSS styles are correctly formatted
3. Test emails in different email clients

### Missing Company Names
1. Verify database joins to retrieve company information
2. Provide default company name when database value is null

### Content Duplication
1. Check that the latest version of `include/email_templates.php` is being used
2. Verify that message content doesn't unnecessarily repeat information already in the result status
3. Test with `admin/test_email_fix.php` to ensure duplication prevention is working