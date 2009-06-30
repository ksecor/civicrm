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

-- /*******************************************************
-- *
-- * civicrm_participant_status_type
-- *
-- * various types of CiviEvent participant statuses
-- *
-- *******************************************************/
CREATE TABLE civicrm_participant_status_type (
     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'unique participant status type id',
     name varchar(64)    COMMENT 'non-localized name of the status type',
     label varchar(255)    COMMENT 'localized label for display of this status type',
     class enum('Positive', 'Pending', 'Waiting', 'Negative')    COMMENT 'the general group of status type this one belongs to',
     is_reserved tinyint    COMMENT 'whether this is a status type required by the system',
     is_active tinyint   DEFAULT 1 COMMENT 'whether this status type is active',
     is_counted tinyint    COMMENT 'whether this status type is counted against event size limit',
     weight int unsigned NOT NULL   COMMENT 'controls sort order',
     visibility_id int unsigned    COMMENT 'whether the status type is visible to the public, an implicit foreign key to option_value.value related to the `visibility` option_group' 
,
    PRIMARY KEY ( id )
 )  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

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
 
  ALTER TABLE `civicrm_participant` CHANGE `status_id` `status_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Participant status ID. FK to civicrm_participant_status_type. Default of 1 should map to status = Registered.';

  ALTER TABLE civicrm_participant ADD CONSTRAINT FK_civicrm_participant_status_id FOREIGN KEY (status_id) REFERENCES civicrm_participant_status_type (id);

COMMIT;

-- Add is_reserved, name column to civicrm_uf_group table.

ALTER TABLE `civicrm_uf_group` 
ADD `is_reserved` TINYINT( 4 ) NULL DEFAULT NULL COMMENT 'Is this group reserved for use by some other CiviCRM functionality?',
ADD `name` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'Name of the UF group for directly addressing it in the codebase';

-- Add is_reserved column to civicrm_uf_field.

ALTER TABLE `civicrm_uf_field` 
ADD `is_reserved` TINYINT( 4 ) NULL DEFAULT NULL COMMENT 'Is this group reserved for use by some other CiviCRM functionality?';

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


--CRM-3553
-- Activity Type for bulk email
--CRM-4480
-- Activity Type for case role assignment

SELECT @option_group_id_activity_type        := max(id) from civicrm_option_group where name = 'activity_type';
SELECT @max_val := MAX(ROUND(op.value)) FROM civicrm_option_value op WHERE op.option_group_id  = @option_group_id_activity_type;
SELECT @caseCompId := id FROM `civicrm_component` where `name` like 'CiviCase';
SELECT @max_wt  := max(weight) from civicrm_option_value where option_group_id=@option_group_id_activity_type;
-- FIXME for multilingual

{if $multilingual}
  INSERT INTO civicrm_option_value
    (option_group_id,                {foreach from=$locales item=locale}label_{$locale}, description_{$locale},{/foreach}      value,                            name,           weight,                           filter,          component_id) VALUES
    (@option_group_id_activity_type, {foreach from=$locales item=locale}'Bulk Email',   'Bulk Email Sent.',    {/foreach}     (SELECT @max_val := @max_val+1),  'Bulk Email',    (SELECT @max_wt := @max_wt+1),  1,                NULL ),
    (@option_group_id_activity_type, {foreach from=$locales item=locale}'Assign Case Role',   '',    {/foreach}     (SELECT @max_val := @max_val+2),       'Assign Case Role',    (SELECT @max_wt := @max_wt+2),  0,            @caseCompId ),
    (@option_group_id_activity_type, {foreach from=$locales item=locale}'Remove Case Role',   '',    {/foreach}     (SELECT @max_val := @max_val+3),       'Remove Case Role',    (SELECT @max_wt := @max_wt+3),  0,            @caseCompId );

{else}
  INSERT INTO civicrm_option_value
    (option_group_id,                label,            description,          value,                           name,           weight,                           filter,                   component_id) VALUES
    (@option_group_id_activity_type, 'Bulk Email',     'Bulk Email Sent.',   (SELECT @max_val := @max_val+1), 'Bulk Email',   (SELECT @max_wt := @max_wt+1),  1,                         NULL ),
    (@option_group_id_activity_type, 'Assign Case Role',     '',   (SELECT @max_val := @max_val+2), 'Assign Case Role',       (SELECT @max_wt := @max_wt+2),  0,   @caseCompId ),
    (@option_group_id_activity_type, 'Remove Case Role',     '',   (SELECT @max_val := @max_val+3), 'Remove Case Role',       (SELECT @max_wt := @max_wt+3),  0,   @caseCompId );
    
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


-- CRM-4461
-- Add a new custom html type advanced multi-select
-- CRM-4679
-- Add a new custom data type Auto-Complete & html type Contact Reference

ALTER TABLE `civicrm_custom_field` 
MODIFY `data_type` enum ('String', 'Int', 'Float', 'Money', 'Memo', 'Date', 'Boolean', 'StateProvince', 'Country', 'File', 'Link', 'Auto-complete')NOT NULL COMMENT 'Controls location of data storage in extended_data table.',
MODIFY `html_type` enum ('Text', 'TextArea', 'Select', 'Multi-Select', 'AdvMulti-Select', 'Radio', 'CheckBox', 'Select Date', 'Select State/Province', 'Select Country', 'Multi-Select Country', 'Multi-Select State/Province', 'File', 'Link', 'RichTextEditor', 'Contact Reference')NOT NULL COMMENT 'HTML types plus several built-in extended types.';

-- CRM-4407
-- Add civicrm_navigation table for CiviCRM Menu

CREATE TABLE `civicrm_navigation` (
`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
`path` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Path Name', 
`label` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Navigation Title', 
`name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Internal Name',
`url` VARCHAR(255) NULL DEFAULT NULL COMMENT 'url in case of custom navigation link',
`permission` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Permission for menu item',
`permission_operator` VARCHAR(3)  NULL DEFAULT NULL COMMENT 'Permission Operator',
`parent_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Parent navigation item, used for grouping',
`is_active` TINYINT( 4 ) NULL DEFAULT NULL COMMENT 'Is this navigation item active?',
`has_separator` TINYINT( 4 ) NULL DEFAULT NULL COMMENT 'If separator needs to be added after this menu item',
`weight` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Ordering of the navigation items in various blocks.'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `civicrm_navigation` 
ADD INDEX `FK_civicrm_navigation_parent_id` ( `parent_id` );

