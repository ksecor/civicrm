 -- CRM-5333
 -- Drop unique indexes of activity_target and activity_assignment
 
  ALTER TABLE  civicrm_activity_assignment 
  DROP INDEX `UI_activity_assignee_contact_id` ,
  ADD  INDEX `UI_activity_assignee_contact_id` (`assignee_contact_id`,`activity_id`);

  ALTER TABLE  civicrm_activity_target 
  DROP INDEX `UI_activity_target_contact_id` ,
  ADD INDEX `UI_activity_target_contact_id` (`target_contact_id`,`activity_id`);

