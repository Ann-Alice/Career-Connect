-- Fix for employee table columns not having default values
ALTER TABLE `tblemployees` MODIFY `WORKSTATS` VARCHAR(90) NOT NULL DEFAULT 'Active';
ALTER TABLE `tblemployees` MODIFY `EMPPHOTO` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `tblemployees` MODIFY `CELLNO` VARCHAR(30) NOT NULL DEFAULT '';