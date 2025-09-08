-- Add interview results column to job registration table if it doesn't exist
SET @dbname = 'erisdb';
SET @tablename = 'tbljobregistration';
SET @columnname = 'INTERVIEW_RESULTS';
SET @columntype = 'TEXT';

SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ),
    'SELECT "Column already exists"',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @columntype, ' NULL')
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create table for interview videos
CREATE TABLE IF NOT EXISTS tblinterviewvideos (
    VIDEOID INT PRIMARY KEY AUTO_INCREMENT,
    REGISTRATIONID INT NOT NULL,
    VIDEO_PATH VARCHAR(255) NOT NULL,
    CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (REGISTRATIONID) REFERENCES tbljobregistration(REGISTRATIONID)
);

-- Create interview invitations table
CREATE TABLE IF NOT EXISTS `tblinterviewinvitations` (
    `INVITATIONID` int(11) NOT NULL AUTO_INCREMENT,
    `REGISTRATIONID` int(11) NOT NULL,
    `APPLICANTID` int(11) NOT NULL,
    `JOBID` int(11) NOT NULL,
    `TOKEN` varchar(64) NOT NULL,
    `EXPIRY_DATE` datetime NOT NULL,
    `CREATED_AT` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`INVITATIONID`),
    UNIQUE KEY `TOKEN` (`TOKEN`),
    KEY `REGISTRATIONID` (`REGISTRATIONID`),
    KEY `APPLICANTID` (`APPLICANTID`),
    KEY `JOBID` (`JOBID`),
    CONSTRAINT `tblinterviewinvitations_ibfk_1` FOREIGN KEY (`REGISTRATIONID`) REFERENCES `tbljobregistration` (`REGISTRATIONID`) ON DELETE CASCADE,
    CONSTRAINT `tblinterviewinvitations_ibfk_2` FOREIGN KEY (`APPLICANTID`) REFERENCES `tblapplicants` (`APPLICANTID`) ON DELETE CASCADE,
    CONSTRAINT `tblinterviewinvitations_ibfk_3` FOREIGN KEY (`JOBID`) REFERENCES `tbljob` (`JOBID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create interview recordings table
CREATE TABLE IF NOT EXISTS `tblinterviewrecordings` (
    `RECORDINGID` int(11) NOT NULL AUTO_INCREMENT,
    `REGISTRATIONID` int(11) NOT NULL,
    `QUESTION_NUMBER` int(11) NOT NULL,
    `FILE_PATH` varchar(255) NOT NULL,
    `DURATION` float NOT NULL,
    `RECORDED_AT` datetime NOT NULL,
    PRIMARY KEY (`RECORDINGID`),
    KEY `REGISTRATIONID` (`REGISTRATIONID`),
    CONSTRAINT `tblinterviewrecordings_ibfk_1` FOREIGN KEY (`REGISTRATIONID`) REFERENCES `tbljobregistration` (`REGISTRATIONID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add missing columns to tblinterviewrecordings table
ALTER TABLE `tblinterviewrecordings`
ADD COLUMN IF NOT EXISTS `CONVERSATION_TURN` int(11) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `QUESTION_TYPE` varchar(50) DEFAULT 'initial_answer';

-- Add interview status columns to job registration table
ALTER TABLE `tbljobregistration`
ADD COLUMN IF NOT EXISTS `INTERVIEW_STATUS` varchar(20) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `INTERVIEW_COMPLETED_AT` datetime DEFAULT NULL;

-- Create messages table if it doesn't exist
CREATE TABLE IF NOT EXISTS `tblmessages` (
    `MESSAGEID` int(11) NOT NULL AUTO_INCREMENT,
    `RECEIVERID` int(11) NOT NULL,
    `SENDERID` int(11) NOT NULL,
    `SUBJECT` varchar(255) NOT NULL,
    `MESSAGE` text NOT NULL,
    `DATESENT` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `STATUS` varchar(20) NOT NULL DEFAULT 'Unread',
    PRIMARY KEY (`MESSAGEID`),
    KEY `RECEIVERID` (`RECEIVERID`),
    KEY `SENDERID` (`SENDERID`),
    CONSTRAINT `tblmessages_ibfk_1` FOREIGN KEY (`RECEIVERID`) REFERENCES `tblapplicants` (`APPLICANTID`) ON DELETE CASCADE,
    CONSTRAINT `tblmessages_ibfk_2` FOREIGN KEY (`SENDERID`) REFERENCES `tbladmin` (`ADMINID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key constraints
ALTER TABLE `tblmessages`
  ADD CONSTRAINT `tblmessages_ibfk_1` FOREIGN KEY (`RECEIVERID`) REFERENCES `tblapplicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblmessages_ibfk_2` FOREIGN KEY (`SENDERID`) REFERENCES `tbladmin` (`ADMINID`) ON DELETE CASCADE; 