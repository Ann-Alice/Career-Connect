-- Fix for EMAIL_SENT constraint error
-- This script adds the missing EMAIL_SENT and EMAIL_SENT_AT columns to tbljobregistration table

USE erisdb;

-- Add EMAIL_SENT column if it doesn't exist
SET @dbname = 'erisdb';
SET @tablename = 'tbljobregistration';
SET @columnname = 'EMAIL_SENT';
SET @columntype = 'TEXT NULL';

SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ),
    'SELECT "EMAIL_SENT column already exists" as result',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @columntype)
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add EMAIL_SENT_AT column if it doesn't exist
SET @columnname = 'EMAIL_SENT_AT';
SET @columntype = 'DATETIME NULL';

SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ),
    'SELECT "EMAIL_SENT_AT column already exists" as result',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @columntype)
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add ADMIN_GRADE column if it doesn't exist (also used in the code)
SET @columnname = 'ADMIN_GRADE';
SET @columntype = 'TEXT NULL';

SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ),
    'SELECT "ADMIN_GRADE column already exists" as result',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @columntype)
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add GRADED_AT column if it doesn't exist
SET @columnname = 'GRADED_AT';
SET @columntype = 'DATETIME NULL';

SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ),
    'SELECT "GRADED_AT column already exists" as result',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @columntype)
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Database structure updated successfully!' as result;