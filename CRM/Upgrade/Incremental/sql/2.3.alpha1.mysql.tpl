-- CRM-4048
--modify visibility of civicrm_group

ALTER TABLE `civicrm_group` 
     MODIFY `visibility` enum('User and User Admin Only','Public User Pages','Public User Pages and Listings', 'Public Pages') collate utf8_unicode_ci default 'User and User Admin Only' COMMENT 'In what context(s) is this field visible.';

UPDATE civicrm_group SET visibility = 'Public Pages' WHERE  visibility IN ('Public User Pages', 'Public User Pages and Listings');

ALTER TABLE `civicrm_group` 
  MODIFY `visibility` enum('User and User Admin Only', 'Public Pages') collate utf8_unicode_ci default 'User and User Admin Only' COMMENT 'In what context(s) is this field visible.';

--modify visibility of civicrm_uf_field

ALTER TABLE `civicrm_uf_field` 
     MODIFY `visibility` enum('User and User Admin Only','Public User Pages','Public User Pages and Listings', 'Public Pages', 'Public Pages and Listings') collate utf8_unicode_ci default 'User and User Admin Only' COMMENT 'In what context(s) is this field visible.';

UPDATE civicrm_uf_field SET visibility = 'Public Pages'              WHERE  visibility = 'Public User Pages';
UPDATE civicrm_uf_field SET visibility = 'Public Pages and Listings' WHERE  visibility = 'Public User Pages and Listings';

ALTER TABLE `civicrm_uf_field` 
     MODIFY `visibility` enum('User and User Admin Only', 'Public Pages', 'Public Pages and Listings') collate utf8_unicode_ci default 'User and User Admin Only' COMMENT 'In what context(s) is this field visible.';


--CRM-3336
--Add two label_a_b and label_b_a column in civicrm_relationship_type table 
--
ALTER TABLE `civicrm_relationship_type` ADD `label_a_b` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'label for relationship of contact_a to contact_b.' AFTER `name_a_b`, ADD `label_b_a` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'Optional label for relationship of contact_b to contact_a.' AFTER `name_b_a`;

--Copy value from name_a_b to label_a_b and name_b_a to label_b_a column in civicrm_relationship_type.
--
UPDATE civicrm_relationship_type SET  civicrm_relationship_type.label_a_b = civicrm_relationship_type.name_a_b, civicrm_relationship_type.label_b_a = civicrm_relationship_type.name_b_a;

--Alter comment of name_a_b and name_b_a column in civicrm_relationship_type table 
--
ALTER TABLE `civicrm_relationship_type` CHANGE `name_a_b` `name_a_b` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'name for relationship of contact_a to contact_b.' , CHANGE `name_b_a` `name_b_a` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Optional name for relationship of contact_b to contact_a.';

--
