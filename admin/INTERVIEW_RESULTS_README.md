# Interview Results Module

This module provides a comprehensive interface for reviewing AI-powered interview results, including video playback, AI analysis, admin grading, and email notifications.

## Features

### 1. Interview File Retrieval & Display
- Locates and fetches saved interview video files from the database or storage
- Provides in-browser video streaming with HTML5 video player
- Offers direct download option for interview recordings

### 2. AI Grading System Integration
- Displays AI-generated grading for each completed interview
- Analyzes and scores candidates based on:
  - Clarity of Speech
  - Body Language & Movement
  - Facial Expressions
  - Confidence & Engagement
  - Answer Quality & Completeness
- Shows overall performance score with visual indicators

### 3. Admin Manual Review & Comparison
- Provides a section for admins to watch recorded interview videos
- Allows admins to enter their own grading/remarks for comparison with AI grading
- Displays both AI and Admin grades side by side for better evaluation

### 4. Email Notification System
- Enables admins to send emails to candidates directly from the results page
- Uses standardized professional email templates
- Supports individual or bulk candidate selection for sending results/feedback

### 5. UI & Workflow Requirements
The Interview Results Page contains:
- Candidate details
- AI grading results
- Admin grading/remarks input section
- Interview video playback (watch/download option)
- Email sending panel

## Files

- `interview-results.php` - Main interview results page
- `interview-results.css` - Custom styling for the results page
- `init-interview-db.php` - Database initialization script
- `update-interview-schema.php` - Schema update script
- `test-interview-results.php` - Test script
- `test-interview-data.php` - Data verification script

## Database Requirements

The module requires the following columns in the `tbljobregistration` table:
- `INTERVIEW_RESULTS` (TEXT) - Stores AI analysis results in JSON format
- `INTERVIEW_STATUS` (VARCHAR) - Tracks interview completion status
- `INTERVIEW_COMPLETED_AT` (DATETIME) - Timestamp when interview was completed
- `ADMIN_GRADE` (TEXT) - Stores admin grading in JSON format
- `GRADED_AT` (DATETIME) - Timestamp when admin grading was completed
- `EMAIL_SENT` (TEXT) - Stores email sending history in JSON format
- `EMAIL_SENT_AT` (DATETIME) - Timestamp when email was sent

## Setup Instructions

1. Run `init-interview-db.php` to ensure all required tables and columns exist
2. Send interview invitations to candidates through the AI Interviews section
3. Have candidates complete their AI interviews
4. Access `interview-results.php` to view and review completed interviews

## Usage

1. **View Results**: Access the Interview Results page to see all completed interviews
2. **Watch Videos**: Use the video player to review candidate interviews
3. **Grade Interviews**: Enter admin grades and remarks in the grading section
4. **Send Emails**: Use the email panel to send results to candidates
5. **Compare Grades**: View both AI and admin grades side by side

## Technical Details

- Uses HTML5 video player for video streaming
- Implements range requests for efficient video streaming
- Provides professional email templates for candidate communication
- Stores all data in JSON format for flexibility
- Responsive design works on desktop and mobile devices

## Troubleshooting

If you encounter issues:
1. Ensure all required database columns exist by running `init-interview-db.php`
2. Check that video files exist in the expected locations
3. Verify email configuration in `include/email_functions.php`
4. Check PHP error logs for any technical issues