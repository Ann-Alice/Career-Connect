-- First check if the referenced tables exist and create them if they don't
CREATE TABLE IF NOT EXISTS `tblapplicants` (
    `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT,
    `FNAME` varchar(50) NOT NULL,
    `LNAME` varchar(50) NOT NULL,
    `MNAME` varchar(50) NOT NULL,
    `ADDRESS` varchar(100) NOT NULL,
    `SEX` varchar(20) NOT NULL,
    `CIVILSTATUS` varchar(20) NOT NULL,
    `BIRTHDATE` date NOT NULL,
    `BIRTHPLACE` varchar(50) NOT NULL,
    `AGE` int(3) NOT NULL,
    `USERNAME` varchar(50) NOT NULL,
    `PASS` varchar(50) NOT NULL,
    `CONTACTNO` varchar(30) NOT NULL,
    `EMAILADDRESS` varchar(50) NOT NULL,
    `DEGREE` varchar(50) NOT NULL,
    PRIMARY KEY (`APPLICANTID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbladmin` (
    `ADMINID` int(11) NOT NULL AUTO_INCREMENT,
    `FULLNAME` varchar(50) NOT NULL,
    `USERNAME` varchar(50) NOT NULL,
    `PASS` varchar(50) NOT NULL,
    PRIMARY KEY (`ADMINID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Now create the messages table
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