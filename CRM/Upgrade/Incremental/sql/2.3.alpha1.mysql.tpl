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