-- Insert default menu to table

INSERT INTO civicrm_navigation
( id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight ) VALUES

( 1,  NULL, '{ts escape="sql"}Search...{/ts}', 'Search...', NULL, '', NULL, '1', NULL, 1 ), 
( 2,  'civicrm/contact/search?reset=1', '{ts escape="sql"}Find Contacts{/ts}', 'Find Contacts', NULL, '', '1', '1', NULL, 1 ), 
( 3,  'civicrm/contact/search/advanced?reset=1', '{ts escape="sql"}Find Contacts - Advanced Search{/ts}', 'Find Contacts - Advanced Search', NULL, '', '1', '1', NULL, 2 ), 
( 4,  'civicrm/contact/search/custom?csid=15&reset=1', '{ts escape="sql"}Full-text Search{/ts}', 'Full-text Search', NULL, '', '1', '1', NULL, 3 ), 
( 5,  'civicrm/contact/search/builder?reset=1', '{ts escape="sql"}Search Builder{/ts}', 'Search Builder', NULL, '', '1', '1', '1', 4 ), 
( 6,  'civicrm/case/search?reset=1', '{ts escape="sql"}Find Cases{/ts}', 'Find Cases', 'access CiviCase', '', '1', '1', NULL, 5 ), 
( 7,  'civicrm/contribute/search?reset=1', '{ts escape="sql"}Find Contributions{/ts}', 'Find Contributions', 'access CiviContribute', '', '1', '1', NULL, 6 ), 
( 8,  'civicrm/mailing?reset=1', '{ts escape="sql"}Find Mailings{/ts}', 'Find Mailings', 'access CiviMail', '', '1', '1', NULL, 7 ), 
( 9,  'civicrm/member/search?reset=1', '{ts escape="sql"}Find Members{/ts}', 'Find Members', 'access CiviMember', '', '1', '1', NULL, 8 ), 
( 10, 'civicrm/event/search?reset=1', '{ts escape="sql"}Find Participants{/ts}', 'Find Participants',  'access CiviEvent', '', '1', '1', NULL, 9 ), 
( 11, 'civicrm/pledge/search?reset=1', '{ts escape="sql"}Find Pledges{/ts}', 'Find Pledges', 'access CiviPledge', '', '1', '1', 1, 10 ), 

( 12, 'civicrm/contact/search/custom/list?reset=1', '{ts escape="sql"}Custom Searches...{/ts}', 'Custom Searches...', NULL, '', '1', '1', NULL, 11 ), 
( 13, 'civicrm/contact/search/custom?reset=1&csid=8', '{ts escape="sql"}Activity Search{/ts}', 'Activity Search', NULL, '', '12', '1', NULL, 1 ), 
( 14, 'civicrm/contact/search/custom?reset=1&csid=11', '{ts escape="sql"}Contacts by Date Added{/ts}', 'Contacts by Date Added', NULL, '', '12', '1', NULL, 2 ), 
( 15, 'civicrm/contact/search/custom?reset=1&csid=2', '{ts escape="sql"}Contributors by Aggregate Totals{/ts}', 'Contributors by Aggregate Totals', NULL, '', '12', '1', NULL, 3 ), 
( 16, 'civicrm/contact/search/custom?reset=1&csid=6', '{ts escape="sql"}Proximity Search{/ts}', 'Proximity Search', NULL, '', '12', '1', NULL, 4 ), 

