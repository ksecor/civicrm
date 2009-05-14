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



-- CRM-3140
ALTER TABLE `civicrm_mapping_field`
  ADD `im_provider_id` int(10) unsigned default NULL COMMENT 'Which type of IM Provider does this name belong' AFTER `phone_type_id`; 



-- migrate participant status types, CRM-4321
-- FIXME for multilingual

BEGIN;

  SELECT @ps_ogid := id FROM civicrm_option_group WHERE name = 'participant_status';

  INSERT INTO civicrm_participant_status_type (id,    name, label, is_reserved, is_active, is_counted, weight, visibility_id)
    SELECT                                     value, name, label, is_reserved, is_active, filter,     weight, visibility_id
    FROM civicrm_option_value WHERE option_group_id = @ps_ogid;

  UPDATE civicrm_participant_status_type SET class = 'Positive' WHERE name IN ('Registered', 'Attended');
  UPDATE civicrm_participant_status_type SET class = 'Negative' WHERE name IN ('No-show', 'Cancelled');
  UPDATE civicrm_participant_status_type SET class = 'Pending'  WHERE name IN ('Pending');

  UPDATE civicrm_participant_status_type SET name = 'Pending from pay later', label = 'Pending from pay later' WHERE name = 'Pending';

  INSERT INTO civicrm_participant_status_type
    (name,                    label,                                         class,      is_reserved, is_active, is_counted, weight, visibility_id) VALUES
    ('On waitlist',           '{ts escape="sql"}On waitlist{/ts}',           'Waiting',  1,           1,         0,          6,      2            ),
    ('Awaiting approval',     '{ts escape="sql"}Awaiting approval{/ts}',     'Waiting',  1,           1,         1,          7,      2            ),
    ('Pending from waitlist', '{ts escape="sql"}Pending from waitlist{/ts}', 'Pending',  1,           1,         1,          8,      2            ),
    ('Pending from approval', '{ts escape="sql"}Pending from approval{/ts}', 'Pending',  1,           1,         1,          9,      2            ),
    ('Rejected',              '{ts escape="sql"}Rejected{/ts}',              'Negative', 1,           1,         0,          10,     2            ),
    ('Expired',               '{ts escape="sql"}Expired{/ts}',               'Negative', 1,           1,         0,          11,     2            );

  DELETE FROM civicrm_option_value WHERE option_group_id = @ps_ogid;
  DELETE FROM civicrm_option_group WHERE              id = @ps_ogid;

  ALTER TABLE civicrm_participant MODIFY status_id COMMENT 'Participant status ID. FK to civicrm_participant_status_type. Default of 1 should map to status = Registered.';
  ALTER TABLE civicrm_participant ADD CONSTRAINT FK_civicrm_participant_status_id FOREIGN KEY (status_id) REFERENCES civicrm_participant_status_type (id);

COMMIT;



-- add a profile for CRM-4323
-- FIXME for multilingual

BEGIN;

  INSERT INTO civicrm_uf_group
    (name,                 group_type,    title,                                      is_reserved) VALUES
    ('participant_status', 'Participant', '{ts escape="sql"}Participant Status{/ts}', 1);

  SELECT @ufgid := id FROM civicrm_uf_group WHERE name = 'participant_status';

  INSERT INTO civicrm_uf_field
    (uf_group_id, field_name,              is_required, is_reserved, label,                                      field_type) VALUES
    (@ufgid,      'participant_status_id', 1,           1,           '{ts escape="sql"}Participant Status{/ts}', 'Participant');

COMMIT;

-- CRM-4407
ALTER TABLE `civicrm_preferences` ADD `navigation` TEXT NULL AFTER `mailing_backend` ;


-CRM-3553
-- Activity Type for bulk email

SELECT @option_group_id_activity_type        := max(id) from civicrm_option_group where name = 'activity_type';
SELECT @max_val := MAX(ROUND(op.value)) FROM civicrm_option_value op WHERE op.option_group_id  = @option_group_id_activity_type;
-- FIXME for multilingual

{if $multilingual}
  INSERT INTO civicrm_option_value
    (option_group_id,                {foreach from=$locales item=locale}label_{$locale}, description_{$locale},{/foreach}      value,                            name,           weight,                           filter,          component_id) VALUES
    (@option_group_id_activity_type, {foreach from=$locales item=locale}'Bulk Email',   'Bulk Email Sent.',    {/foreach}     (SELECT @max_val := @max_val+1),  'Bulk Email',    (SELECT @max_val := @max_val+1),  1,                NULL );

{else}
  INSERT INTO civicrm_option_value
    (option_group_id,                label,            description,          value,                           name,           weight,                           filter,                   component_id) VALUES
    (@option_group_id_activity_type, 'Bulk Email',     'Bulk Email Sent.',   (SELECT @max_val := @max_val+1), 'Bulk Email',   (SELECT @max_val := @max_val+1),  1,                         NULL );
    
{/if}

-- delete unnecessary activities
SELECT @bulkEmailID := op.value from civicrm_option_value op where op.name = 'Bulk Email' and op.option_group_id  = @option_group_id_activity_type;

UPDATE civicrm_activity ca
SET ca.activity_type_id = @bulkEmailID
WHERE ca.activity_type_id = 3
     AND ca.source_record_id IS NOT NULL
     AND ca.id NOT IN ( SELECT cca.activity_id FROM civicrm_case_activity cca );

-- CRM-4478

INSERT INTO 
   `civicrm_option_group` (`name`, `description`, `is_reserved`, `is_active`) 
VALUES 
   ('priority', 'Priority', 0, 1);

SELECT @og_id_pr  := id FROM civicrm_option_group WHERE name = 'priority';

 {if $multilingual}
    INSERT INTO civicrm_option_value
      (option_group_id, {foreach from=$locales item=locale}label_{$locale},{/foreach}  value, name, filter, weight, is_active) 
    VALUES
      (@og_id_pr, {foreach from=$locales item=locale}'Urgent',{/foreach} 1, 'Urgent', 0, 1, 1),
      (@og_id_pr, {foreach from=$locales item=locale}'Normal',{/foreach} 2, 'Normal', 0, 2, 1),
      (@og_id_pr, {foreach from=$locales item=locale}'Low',{/foreach} 3, 'Low', 0, 3, 1);
 {else}
    INSERT INTO `civicrm_option_value`  
      (`option_group_id`, `label`, `value`, `name`, `filter`, `weight`, `is_active`) 
    VALUES    
      (@og_id_pr, 'Urgent', 1, 'Urgent', 0, 1, 1),
      (@og_id_pr, 'Normal', 2, 'Normal', 0, 2, 1),
      (@og_id_pr, 'Low', 3, 'Low', 0, 3, 1);
 {/if}