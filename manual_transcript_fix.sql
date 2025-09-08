-- Manual fix for TRANSCRIPT column error
-- Run this SQL in phpMyAdmin or MySQL command line

USE erisdb;

-- Check if the column exists first
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'erisdb' 
AND TABLE_NAME = 'tblinterviewrecordings' 
AND COLUMN_NAME = 'TRANSCRIPT';

-- Add the TRANSCRIPT column if it doesn't exist
ALTER TABLE `tblinterviewrecordings` 
ADD COLUMN IF NOT EXISTS `TRANSCRIPT` text AFTER `QUESTION_TYPE`;

-- Verify the column was added
DESCRIBE tblinterviewrecordings;

-- Test insert to verify the fix works
INSERT INTO tblinterviewrecordings 
(REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
VALUES 
('TEST_FIX', 1, 'test.webm', 5.0, NOW(), 1, 'test', 'This is a test transcript');

-- Clean up test record
DELETE FROM tblinterviewrecordings WHERE REGISTRATIONID = 'TEST_FIX';

SELECT 'TRANSCRIPT column fix completed successfully!' as result;