( 17, NULL, '{ts escape="sql"}Contacts{/ts}', 'Contacts', NULL, '', NULL, '1', NULL, 3 ), 
( 18, 'civicrm/contact/add?reset=1&ct=Individual', '{ts escape="sql"}New Individual{/ts}', 'New Individual', NULL, '', '17', '1', NULL, 1 ), 
( 19, 'civicrm/contact/add?reset=1&ct=Household', '{ts escape="sql"}New Household{/ts}', 'New Household', NULL, '', '17', '1', NULL, 2 ), 
( 20, 'civicrm/contact/add?reset=1&ct=Organization', '{ts escape="sql"}New Organization{/ts}', 'New Organization', NULL, '', '17', '1', 1, 3 ), 
( 21, 'civicrm/activity?reset=1&action=add&context=standalone', '{ts escape="sql"}New Activity{/ts}', 'New Activity', NULL, '', '17', '1', NULL, 4 ), 
( 22, 'civicrm/contact/view/activity?atype=3&action=add&reset=1&context=standalone', '{ts escape="sql"}New Email{/ts}', 'New Email', NULL, '', '17', '1', '1', 5 ), 
( 23, 'civicrm/import/contact?reset=1', '{ts escape="sql"}Import Contacts{/ts}', 'Import Contacts', NULL, '', '17', '1', NULL, 6 ), 
( 24, 'civicrm/import/activity?reset=1', '{ts escape="sql"}Import Activities{/ts}', 'Import Activities', NULL, '', '17', '1', '1', 7 ), 
( 25, 'civicrm/group/add?reset=1', '{ts escape="sql"}New Group{/ts}', 'New Group', NULL, '', '17', '1', NULL, 8 ), 
( 26, 'civicrm/group?reset=1', '{ts escape="sql"}Manage Groups{/ts}', 'Manage Groups', NULL, '', '17', '1', '1', 9 ), 
( 27,'civicrm/admin/tag?reset=1&action=add', '{ts escape="sql"}New Tag{/ts}', 'New Tag', NULL, '', '17', '1', NULL, 10 ), 
( 28,'civicrm/admin/tag?reset=1', '{ts escape="sql"}Manage Tags (Categories){/ts}', 'Manage Tags (Categories)', NULL, '', '17', '1', NULL, 11 ), 

