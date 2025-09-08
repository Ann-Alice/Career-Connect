-- Create interview reviews table for candidate feedback
CREATE TABLE IF NOT EXISTS `tblinterviewreviews` (
    `REVIEWID` int(11) NOT NULL AUTO_INCREMENT,
    `REGISTRATIONID` int(11) NOT NULL,
    `CANDIDATE_NAME` varchar(255) NOT NULL,
    `RATING` int(1) NOT NULL DEFAULT 5,
    `EXPERIENCE_RATING` int(1) NOT NULL DEFAULT 5,
    `DIFFICULTY_RATING` int(1) NOT NULL DEFAULT 3,
    `COMMENTS` text,
    `CREATED_AT` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`REVIEWID`),
    KEY `REGISTRATIONID` (`REGISTRATIONID`),
    CONSTRAINT `tblinterviewreviews_ibfk_1` FOREIGN KEY (`REGISTRATIONID`) REFERENCES `tbljobregistration` (`REGISTRATIONID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample review data (optional)
INSERT INTO `tblinterviewreviews` (`REGISTRATIONID`, `CANDIDATE_NAME`, `RATING`, `EXPERIENCE_RATING`, `DIFFICULTY_RATING`, `COMMENTS`) VALUES
(1, 'John Doe', 5, 5, 4, 'Great experience with the AI interview. The questions were relevant and the interface was user-friendly.'),
(2, 'Jane Smith', 4, 4, 3, 'The interview process was smooth and professional. Would recommend to others.'),
(3, 'Mike Johnson', 5, 5, 5, 'Excellent AI interview system. Very comprehensive and fair assessment.'); 