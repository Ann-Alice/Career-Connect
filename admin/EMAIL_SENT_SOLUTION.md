# EMAIL_SENT Constraint Error - Solution Summary

## Problem Description
Fatal error: `CONSTRAINT 'tbljobregistration.EMAIL_SENT' failed for 'erisdb'.'tbljobregistration'`

This error occurs when the interview results page tries to update the EMAIL_SENT and EMAIL_SENT_AT columns in the tbljobregistration table, but these columns don't exist in the database.

## Root Cause
The original database schema (erisdb.sql) doesn't include the EMAIL_SENT and EMAIL_SENT_AT columns that are required by the interview results functionality.

## Solution Steps

### Step 1: Run Database Fix
Navigate to: http://localhost/eris/admin/fix_email_constraint.php

This will:
- Check if the required columns exist
- Add missing columns automatically
- Test the email functionality
- Provide detailed diagnostics

### Step 2: Alternative Manual Fix
If the automated fix doesn't work, run this SQL manually:

```sql
USE erisdb;

-- Add EMAIL_SENT column
ALTER TABLE tbljobregistration ADD COLUMN EMAIL_SENT LONGTEXT NULL;

-- Add EMAIL_SENT_AT column  
ALTER TABLE tbljobregistration ADD COLUMN EMAIL_SENT_AT DATETIME NULL;

-- Add ADMIN_GRADE column (also used by the system)
ALTER TABLE tbljobregistration ADD COLUMN ADMIN_GRADE LONGTEXT NULL;

-- Add GRADED_AT column
ALTER TABLE tbljobregistration ADD COLUMN GRADED_AT DATETIME NULL;
```

### Step 3: Verify Fix
1. Go to the interview results page: http://localhost/eris/admin/interview-results.php
2. Try to send an email to a candidate
3. The error should be resolved

## Files Modified
- `interview-results.php` - Added better error handling and SQL escaping
- `fix_email_constraint.php` - Diagnostic and fix script
- `fix_email_sent_constraint.sql` - Manual SQL fix script

## Technical Details
- Used LONGTEXT instead of TEXT to handle larger JSON data
- Added proper SQL escaping for JSON data
- Included error handling and column existence checks
- Added detailed logging for debugging

## Prevention
Consider running the full database schema update from:
- `interview-system/sql/update_schema.sql` 
- `erisdb_optimized.sql`

These contain all the necessary columns for the full interview system functionality.