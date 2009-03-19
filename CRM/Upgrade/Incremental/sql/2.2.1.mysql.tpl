-- CRM-4257

UPDATE `civicrm_option_value` SET `description`= 'CRM_Event_Page_ParticipantListing_Name' WHERE `name` = 'Name Only';
UPDATE `civicrm_option_value` SET `description`= 'CRM_Event_Page_ParticipantListing_NameAndEmail' WHERE `name` = 'Name and Email';

SELECT @og_id_pl   := max(id) from civicrm_option_group where name = 'participant_listing';
SELECT @max_val_pl := MAX(ROUND(op.value)) FROM civicrm_option_value op WHERE op.option_group_id  = @og_id_pl;
SELECT @max_wt_pl  := MAX(op.weight) FROM civicrm_option_value op WHERE op.option_group_id = @og_id_pl;


INSERT INTO `civicrm_option_value`  
(`option_group_id`, `label`, `value`, `name`, `filter`, `weight`, `description`, `is_reserved`, `is_active`) VALUES                 
(@og_id_pl, 'Name, Status and Register Date', @max_val_pl + 1, 'Name, Status and Register Date', 0, @max_wt_pl+1, 'CRM_Event_Page_ParticipantListing_NameStatusAndDate', 1, 1);