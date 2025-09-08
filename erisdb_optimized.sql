-- =====================================================
-- CAREER CONNECT - Optimized Database Schema
-- =====================================================
-- This is an optimized version of the original database
-- with better structure, indexes, and modern MySQL features

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- Database Creation
-- =====================================================
CREATE DATABASE IF NOT EXISTS `erisdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `erisdb`;

-- =====================================================
-- Table: tblusers (Admin Users)
-- =====================================================
DROP TABLE IF EXISTS `tblusers`;
CREATE TABLE `tblusers` (
  `USERID` varchar(30) NOT NULL,
  `FULLNAME` varchar(100) NOT NULL,
  `USERNAME` varchar(90) NOT NULL,
  `PASS` varchar(255) NOT NULL COMMENT 'SHA1 hash',
  `ROLE` enum('Administrator','Employee','Manager') NOT NULL DEFAULT 'Employee',
  `PICLOCATION` varchar(255) DEFAULT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`USERID`),
  UNIQUE KEY `username_unique` (`USERNAME`),
  KEY `idx_role` (`ROLE`),
  KEY `idx_active` (`IS_ACTIVE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tblapplicants
-- =====================================================
DROP TABLE IF EXISTS `tblapplicants`;
CREATE TABLE `tblapplicants` (
  `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT,
  `FNAME` varchar(90) NOT NULL,
  `LNAME` varchar(90) NOT NULL,
  `MNAME` varchar(90) DEFAULT NULL,
  `ADDRESS` text NOT NULL,
  `SEX` enum('Male','Female','Other') NOT NULL,
  `CIVILSTATUS` enum('Single','Married','Divorced','Widowed','Separated') NOT NULL,
  `BIRTHDATE` date NOT NULL,
  `BIRTHPLACE` varchar(255) NOT NULL,
  `AGE` int(3) GENERATED ALWAYS AS (YEAR(CURDATE()) - YEAR(BIRTHDATE)) STORED,
  `USERNAME` varchar(90) NOT NULL,
  `PASS` varchar(255) NOT NULL COMMENT 'SHA1 hash',
  `EMAILADDRESS` varchar(100) NOT NULL,
  `CONTACTNO` varchar(20) NOT NULL,
  `DEGREE` text NOT NULL,
  `APPLICANTPHOTO` varchar(255) DEFAULT NULL,
  `NATIONALID` varchar(50) DEFAULT NULL,
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`APPLICANTID`),
  UNIQUE KEY `username_unique` (`USERNAME`),
  UNIQUE KEY `email_unique` (`EMAILADDRESS`),
  KEY `idx_name` (`FNAME`, `LNAME`),
  KEY `idx_active` (`IS_ACTIVE`),
  KEY `idx_birthdate` (`BIRTHDATE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tblcompany
-- =====================================================
DROP TABLE IF EXISTS `tblcompany`;
CREATE TABLE `tblcompany` (
  `COMPANYID` int(11) NOT NULL AUTO_INCREMENT,
  `COMPANYNAME` varchar(150) NOT NULL,
  `COMPANYADDRESS` text NOT NULL,
  `COMPANYCONTACTNO` varchar(30) NOT NULL,
  `COMPANYSTATUS` enum('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
  `COMPANYMISSION` text DEFAULT NULL,
  `COMPANY_EMAIL` varchar(100) DEFAULT NULL,
  `COMPANY_WEBSITE` varchar(255) DEFAULT NULL,
  `INDUSTRY` varchar(100) DEFAULT NULL,
  `EMPLOYEE_COUNT` int(11) DEFAULT NULL,
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`COMPANYID`),
  KEY `idx_name` (`COMPANYNAME`),
  KEY `idx_status` (`COMPANYSTATUS`),
  KEY `idx_active` (`IS_ACTIVE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tblcategory
-- =====================================================
DROP TABLE IF EXISTS `tblcategory`;
CREATE TABLE `tblcategory` (
  `CATEGORYID` int(11) NOT NULL AUTO_INCREMENT,
  `CATEGORY` varchar(250) NOT NULL,
  `DESCRIPTION` text DEFAULT NULL,
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CATEGORYID`),
  UNIQUE KEY `category_unique` (`CATEGORY`),
  KEY `idx_active` (`IS_ACTIVE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tblemployees
-- =====================================================
DROP TABLE IF EXISTS `tblemployees`;
CREATE TABLE `tblemployees` (
  `INCID` int(11) NOT NULL AUTO_INCREMENT,
  `EMPLOYEEID` varchar(30) NOT NULL,
  `FNAME` varchar(50) NOT NULL,
  `LNAME` varchar(50) NOT NULL,
  `MNAME` varchar(50) DEFAULT NULL,
  `ADDRESS` text NOT NULL,
  `BIRTHDATE` date NOT NULL,
  `BIRTHPLACE` varchar(100) NOT NULL,
  `AGE` int(3) GENERATED ALWAYS AS (YEAR(CURDATE()) - YEAR(BIRTHDATE)) STORED,
  `SEX` enum('Male','Female','Other') NOT NULL,
  `CIVILSTATUS` enum('Single','Married','Divorced','Widowed','Separated') NOT NULL,
  `TELNO` varchar(20) DEFAULT NULL,
  `EMP_EMAILADDRESS` varchar(100) NOT NULL,
  `CELLNO` varchar(20) NOT NULL,
  `POSITION` varchar(100) NOT NULL,
  `WORKSTATS` enum('Active','Inactive','Terminated','Resigned') NOT NULL DEFAULT 'Active',
  `EMPPHOTO` varchar(255) DEFAULT NULL,
  `EMPUSERNAME` varchar(90) NOT NULL,
  `EMPPASSWORD` varchar(255) NOT NULL COMMENT 'SHA1 hash',
  `DATEHIRED` date NOT NULL,
  `COMPANYID` int(11) NOT NULL,
  `SALARY` decimal(10,2) DEFAULT NULL,
  `DEPARTMENT` varchar(100) DEFAULT NULL,
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`INCID`),
  UNIQUE KEY `employeeid_unique` (`EMPLOYEEID`),
  UNIQUE KEY `username_unique` (`EMPUSERNAME`),
  UNIQUE KEY `email_unique` (`EMP_EMAILADDRESS`),
  KEY `idx_company` (`COMPANYID`),
  KEY `idx_position` (`POSITION`),
  KEY `idx_workstatus` (`WORKSTATS`),
  KEY `idx_active` (`IS_ACTIVE`),
  CONSTRAINT `fk_employee_company` FOREIGN KEY (`COMPANYID`) REFERENCES `tblcompany` (`COMPANYID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tbljob
-- =====================================================
DROP TABLE IF EXISTS `tbljob`;
CREATE TABLE `tbljob` (
  `JOBID` int(11) NOT NULL AUTO_INCREMENT,
  `COMPANYID` int(11) NOT NULL,
  `CATEGORYID` int(11) NOT NULL,
  `OCCUPATIONTITLE` varchar(150) NOT NULL,
  `REQ_NO_EMPLOYEES` int(11) NOT NULL DEFAULT 1,
  `SALARIES` decimal(10,2) NOT NULL,
  `SALARY_MIN` decimal(10,2) DEFAULT NULL,
  `SALARY_MAX` decimal(10,2) DEFAULT NULL,
  `DURATION_EMPLOYEMENT` varchar(100) NOT NULL,
  `QUALIFICATION_WORKEXPERIENCE` text NOT NULL,
  `JOBDESCRIPTION` text NOT NULL,
  `PREFEREDSEX` enum('Male','Female','Any') NOT NULL DEFAULT 'Any',
  `SECTOR_VACANCY` text DEFAULT NULL,
  `JOBSTATUS` enum('Open','Closed','On Hold','Filled') NOT NULL DEFAULT 'Open',
  `DATEPOSTED` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DEADLINE` date DEFAULT NULL,
  `LOCATION` varchar(255) DEFAULT NULL,
  `JOB_TYPE` enum('Full-time','Part-time','Contract','Internship','Remote') NOT NULL DEFAULT 'Full-time',
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`JOBID`),
  KEY `idx_company` (`COMPANYID`),
  KEY `idx_category` (`CATEGORYID`),
  KEY `idx_status` (`JOBSTATUS`),
  KEY `idx_posted` (`DATEPOSTED`),
  KEY `idx_active` (`IS_ACTIVE`),
  CONSTRAINT `fk_job_company` FOREIGN KEY (`COMPANYID`) REFERENCES `tblcompany` (`COMPANYID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_job_category` FOREIGN KEY (`CATEGORYID`) REFERENCES `tblcategory` (`CATEGORYID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tbljobregistration
-- =====================================================
DROP TABLE IF EXISTS `tbljobregistration`;
CREATE TABLE `tbljobregistration` (
  `REGISTRATIONID` int(11) NOT NULL AUTO_INCREMENT,
  `COMPANYID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `APPLICANT` varchar(150) NOT NULL,
  `REGISTRATIONDATE` date NOT NULL DEFAULT (CURDATE()),
  `REMARKS` text DEFAULT 'Pending',
  `STATUS` enum('Pending','Under Review','Shortlisted','Interviewed','Hired','Rejected') NOT NULL DEFAULT 'Pending',
  `FILEID` varchar(50) DEFAULT NULL,
  `PENDINGAPPLICATION` tinyint(1) NOT NULL DEFAULT 1,
  `HVIEW` tinyint(1) NOT NULL DEFAULT 1,
  `DATETIMEAPPROVED` datetime DEFAULT NULL,
  `INTERVIEW_DATE` datetime DEFAULT NULL,
  `INTERVIEW_NOTES` text DEFAULT NULL,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`REGISTRATIONID`),
  UNIQUE KEY `unique_application` (`JOBID`, `APPLICANTID`),
  KEY `idx_company` (`COMPANYID`),
  KEY `idx_job` (`JOBID`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_status` (`STATUS`),
  KEY `idx_date` (`REGISTRATIONDATE`),
  CONSTRAINT `fk_reg_company` FOREIGN KEY (`COMPANYID`) REFERENCES `tblcompany` (`COMPANYID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reg_job` FOREIGN KEY (`JOBID`) REFERENCES `tbljob` (`JOBID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reg_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tblapplicants` (`APPLICANTID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tblattachmentfile
-- =====================================================
DROP TABLE IF EXISTS `tblattachmentfile`;
CREATE TABLE `tblattachmentfile` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FILEID` varchar(50) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `FILE_NAME` varchar(255) NOT NULL,
  `FILE_LOCATION` varchar(500) NOT NULL,
  `FILE_SIZE` bigint(20) DEFAULT NULL,
  `FILE_TYPE` varchar(100) DEFAULT NULL,
  `USERATTACHMENTID` int(11) NOT NULL,
  `UPLOAD_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `fileid_unique` (`FILEID`),
  KEY `idx_job` (`JOBID`),
  KEY `idx_user` (`USERATTACHMENTID`),
  KEY `idx_active` (`IS_ACTIVE`),
  CONSTRAINT `fk_attachment_job` FOREIGN KEY (`JOBID`) REFERENCES `tbljob` (`JOBID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tblfeedback
-- =====================================================
DROP TABLE IF EXISTS `tblfeedback`;
CREATE TABLE `tblfeedback` (
  `FEEDBACKID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `REGISTRATIONID` int(11) NOT NULL,
  `FEEDBACK` text NOT NULL,
  `RATING` tinyint(1) DEFAULT NULL COMMENT '1-5 rating',
  `FEEDBACK_TYPE` enum('Application','Interview','General') NOT NULL DEFAULT 'General',
  `CREATED_BY` varchar(50) DEFAULT NULL,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`FEEDBACKID`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_registration` (`REGISTRATIONID`),
  KEY `idx_rating` (`RATING`),
  CONSTRAINT `fk_feedback_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tblapplicants` (`APPLICANTID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_feedback_registration` FOREIGN KEY (`REGISTRATIONID`) REFERENCES `tbljobregistration` (`REGISTRATIONID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: tblautonumbers
-- =====================================================
DROP TABLE IF EXISTS `tblautonumbers`;
CREATE TABLE `tblautonumbers` (
  `AUTOID` int(11) NOT NULL AUTO_INCREMENT,
  `AUTOSTART` varchar(30) NOT NULL,
  `AUTOEND` int(11) NOT NULL,
  `AUTOINC` int(11) NOT NULL DEFAULT 1,
  `AUTOKEY` varchar(30) NOT NULL,
  `PREFIX` varchar(10) DEFAULT NULL,
  `SUFFIX` varchar(10) DEFAULT NULL,
  `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AUTOID`),
  UNIQUE KEY `autokey_unique` (`AUTOKEY`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert Sample Data
-- =====================================================

-- Insert default admin user
INSERT INTO `tblusers` (`USERID`, `FULLNAME`, `USERNAME`, `PASS`, `ROLE`, `EMAIL`) VALUES
('ADMIN001', 'System Administrator', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator', 'admin@eris.com');

-- Insert default categories
INSERT INTO `tblcategory` (`CATEGORY`, `DESCRIPTION`) VALUES
('Technology', 'Information Technology and Software Development'),
('Engineering', 'Civil, Mechanical, Electrical Engineering'),
('Healthcare', 'Medical and Healthcare Services'),
('Finance', 'Banking, Accounting, and Financial Services'),
('Education', 'Teaching and Educational Services'),
('Sales & Marketing', 'Sales, Marketing, and Business Development'),
('Administration', 'Administrative and Office Management'),
('Manufacturing', 'Production and Manufacturing'),
('Customer Service', 'Customer Support and Service'),
('Research & Development', 'R&D and Innovation');

-- Insert sample company
INSERT INTO `tblcompany` (`COMPANYNAME`, `COMPANYADDRESS`, `COMPANYCONTACTNO`, `COMPANYSTATUS`, `COMPANYMISSION`, `INDUSTRY`) VALUES
('ERIS Corporation', '123 Main Street, City', '+1234567890', 'Active', 'To provide excellent employment services', 'Human Resources');

-- Insert sample job
INSERT INTO `tbljob` (`COMPANYID`, `CATEGORYID`, `OCCUPATIONTITLE`, `REQ_NO_EMPLOYEES`, `SALARIES`, `DURATION_EMPLOYEMENT`, `QUALIFICATION_WORKEXPERIENCE`, `JOBDESCRIPTION`, `PREFEREDSEX`, `JOBSTATUS`, `LOCATION`, `JOB_TYPE`) VALUES
(1, 1, 'Software Developer', 2, 50000.00, 'Permanent', '2+ years experience in PHP/MySQL', 'We are looking for experienced software developers', 'Any', 'Open', 'Remote', 'Full-time');

-- Insert sample applicant
INSERT INTO `tblapplicants` (`FNAME`, `LNAME`, `MNAME`, `ADDRESS`, `SEX`, `CIVILSTATUS`, `BIRTHDATE`, `BIRTHPLACE`, `USERNAME`, `PASS`, `EMAILADDRESS`, `CONTACTNO`, `DEGREE`) VALUES
('John', 'Doe', 'M', '123 Sample Street', 'Male', 'Single', '1990-01-01', 'Sample City', 'johndoe', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'john@example.com', '1234567890', 'BS Computer Science');

-- Insert sample employee
INSERT INTO `tblemployees` (`EMPLOYEEID`, `FNAME`, `LNAME`, `MNAME`, `ADDRESS`, `BIRTHDATE`, `BIRTHPLACE`, `SEX`, `CIVILSTATUS`, `EMP_EMAILADDRESS`, `CELLNO`, `POSITION`, `WORKSTATS`, `EMPUSERNAME`, `EMPPASSWORD`, `DATEHIRED`, `COMPANYID`, `SALARY`, `DEPARTMENT`) VALUES
('EMP001', 'Jane', 'Smith', 'A', '456 Employee Street', '1985-05-15', 'Employee City', 'Female', 'Married', 'jane@company.com', '0987654321', 'HR Manager', 'Active', 'janesmith', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', '2023-01-01', 1, 60000.00, 'Human Resources');

-- Insert sample job registration
INSERT INTO `tbljobregistration` (`COMPANYID`, `JOBID`, `APPLICANTID`, `APPLICANT`, `STATUS`) VALUES
(1, 1, 1, 'John Doe', 'Pending');

-- Insert sample attachment
INSERT INTO `tblattachmentfile` (`FILEID`, `JOBID`, `FILE_NAME`, `FILE_LOCATION`, `USERATTACHMENTID`) VALUES
('FILE001', 1, 'Resume.pdf', 'uploads/resumes/resume001.pdf', 1);

-- Insert sample feedback
INSERT INTO `tblfeedback` (`APPLICANTID`, `REGISTRATIONID`, `FEEDBACK`, `RATING`, `FEEDBACK_TYPE`) VALUES
(1, 1, 'Good candidate with relevant experience', 4, 'Application');

-- Insert sample autonumbers
INSERT INTO `tblautonumbers` (`AUTOSTART`, `AUTOEND`, `AUTOINC`, `AUTOKEY`, `PREFIX`, `SUFFIX`) VALUES
('00001', 1, 1, 'APPLICANT', 'APP', ''),
('00001', 1, 1, 'EMPLOYEE', 'EMP', ''),
('00001', 1, 1, 'JOB', 'JOB', ''),
('00001', 1, 1, 'FILE', 'FILE', '');

-- =====================================================
-- Create Views for Common Queries
-- =====================================================

-- View for active jobs with company info
CREATE OR REPLACE VIEW `vw_active_jobs` AS
SELECT 
    j.JOBID,
    j.OCCUPATIONTITLE,
    j.SALARIES,
    j.LOCATION,
    j.JOB_TYPE,
    j.DATEPOSTED,
    c.COMPANYNAME,
    cat.CATEGORY
FROM tbljob j
JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
JOIN tblcategory cat ON j.CATEGORYID = cat.CATEGORYID
WHERE j.JOBSTATUS = 'Open' AND j.IS_ACTIVE = 1;

-- View for applicant applications
CREATE OR REPLACE VIEW `vw_applicant_applications` AS
SELECT 
    jr.REGISTRATIONID,
    jr.STATUS,
    jr.REGISTRATIONDATE,
    j.OCCUPATIONTITLE,
    c.COMPANYNAME,
    j.SALARIES,
    j.LOCATION
FROM tbljobregistration jr
JOIN tbljob j ON jr.JOBID = j.JOBID
JOIN tblcompany c ON jr.COMPANYID = c.COMPANYID
WHERE jr.APPLICANTID = jr.APPLICANTID;

-- =====================================================
-- Create Stored Procedures
-- =====================================================

DELIMITER //

-- Procedure to get next auto number
CREATE PROCEDURE `GetNextAutoNumber`(IN p_autokey VARCHAR(30), OUT p_next_number VARCHAR(30))
BEGIN
    DECLARE v_start VARCHAR(30);
    DECLARE v_end INT;
    DECLARE v_inc INT;
    DECLARE v_prefix VARCHAR(10);
    DECLARE v_suffix VARCHAR(10);
    
    SELECT AUTOSTART, AUTOEND, AUTOINC, PREFIX, SUFFIX 
    INTO v_start, v_end, v_inc, v_prefix, v_suffix
    FROM tblautonumbers 
    WHERE AUTOKEY = p_autokey;
    
    SET p_next_number = CONCAT(
        COALESCE(v_prefix, ''),
        LPAD(v_start, 5, '0'),
        COALESCE(v_suffix, '')
    );
    
    UPDATE tblautonumbers 
    SET AUTOEND = AUTOEND + AUTOINC 
    WHERE AUTOKEY = p_autokey;
END //

-- Procedure to search jobs
CREATE PROCEDURE `SearchJobs`(
    IN p_keyword VARCHAR(255),
    IN p_category_id INT,
    IN p_location VARCHAR(255),
    IN p_salary_min DECIMAL(10,2),
    IN p_salary_max DECIMAL(10,2)
)
BEGIN
    SELECT 
        j.JOBID,
        j.OCCUPATIONTITLE,
        j.SALARIES,
        j.LOCATION,
        j.JOB_TYPE,
        j.DATEPOSTED,
        c.COMPANYNAME,
        cat.CATEGORY
    FROM tbljob j
    JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
    JOIN tblcategory cat ON j.CATEGORYID = cat.CATEGORYID
    WHERE j.JOBSTATUS = 'Open' 
        AND j.IS_ACTIVE = 1
        AND (p_keyword IS NULL OR 
             j.OCCUPATIONTITLE LIKE CONCAT('%', p_keyword, '%') OR
             j.JOBDESCRIPTION LIKE CONCAT('%', p_keyword, '%'))
        AND (p_category_id IS NULL OR j.CATEGORYID = p_category_id)
        AND (p_location IS NULL OR j.LOCATION LIKE CONCAT('%', p_location, '%'))
        AND (p_salary_min IS NULL OR j.SALARIES >= p_salary_min)
        AND (p_salary_max IS NULL OR j.SALARIES <= p_salary_max)
    ORDER BY j.DATEPOSTED DESC;
END //

DELIMITER ;

-- =====================================================
-- Create Triggers
-- =====================================================

-- Trigger to update age when birthdate changes
DELIMITER //
CREATE TRIGGER `tr_applicant_age_update` 
BEFORE UPDATE ON `tblapplicants`
FOR EACH ROW
BEGIN
    SET NEW.AGE = YEAR(CURDATE()) - YEAR(NEW.BIRTHDATE);
END //

CREATE TRIGGER `tr_employee_age_update` 
BEFORE UPDATE ON `tblemployees`
FOR EACH ROW
BEGIN
    SET NEW.AGE = YEAR(CURDATE()) - YEAR(NEW.BIRTHDATE);
END //
DELIMITER ;

-- =====================================================
-- Final Commit
-- =====================================================
COMMIT; 