( 29,NULL, '{ts escape="sql"}Contributions{/ts}', 'Contributions', 'access CiviContribute', '', NULL, '1', NULL, 4 ), 
( 30,'civicrm/contribute?reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '29', '1', NULL, 1 ), 
( 31,'civicrm/contact/view/contribution?reset=1&action=add&context=standalone', '{ts escape="sql"}New Contribution{/ts}', 'New Contribution', NULL, '', '29', '1', NULL, 2 ), 
( 32, 'civicrm/contribute/search?reset=1', '{ts escape="sql"}Find Contributions{/ts}', 'Find Contributions', NULL, '', '29', '1', NULL, 3 ), 
( 33, 'civicrm/contribute/import?reset=1', '{ts escape="sql"}Import Contributions{/ts}', 'Import Contributions', NULL, '', '29', '1', '1', 4 ),
( 34,NULL, '{ts escape="sql"}Pledges{/ts}', 'Pledges', 'access CiviPledge', '', 29, '1', 1, 5 ), 
( 35,'civicrm/pledge?reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '34', '1', NULL, 1 ), 
( 36,'civicrm/pledge/search?reset=1', '{ts escape="sql"}Find Pledges{/ts}', 'Find Pledges', NULL, '', '34', '1', NULL, 2 ), 
( 37, 'civicrm/admin/contribute?reset=1&action=add', '{ts escape="sql"}New Contribution Page{/ts}', 'New Contribution Page', NULL, '', '29', '1', NULL, 6 ), 
( 38, 'civicrm/admin/contribute?reset=1', '{ts escape="sql"}Manage Contribution Pages{/ts}', 'Manage Contribution Pages', NULL, '', '29', '1', '1', 7 ), 
( 39, 'civicrm/admin/pcp?reset=1', '{ts escape="sql"}Personal Campaign Pages{/ts}', 'Personal Campaign Pages', NULL, '', '29', '1', NULL, 8 ), 
( 40, 'civicrm/admin/contribute/managePremiums?reset=1', '{ts escape="sql"}Premiums (Thank-you Gifts){/ts}', 'Premiums', NULL, '', '29', '1', NULL, 9	 ), 

( 41, NULL, '{ts escape="sql"}Events{/ts}', 'Events', 'access CiviEvent', '', NULL, '1', NULL, 5 ), 
( 42, 'civicrm/event?reset=1', '{ts escape="sql"}Dashboard{/ts}', 'CiviEvent Dashboard', NULL, '', '41', '1', NULL, 1 ), 
( 43, 'civicrm/contact/view/participant?reset=1&action=add&context=standalone', '{ts escape="sql"}Register Event Participant{/ts}', 'Register Event Participant', NULL, '', '41', '1', NULL, 2 ), 
( 44, 'civicrm/event/search?reset=1', '{ts escape="sql"}Find Participants{/ts}', 'Find Participants', NULL, '', '41', '1', NULL, 3 ), 
( 45, 'civicrm/event/import?reset=1', '{ts escape="sql"}Import Participants{/ts}', 'Import Participants', NULL, '', '41', '1', '1', 4 ), 
( 46, 'civicrm/event/add?reset=1&action=add', '{ts escape="sql"}New Event{/ts}', 'New Event', NULL, '', '41', '1', NULL, 5 ), 
( 47, 'civicrm/event/manage?reset=1', '{ts escape="sql"}Manage Events{/ts}', 'Manage Events', NULL, '', '41', '1', 1, 6 ), 
( 48, 'civicrm/admin/eventTemplate?reset=1', '{ts escape="sql"}Event Templates{/ts}', 'Event Templates', 'access CiviEvent, administer CiviCRM', '', '41', '1', 1, 7 ), 
( 49, 'civicrm/admin/price?reset=1&action=add', '{ts escape="sql"}New Price Set{/ts}', 'New Price Set', NULL, '', '41', '1', NULL, 8 ), 
( 50, 'civicrm/event/price?reset=1', '{ts escape="sql"}Manage Price Sets{/ts}', 'Manage Price Sets', NULL, '', '41', '1', NULL, 9 ),

( 51, NULL, '{ts escape="sql"}Mailings{/ts}', 'Mailings', 'access CiviMail', '', NULL, '1', NULL, 6 ), 
( 52, 'civicrm/mailing/send?reset=1', '{ts escape="sql"}New Mailing{/ts}', 'New Mailing', NULL, '', '51', '1', NULL, 1 ), 
( 53, 'civicrm/mailing/browse/unscheduled?reset=1&scheduled=false', '{ts escape="sql"}Draft and Unscheduled Mailings{/ts}', 'Draft and Unscheduled Mailings', NULL, '', '51', '1', NULL, 2 ), 
( 54, 'civicrm/mailing/browse/scheduled?reset=1&scheduled=true', '{ts escape="sql"}Scheduled and Sent Mailings{/ts}', 'Scheduled and Sent Mailings', NULL, '', '51', '1', NULL, 3 ), 
( 55, 'civicrm/mailing/browse/archived?reset=1', '{ts escape="sql"}Archived Mailings{/ts}', 'Archived Mailings', NULL, '', '51', '1', 1, 4 ), 
( 56, 'civicrm/admin/component?reset=1', '{ts escape="sql"}Headers, Footers, and Automated Messages{/ts}', 'Headers, Footers, and Automated Messages', NULL, '', '51', '1', NULL, 5 ), 
( 57, 'civicrm/admin/options/from_email?group=from_email_address&reset=1', '{ts escape="sql"}From Email Addresses{/ts}', 'From Email Addresses', NULL, '', '51', '1', NULL, 6 ), 

( 58, NULL, '{ts escape="sql"}Memberships{/ts}', 'Memberships', 'access CiviMember', '', NULL, '1', NULL, 7 ), 
( 59, 'civicrm/member?reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '58', '1', NULL, 1 ), 
( 60, 'civicrm/contact/view/membership?reset=1&action=add&context=standalone', '{ts escape="sql"}New Membership{/ts}', 'New Membership', NULL, '', '58', '1', NULL, 2 ), 
( 61, 'civicrm/member/search?reset=1', '{ts escape="sql"}Find Members{/ts}', 'Find Members', NULL, '', '58', '1', NULL, 3 ), 
( 62, 'civicrm/member/import?reset=1', '{ts escape="sql"}Import Members{/ts}', 'Import Members', NULL, '', '58', '1', NULL, 4 ), 

( 63, NULL, '{ts escape="sql"}Other{/ts}', 'Other', 'access CiviGrant, access CiviCase', 'OR', NULL, '1', NULL, 8 ), 
( 64, NULL, '{ts escape="sql"}Cases{/ts}', 'Cases', 'access CiviCase', '', '63', '1', NULL, 1 ), 
( 65, 'civicrm/case?reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '64', '1', NULL, 1 ), 
( 66, 'civicrm/contact/view/case?reset=1&action=add&atype=13&context=standalone', '{ts escape="sql"}New Case{/ts}', 'New Case', NULL, '', '64', '1', NULL, 2 ), 
( 67, 'civicrm/case/search?reset=1', '{ts escape="sql"}Find Cases{/ts}', 'Find Cases', NULL, '', '64', '1', 1, 3 ), 

( 68, NULL, '{ts escape="sql"}Grants{/ts}', 'Grants', 'access CiviGrant', '', '63', '1', NULL, 2 ),
( 69, 'civicrm/grant?reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '68', '1', NULL, 1 ), 
( 70, 'civicrm/contact/view/grant?reset=1&action=add&context=standalone', '{ts escape="sql"}New Grant{/ts}', 'New Grant', NULL, '', '68', '1', NULL, 2 ), 
( 71, 'civicrm/grant/search?reset=1', '{ts escape="sql"}Find Grants{/ts}', 'Find Grants', NULL, '', '68', '1', 1, 3 ), 

( 72, NULL, '{ts escape="sql"}Administer{/ts}', 'Administer', 'administer CiviCRM', '', NULL, '1', NULL, 9 ), 
( 73, 'civicrm/admin?reset=1', '{ts escape="sql"}Administration Console{/ts}', 'Administration Console', NULL, '', '72', '1', NULL, 1 ), 

( 74, NULL, '{ts escape="sql"}Customize{/ts}', 'Customize', NULL, '', '72', '1', NULL, 2 ), 
( 75, 'civicrm/admin/custom/group?reset=1', '{ts escape="sql"}Custom Data{/ts}', 'Custom Data', NULL, '', '74', '1', NULL, 1 ), 
( 76, 'civicrm/admin/uf/group?reset=1', '{ts escape="sql"}CiviCRM Profile{/ts}', 'CiviCRM Profile', NULL, '', '74', '1', NULL, 2 ), 
( 77, 'civicrm/admin/menu?reset=1', '{ts escape="sql"}Navigation Menu{/ts}', 'Navigation Menu', NULL, '', '74', '1', NULL, 3 ), 
( 78, 'civicrm/admin/options/custom_search?reset=1&group=custom_search', '{ts escape="sql"}Manage Custom Searches{/ts}', 'Manage Custom Searches', NULL, '', '74', '1', NULL, 4 ), 

( 79, NULL, '{ts escape="sql"}Configure{/ts}', 'Configure', NULL, '', '72', '1', NULL, 3 ), 
( 80, 'civicrm/admin/configtask?reset=1', '{ts escape="sql"}Configuration Checklist{/ts}', 'Configuration Checklist', NULL, '', '79', '1', NULL, 1 ), 

( 81, 'civicrm/admin/setting?reset=1', '{ts escape="sql"}Global Settings{/ts}', 'Global Settings', NULL, '', '79', '1', NULL, 2 ), 
( 82, 'civicrm/admin/setting/component?reset=1', '{ts escape="sql"}Enable CiviCRM Components{/ts}', 'Enable Components', NULL, '', '81', '1', NULL, 1 ), 
( 83, 'civicrm/admin/setting/preferences/display?reset=1', '{ts escape="sql"}Site Preferences (screen and form configuration){/ts}', 'Site Preferences', NULL, '', '81', '1', NULL, 2 ), 
( 84, 'civicrm/admin/setting/path?reset=1', '{ts escape="sql"}Directories{/ts}', 'Directories', NULL, '', '81', '1', NULL, 3 ), 
( 85, 'civicrm/admin/setting/url?reset=1', '{ts escape="sql"}Resource URLs{/ts}', 'Resource URLs', NULL, '', '81', '1', NULL, 4 ), 
( 86, 'civicrm/admin/setting/smtp?reset=1', '{ts escape="sql"}Outbound Email (SMTP/Sendmail){/ts}', 'Outbound Email', NULL, '', '81', '1', NULL, 5 ), 
( 87, 'civicrm/admin/setting/mapping?reset=1', '{ts escape="sql"}Mapping and Geocoding{/ts}', 'Mapping and Geocoding', NULL, '', '81', '1', NULL, 6 ), 
( 88, 'civicrm/admin/paymentProcessor?reset=1', '{ts escape="sql"}Payment Processors{/ts}', 'Payment Processors', NULL, '', '81', '1', NULL, 7 ), 
( 89, 'civicrm/admin/setting/localization?reset=1', '{ts escape="sql"}Localization{/ts}', 'Localization', NULL, '', '81', '1', NULL, 8 ), 
( 90, 'civicrm/admin/setting/preferences/address?reset=1', '{ts escape="sql"}Address Settings{/ts}', 'Address Settings', NULL, '', '81', '1', NULL, 9 ), 
( 91, 'civicrm/admin/setting/date?reset=1', '{ts escape="sql"}Date Formats{/ts}', 'Date Formats', NULL, '', '81', '1', NULL, 10 ), 
( 92, 'civicrm/admin/setting/uf?reset=1', '{ts escape="sql"}CMS Integration{/ts}', 'CMS Integration', NULL, '', '81', '1', NULL, 11 ), 
( 93, 'civicrm/admin/setting/misc?reset=1', '{ts escape="sql"}Miscellaneous (version check, search, reCAPTCHA...){/ts}', 'Miscellaneous', NULL, '', '81', '1', NULL, 12 ), 
( 94, 'civicrm/admin/options/safe_file_extension?group=safe_file_extension&reset=1', '{ts escape="sql"}Safe File Extensions{/ts}', 'Safe File Extensions', NULL, '', '81', '1', NULL, 13 ), 
( 95, 'civicrm/admin/setting/debug?reset=1', '{ts escape="sql"}Debugging{/ts}', 'Debugging', NULL, '', '81', '1', NULL, 14 ), 

( 96, 'civicrm/admin/mapping?reset=1', '{ts escape="sql"}Import/Export Mappings{/ts}', 'Import/Export Mappings', NULL, '', '79', '1', NULL, 3 ), 
( 97, 'civicrm/admin/messageTemplates?reset=1', '{ts escape="sql"}Message Templates{/ts}', 'Message Templates', NULL, '', '79', '1', NULL, 4 ), 
( 98, 'civicrm/contact/domain?action=update&reset=1', '{ts escape="sql"}Domain Information{/ts}', 'Domain Information', NULL, '', '79', '1', NULL, 5 ), 
( 99, 'civicrm/admin/options/from_email_address?group=from_email_address&reset=1', '{ts escape="sql"}FROM Email Addresses{/ts}', 'FROM Email Addresses', NULL, '', '79', '1', NULL, 6 ), 
( 100, 'civicrm/admin/setting/updateConfigBackend?reset=1', '{ts escape="sql"}Update Directory Path and URL{/ts}', 'Update Directory Path and URL', NULL, '', '79', '1', NULL, 7 ), 

( 101, NULL, '{ts escape="sql"}Manage{/ts}', 'Manage', NULL, '', '72', '1', NULL, 4 ), 
( 102, 'civicrm/admin/deduperules?reset=1', '{ts escape="sql"}Find and Merge Duplicate Contacts{/ts}', 'Find and Merge Duplicate Contacts', '', '', '101', '1', NULL, 1 ), 
( 103, 'civicrm/admin/access?reset=1', '{ts escape="sql"}Access Control{/ts}', 'Access Control', NULL, '', '101', '1', NULL, 2 ), 
( 104, 'civicrm/admin/synchUser?reset=1', '{ts escape="sql"}Synchronize Users to Contacts{/ts}', 'Synchronize Users to Contacts', NULL, '', '101', '1', NULL, 3 ), 

( 105, NULL, '{ts escape="sql"}Option Lists{/ts}', 'Option Lists', NULL, '', '72', '1', NULL, 5 ), 
( 106, 'civicrm/admin/options/activity_type?reset=1&group=activity_type', '{ts escape="sql"}Activity Types{/ts}', 'Activity Types', NULL, '', '105', '1', NULL, 1 ), 
( 107, 'civicrm/admin/reltype?reset=1', '{ts escape="sql"}Relationship Types{/ts}', 'Relationship Types', NULL, '', '105', '1', NULL, 2 ), 
( 108, 'civicrm/admin/tag?reset=1', '{ts escape="sql"}Tags (Categories){/ts}', 'Tags (Categories)', NULL, '', '105', '1', 1, 3 ), 
( 109, 'civicrm/admin/options/gender?reset=1&group=gender', '{ts escape="sql"}Gender Options{/ts}', 'Gender Options', NULL, '', '105', '1', NULL, 4 ), 
( 110, 'civicrm/admin/options/individual_prefix?group=individual_prefix&reset=1', '{ts escape="sql"}Individual Prefixes (Ms, Mr...){/ts}', 'Individual Prefixes (Ms, Mr...)', NULL, '', '105', '1', NULL, 5 ), 
( 111, 'civicrm/admin/options/individual_suffix?group=individual_suffix&reset=1', '{ts escape="sql"}Individual Suffixes (Jr, Sr...){/ts}', 'Individual Suffixes (Jr, Sr...)', NULL, '', '105', '1', 1, 6 ), 
( 112, 'civicrm/admin/options/addressee?group=addressee&reset=1', '{ts escape="sql"}Addressee Formats{/ts}', 'Addressee Formats', NULL, '', '105', '1', NULL, 7 ), 
( 113, 'civicrm/admin/options/email_greeting?group=email_greeting&reset=1', '{ts escape="sql"}Email Greetings{/ts}', 'Email Greetings', NULL, '', '105', '1', NULL, 8 ), 
( 114, 'civicrm/admin/options/postal_greeting?group=postal_greeting&reset=1', '{ts escape="sql"}Postal Greetings{/ts}', 'Postal Greetings', NULL, '', '105', '1', 1, 9 ), 
( 115, 'civicrm/admin/options/instant_messenger_service?group=instant_messenger_service&reset=1', '{ts escape="sql"}Instant Messenger Services{/ts}', 'Instant Messenger Services', NULL, '', '105', '1', NULL, 10 ), 
( 116, 'civicrm/admin/locationType?reset=1', '{ts escape="sql"}Location Types (Home, Work...){/ts}', 'Location Types (Home, Work...)', NULL, '', '105', '1', NULL, 11 ), 
( 117, 'civicrm/admin/options/mobile_provider?group=mobile_provider&reset=1', '{ts escape="sql"}Mobile Phone Providers{/ts}', 'Mobile Phone Providers', NULL, '', '105', '1', NULL, 12 ), 
( 118, 'civicrm/admin/options/phone_type?group=phone_type&reset=1', '{ts escape="sql"}Phone Types{/ts}', 'Phone Types', NULL, '', '105', '1', NULL, 13 ), 
( 119, 'civicrm/admin/options/preferred_communication_method?group=preferred_communication_method&reset=1', '{ts escape="sql"}Preferred Communication Methods{/ts}', 'Preferred Communication Methods', NULL, '', '105', '1', NULL, 14 ), 

( 120, NULL, '{ts escape="sql"}CiviCase{/ts}', 'CiviCase', 'access CiviCase, administer CiviCRM', '', '72', '1', NULL, 6 ), 
( 121, 'civicrm/admin/options/case_type?group=case_type&reset=1', '{ts escape="sql"}Case Types{/ts}', 'Case Types', 'access CiviCase, administer CiviCRM', '', '120', '1', NULL, 1 ), 
( 122, 'civicrm/admin/options/redaction_rule?group=redaction_rule&reset=1', '{ts escape="sql"}Redaction Rules{/ts}', 'Redaction Rules', 'access CiviCase, administer CiviCRM', '', '120', '1', NULL, 2 ), 

( 123, NULL, '{ts escape="sql"}CiviContribute{/ts}', 'CiviContribute', 'access CiviContribute, administer CiviCRM', '', '72', '1', NULL, 7 ), 
( 124, 'civicrm/admin/contribute?reset=1&action=add', '{ts escape="sql"}New Contribution Page{/ts}', 'New Contribution Page', NULL, '', '123', '1', NULL, 6 ), 
( 125, 'civicrm/admin/contribute?reset=1', '{ts escape="sql"}Manage Contribution Pages{/ts}', 'Manage Contribution Pages', NULL, '', '123', '1', '1', 7 ), 
( 126, 'civicrm/admin/pcp?reset=1', '{ts escape="sql"}Personal Campaign Pages{/ts}', 'Personal Campaign Pages', NULL, '', '123', '1', NULL, 8 ), 
( 127, 'civicrm/admin/contribute/managePremiums?reset=1', '{ts escape="sql"}Premiums (Thank-you Gifts){/ts}', 'Premiums', NULL, '', '123', '1', 1, 9	 ), 
( 128, 'civicrm/admin/contribute/contributionType?reset=1', '{ts escape="sql"}Contribution Types{/ts}', 'Contribution Types', NULL, '', '123', '1', NULL, 10	 ), 
( 129, 'civicrm/admin/options/payment_instrument?group=payment_instrument&reset=1', '{ts escape="sql"}Payment Instruments{/ts}', 'Payment Instruments', NULL, '', '123', '1', NULL, 11	 ), 
( 130, 'civicrm/admin/options/accept_creditcard?group=accept_creditcard&reset=1', '{ts escape="sql"}Accepted Credit Cards{/ts}', 'Accepted Credit Cards', NULL, '', '123', '1', NULL, 12	 ), 

( 131, NULL, '{ts escape="sql"}CiviEvent{/ts}', 'CiviEvent', 'access CiviEvent, administer CiviCRM', '', '72', '1', NULL, 8 ), 
( 132, 'civicrm/event/add?reset=1&action=add', '{ts escape="sql"}New Event{/ts}', 'New Event', NULL, '', '131', '1', NULL, 1 ), 
( 133, 'civicrm/event/manage?reset=1', '{ts escape="sql"}Manage Events{/ts}', 'Manage Events', NULL, '', '131', '1', 1, 2 ), 
( 134, 'civicrm/admin/eventTemplate?reset=1', '{ts escape="sql"}Event Templates{/ts}', 'Event Templates', 'access CiviEvent, administer CiviCRM', '', '131', '1', 1, 3 ), 
( 135, 'civicrm/admin/price?reset=1&action=add', '{ts escape="sql"}New Price Set{/ts}', 'New Price Set', NULL, '', '131', '1', NULL, 4 ), 
( 136, 'civicrm/event/price?reset=1', '{ts escape="sql"}Manage Price Sets{/ts}', 'Manage Price Sets', NULL, '', '131', '1', 1, 5 ),
( 137, 'civicrm/admin/options/participant_listing?group=participant_listing&reset=1', '{ts escape="sql"}Participant Listing Templates{/ts}', 'Participant Listing Templates', NULL, '', '131', '1', NULL, 6 ), 
( 138, 'civicrm/admin/options/event_type?group=event_type&reset=1', '{ts escape="sql"}Event Types{/ts}', 'Event Types', NULL, '', '131', '1', NULL, 7 ), 
( 139, 'civicrm/admin/participant_status?reset=1', '{ts escape="sql"}Participant Statuses{/ts}', 'Participant Statuses', NULL, '', '131', '1', NULL, 8 ), 
( 140, 'civicrm/admin/options/participant_role?group=participant_role&reset=1', '{ts escape="sql"}Participant Roles{/ts}', 'Participant Roles', NULL, '', '131', '1', NULL, 9 ), 

( 141, NULL, '{ts escape="sql"}CiviGrant{/ts}', 'CiviGrant', 'access CiviGrant, administer CiviCRM', '', '72', '1', NULL, 9 ), 
( 142, 'civicrm/admin/options/grant_type?group=grant_type&reset=1', '{ts escape="sql"}Grant Types{/ts}', 'Grant Types', 'access CiviGrant, administer CiviCRM', '', '141', '1', NULL, 1 ), 

( 143, NULL, '{ts escape="sql"}CiviMail{/ts}', 'CiviMail', 'access CiviMail, administer CiviCRM', '', '72', '1', NULL, 10 ), 
( 144, 'civicrm/admin/component?reset=1', '{ts escape="sql"}Headers, Footers, and Automated Messages{/ts}', 'Headers, Footers, and Automated Messages', NULL, '', '143', '1', NULL, 1 ), 
( 145, 'civicrm/admin/options/from_email?group=from_email_address&reset=1', '{ts escape="sql"}From Email Addresses{/ts}', 'From Email Addresses', NULL, '', '143', '1', NULL, 2 ), 
( 146, 'civicrm/admin/mailSettings?reset=1', '{ts escape="sql"}Mail Accounts{/ts}', 'Mail Accounts', NULL, '', '143', '1', NULL, 3 ), 

( 147, NULL, '{ts escape="sql"}CiviMember{/ts}', 'CiviMember', 'access CiviMember, administer CiviCRM', '', '72', '1', NULL, 11 ), 
( 148, 'civicrm/admin/member/membershipType?reset=1', '{ts escape="sql"}Membership Types{/ts}', 'Membership Types', 'access CiviMember, administer CiviCRM', '', '147', '1', NULL, 1 ), 
( 149, 'civicrm/admin/member/membershipStatus?reset=1', '{ts escape="sql"}Membership Status Rules{/ts}', 'Membership Status Rules', 'access CiviMember, administer CiviCRM', '', '147', '1', NULL, 2 ), 

( 150, NULL, '{ts escape="sql"}CiviReport{/ts}', 'CiviReport', 'administer CiviCRM', '', '72', '1', NULL, 12 ), 
( 151, 'civicrm/report/list?reset=1', '{ts escape="sql"}Manage Reports{/ts}', 'Manage Reports', NULL, '', '150', '1', NULL, 1 ), 
( 152, 'civicrm/admin/report/template/list?reset=1', '{ts escape="sql"}Create Reports from Templates{/ts}', 'Create Reports from Templates', NULL, '', '150', '1', NULL, 2 ), 
( 153, 'civicrm/admin/report/options/report_template?reset=1', '{ts escape="sql"}Manage Templates{/ts}', 'Manage Templates', NULL, '', '150', '1', NULL, 3 ), 

( 154, NULL, '{ts escape="sql"}Help{/ts}', 'Help', NULL, '', NULL, '1', NULL, 10 ), 
( 155, 'http://documentation.civicrm.org', '{ts escape="sql"}Documentation{/ts}', 'Documentation', NULL, 'AND', '154', '1', NULL, 1 ), 
( 156, 'http://forum.civicrm.org', '{ts escape="sql"}Community Forums{/ts}', 'Community Forums', NULL, 'AND', '154', '1', NULL, 2 ), 
( 157, 'http://civicrm.org/participate', '{ts escape="sql"}Participate{/ts}', 'Participate', NULL, 'AND', '154', '1', NULL, 3 ), 
( 158, 'http://civicrm.org/aboutcivicrm', '{ts escape="sql"}About{/ts}', 'About', NULL, 'AND', '154', '1', NULL, 4 );

-- End navigation

-- CRM-4414
-- Add individual, organization and household default profile

INSERT INTO `civicrm_uf_group`
( `name`,               `group_type`,          `title`, `is_cms_user`,`is_reserved`,`help_post` ) VALUES
( 'new_individual',     'Individual,Contact',  '{ts escape="sql"}New Individual{/ts}'   , 0,           1,           NULL),
( 'new_organization',   'Organization,Contact','{ts escape="sql"}New Organization{/ts}'  , 0,           1,           NULL),
( 'new_household',      'Household,Contact',   '{ts escape="sql"}New Household{/ts}'    , 0,           1,           NULL);

SELECT @uf_group_id_individual   := max(id) from civicrm_uf_group where name = 'new_individual';
SELECT @uf_group_id_organization := max(id) from civicrm_uf_group where name = 'new_organization';
SELECT @uf_group_id_household    := max(id) from civicrm_uf_group where name = 'new_household';

INSERT INTO `civicrm_uf_join`
 ( `is_active`, `module`, `entity_table`, `entity_id`, `weight`, `uf_group_id` ) VALUES
 ( 1,           'Profile', NULL,           NULL,        3,       @uf_group_id_individual ),
 ( 1,           'Profile', NULL,           NULL,        4,       @uf_group_id_organization ),
 ( 1,           'Profile', NULL,           NULL,        5,       @uf_group_id_household );

INSERT INTO `civicrm_uf_field`
( `id`, `uf_group_id`, `field_name`, `is_required`, `is_reserved`, `weight`, `visibility`, `in_selector`, `is_searchable`, `location_type_id`, `label`, `field_type`, `help_post` )VALUES
( 12, @uf_group_id_individual, 'first_name', 1, 0, 1, 'User and User Admin Only', 0, 0, NULL, '{ts escape="sql"}FirstName{/ts}', 'Individual', NULL ), 
( 13, @uf_group_id_individual, 'last_name', 1, 0, 2, 'User and User Admin Only', 0, 0, NULL, '{ts escape="sql"}LastName{/ts}', 'Individual', NULL ), 
( 14, @uf_group_id_individual, 'email', 1, 0, 3, 'User and User Admin Only', 0, 0, NULL, '{ts escape="sql"}EmailAddress{/ts}', 'Contact', NULL ), 
( 15, @uf_group_id_organization, 'organization_name', 1, 0, 2, 'User and User Admin Only', 0, 0, NULL, '{ts escape="sql"}OrganizationName{/ts}', 'Organization', NULL ), 
( 16, @uf_group_id_organization, 'email', 1, 0, 3, 'User and User Admin Only', 0, 0, NULL, '{ts escape="sql"}EmailAddress{/ts}', 'Contact', NULL ), 
( 17, @uf_group_id_household, 'household_name', 1, 0, 2, 'User and User Admin Only', 0, 0, NULL, '{ts escape="sql"}HouseholdName{/ts}', 'Household', NULL ), 
( 18, @uf_group_id_household, 'email', 1, 0, 3, 'User and User Admin Only', 0, 0, NULL, '{ts escape="sql"}EmailAddress{/ts}', 'Contact', NULL );



-- CRM-4534

INSERT INTO civicrm_state_province (id, country_id, abbreviation, name) VALUES (5218, 1140, "DIF", "Distrito Federal");
UPDATE civicrm_state_province SET name = "Coahuila"  WHERE id = 3808;
UPDATE civicrm_state_province SET name = "Colima"    WHERE id = 3809;
UPDATE civicrm_state_province SET name = "Chihuahua" WHERE id = 3811;


-- CRM-4469
--Add collapse_adv_search column to civicrm_custom_group

ALTER TABLE `civicrm_custom_group` ADD `collapse_adv_display` int(10) unsigned default '0' COMMENT 'Will this group be in collapsed or expanded mode on advanced search display ?' AFTER `max_multiple`;



-- CRM-4587

UPDATE civicrm_state_province SET name = "Sofia"       WHERE id = 1859;
UPDATE civicrm_state_province SET name = "Ulaanbaatar" WHERE id = 3707;
UPDATE civicrm_state_province SET name = "Achaïa"      WHERE id = 2879;

-- CRM-4569

INSERT INTO 
   `civicrm_option_group` (`name`, `description`, `is_reserved`, `is_active`) 
VALUES 
   ('redaction_rule', 'Redaction Rule', 0, 1);

-- CRM-4394
UPDATE civicrm_state_province SET country_id = 1008 WHERE id = 1637;

---CRM-4633
ALTER TABLE `civicrm_contact`
  ADD `do_not_sms` tinyint(4) default '0' AFTER `do_not_mail`;

---CRM-4664
ALTER TABLE `civicrm_option_value` MODIFY `name` VARCHAR(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Stores a fixed (non-translated) name for this option value. Lookup functions should use the name as the key for the option value row.';

-- CRM-4687
-- set activity_date = due_date and drop due_date_time column from civicrm_activity.

UPDATE civicrm_activity ca INNER JOIN civicrm_case_activity cca ON ca.id = cca.activity_id 
       SET activity_date_time = COALESCE( ca.due_date_time, ca.activity_date_time );

ALTER TABLE civicrm_activity DROP COLUMN due_date_time;

        