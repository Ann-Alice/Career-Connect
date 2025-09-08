-- Fix for WORKSTATS column not having a default value
ALTER TABLE `tblemployees` MODIFY `WORKSTATS` VARCHAR(90) NOT NULL DEFAULT 'Active';