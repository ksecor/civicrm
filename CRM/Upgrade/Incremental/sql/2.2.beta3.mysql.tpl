-- CRM-4167

ALTER TABLE `civicrm_event`
  ADD `allow_same_participant_emails` tinyint(4) default '0' COMMENT 'if true - allows the user to register multiple registrations from same email address.';  