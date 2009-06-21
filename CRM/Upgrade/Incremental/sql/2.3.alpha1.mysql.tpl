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
    (@option_group_id_activity_type, {foreach from=$locales item=locale}'Assign Case Role',   '',    {/foreach}     (SELECT @max_val := @max_val+2),       'Assign Case Role',    (SELECT @max_wt := @max_wt+2),  0,            @caseCompId );

{else}
  INSERT INTO civicrm_option_value
    (option_group_id,                label,            description,          value,                           name,           weight,                           filter,                   component_id) VALUES
    (@option_group_id_activity_type, 'Bulk Email',     'Bulk Email Sent.',   (SELECT @max_val := @max_val+1), 'Bulk Email',   (SELECT @max_wt := @max_wt+1),  1,                         NULL ),
    (@option_group_id_activity_type, 'Assign Case Role',     '',   (SELECT @max_val := @max_val+2), 'Assign Case Role',       (SELECT @max_wt := @max_wt+2),  0,   @caseCompId );
    
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
-- Add a new custom data type advanced multi-select

ALTER TABLE `civicrm_custom_field` 
   MODIFY `html_type` enum( 'Text', 'TextArea', 'Select', 'Multi-Select', 'AdvMulti-Select', 'Radio', 'CheckBox', 'Select Date', 'Select State/Province', 'Select Country', 'Multi-Select Country', 'Multi-Select State/Province', 'File', 'Link', 'RichTextEditor') NOT NULL COMMENT 'HTML types plus several built-in extended types.';

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
INSERT INTO `civicrm_navigation`
( `path`, `label`, `name`, `url`, `permission`, `permission_operator`, `parent_id`, `is_active`, `has_separator` ) VALUES
( NULL, '{ts escape="sql"}OurFavorites{/ts}', '{ts escape="sql"}OurFavorites{/ts}', '', NULL, '', NULL, '1', NULL ), 
( NULL, '{ts escape="sql"}CiviCRMBlog{/ts}', '{ts escape="sql"}CiviCRMBlog{/ts}', 'http://civicrm.org/blog', NULL, 'AND', '1', '1', NULL ), 
( 'civicrm/profile/create?gid=1&reset=1', '{ts escape="sql"}CustomProfile{/ts}', '{ts escape="sql"}CustomProfile{/ts}', '', NULL, '', '1', '1', NULL ), 
( NULL, '{ts escape="sql"}Search...{/ts}', '{ts escape="sql"}Search...{/ts}', '', NULL, '', NULL, '1', NULL ), 
( 'civicrm/contact/search', '{ts escape="sql"}FindContacts( Basic ){/ts}', '{ts escape="sql"}FindContacts{/ts}', '', NULL, '', '4', '1', NULL ), 
( 'civicrm/contact/search/advanced', '{ts escape="sql"}FindContacts( Advanced ){/ts}', '{ts escape="sql"}AdvancedSearch{/ts}', '', NULL, '', '4', '1', NULL ), 
( 'civicrm/contact/search/custom&csid=15', '{ts escape="sql"}Full-textSearch{/ts}', '{ts escape="sql"}Full-textSearch{/ts}', '', NULL, '', '4', '1', NULL ), 
( 'civicrm/contact/search/builder', '{ts escape="sql"}SearchBuilder{/ts}', '{ts escape="sql"}SearchBuilder{/ts}', '', NULL, '', '4', '1', '1' ), 
( 'civicrm/case/search', '{ts escape="sql"}FindCases{/ts}', '{ts escape="sql"}FindCases{/ts}', '', 'accessCiviCase', '', '4', '1', NULL ), 
( 'civicrm/contribute/search', '{ts escape="sql"}FindContributions{/ts}', '{ts escape="sql"}FindContributions{/ts}', '', 'accessCiviContribute', '', '4', '1', NULL ), 
( 'civicrm/mailing', '{ts escape="sql"}FindMailings{/ts}', '{ts escape="sql"}FindMailings{/ts}', '', 'accessCiviMail', '', '4', '1', NULL ), 
( 'civicrm/member/search', '{ts escape="sql"}FindMembers{/ts}', '{ts escape="sql"}FindMembers{/ts}', '', 'accessCiviMember', '', '4', '1', NULL ), 
( 'civicrm/event/search', '{ts escape="sql"}FindParticipants{/ts}', '{ts escape="sql"}FindParticipants{/ts}', '', 'accessCiviEvent', '', '4', '1', NULL ), 
( 'civicrm/pledge/search', '{ts escape="sql"}FindPledges{/ts}', '{ts escape="sql"}FindPledges{/ts}', '', 'accessCiviPledge', '', '4', '1', NULL ), 
( 'civicrm/contact/search/custom/list', '{ts escape="sql"}CustomSearches...{/ts}', '{ts escape="sql"}CustomSearches...{/ts}', '', NULL, '', '4', '1', NULL ), 
( 'civicrm/contact/search/custom&csid=11', '{ts escape="sql"}ContactsbyDateAdded{/ts}', '{ts escape="sql"}ContactsbyDateAdded{/ts}', '', NULL, '', '15', '1', NULL ), 
( 'civicrm/contact/search/custom&csid=6', '{ts escape="sql"}ProximitySearch{/ts}', '{ts escape="sql"}ProximitySearch{/ts}', '', NULL, '', '15', '1', NULL ), 
( NULL, '{ts escape="sql"}Contacts{/ts}', '{ts escape="sql"}Contacts{/ts}', '', NULL, '', NULL, '1', NULL ), 
( 'civicrm/contact/add&ct=Individual', '{ts escape="sql"}NewIndividual{/ts}', '{ts escape="sql"}NewIndividual{/ts}', '', NULL, '', '18', '1', NULL ), 
( 'civicrm/contact/add&ct=Household', '{ts escape="sql"}NewHousehold{/ts}', '{ts escape="sql"}NewHousehold{/ts}', '', NULL, '', '18', '1', NULL ), 
( 'civicrm/contact/add&ct=Organization', '{ts escape="sql"}NewOrganization{/ts}', '{ts escape="sql"}NewOrganization{/ts}', '', NULL, '', '18', '1', NULL ), 
( 'civicrm/activity&action=add&context=standalone', '{ts escape="sql"}NewActivity{/ts}', '{ts escape="sql"}NewActivity{/ts}', '', NULL, '', '18', '1', '1' ), 
( 'civicrm/import/contact', '{ts escape="sql"}ImportContacts{/ts}', '{ts escape="sql"}ImportContacts{/ts}', '', NULL, '', '18', '1', NULL ), 
( 'civicrm/import/activity', '{ts escape="sql"}ImportActivities{/ts}', '{ts escape="sql"}ImportActivities{/ts}', '', NULL, '', '18', '1', '1' ), 
( 'civicrm/group/add', '{ts escape="sql"}NewGroup{/ts}', '{ts escape="sql"}NewGroup{/ts}', '', NULL, '', '18', '1', NULL ), 
( 'civicrm/group', '{ts escape="sql"}ManageGroups{/ts}', '{ts escape="sql"}ManageGroups{/ts}', '', NULL, '', '18', '1', '1' ), 
( 'civicrm/admin/tag&action=add', '{ts escape="sql"}NewTag{/ts}', '{ts escape="sql"}NewTag{/ts}', '', NULL, '', '18', '1', NULL ), 
( 'civicrm/admin/tag', '{ts escape="sql"}ManageTags( Categories ){/ts}', '{ts escape="sql"}ManageTags( Categories ){/ts}', '', NULL, '', '18', '1', NULL ), 
( NULL, '{ts escape="sql"}Contributions{/ts}', '{ts escape="sql"}Contributions{/ts}', '', 'accessCiviContribute', '', NULL, '1', NULL ), 
( 'civicrm/contribute', '{ts escape="sql"}Dashboard{/ts}', '{ts escape="sql"}CiviContributeDashboard{/ts}', '', NULL, '', '29', '1', NULL ), 
( NULL, '{ts escape="sql"}NewContribution{/ts}', '{ts escape="sql"}NewContribution{/ts}', '', NULL, '', '29', '1', NULL ), 
( 'civicrm/contribute/search', '{ts escape="sql"}FindContributions{/ts}', '{ts escape="sql"}FindContributions{/ts}', '', NULL, '', '29', '1', NULL ), 
( 'civicrm/contribute/import', '{ts escape="sql"}ImportContributions{/ts}', '{ts escape="sql"}ImportContributions{/ts}', '', NULL, '', '29', '1', '1' ), 
( 'civicrm/admin/contribute&action=add', '{ts escape="sql"}NewContributionPage{/ts}', '{ts escape="sql"}NewContributionPage{/ts}', '', NULL, '', '29', '1', NULL ), 
( 'civicrm/admin/contribute', '{ts escape="sql"}ManageContributionPages{/ts}', '{ts escape="sql"}ManageContributionPages{/ts}', '', NULL, '', '29', '1', '1' ), 
( 'civicrm/admin/pcp', '{ts escape="sql"}ManagePersonalCampaignPages{/ts}', '{ts escape="sql"}PersonalCampaignPages{/ts}', '', NULL, '', '29', '1', NULL ), 
( NULL, '{ts escape="sql"}Events{/ts}', '{ts escape="sql"}Events{/ts}', '', 'accessCiviEvent', '', NULL, '1', NULL ), 
( 'civicrm/event', '{ts escape="sql"}Dashboard{/ts}', '{ts escape="sql"}CiviEventDashboard{/ts}', '', NULL, '', '37', '1', NULL ), 
( NULL, '{ts escape="sql"}RegisterNewParticipant{/ts}', '{ts escape="sql"}RegisterNewParticipant{/ts}', '', NULL, '', '37', '1', NULL ), 
( 'civicrm/event/search', '{ts escape="sql"}FindParticipants{/ts}', '{ts escape="sql"}FindParticipants{/ts}', '', NULL, '', '37', '1', NULL ), 
( 'civicrm/event/import', '{ts escape="sql"}ImportParticipants{/ts}', '{ts escape="sql"}ImportParticipants{/ts}', '', NULL, '', '37', '1', '1' ), 
( 'civicrm/event/add&action=add', '{ts escape="sql"}NewEvent{/ts}', '{ts escape="sql"}NewEvent{/ts}', '', NULL, '', '37', '1', NULL ), 
( 'civicrm/event/manage', '{ts escape="sql"}ManageEvents{/ts}', '{ts escape="sql"}ManageEvents{/ts}', '', NULL, '', '37', '1', NULL ), 
( 'civicrm/admin/price&action=add', '{ts escape="sql"}NewPriceSet{/ts}', '{ts escape="sql"}NewPriceSet{/ts}', '', NULL, '', '37', '1', NULL ), 
( 'civicrm/event/price', '{ts escape="sql"}ManagePriceSets{/ts}', '{ts escape="sql"}ManagePriceSets{/ts}', '', NULL, '', '37', '1', NULL ), 
( NULL, '{ts escape="sql"}Mailings{/ts}', '{ts escape="sql"}Mailings{/ts}', '', 'accessCiviMail', '', NULL, '1', NULL ), 
( 'civicrm/mailing/send', '{ts escape="sql"}NewMailing{/ts}', '{ts escape="sql"}NewMailing{/ts}', '', NULL, '', '46', '1', NULL ), 
( 'civicrm/mailing/browse/unscheduled&scheduled=false', '{ts escape="sql"}DraftandUnscheduledMailings{/ts}', '{ts escape="sql"}DraftandUnscheduledMailings{/ts}', '', NULL, '', '46', '1', NULL ), 
( 'civicrm/mailing/browse/scheduled&scheduled=true', '{ts escape="sql"}ScheduledandSentMailings{/ts}', '{ts escape="sql"}ScheduledandSentMailings{/ts}', '', NULL, '', '46', '1', NULL ), 
( 'civicrm/mailing/browse/archived', '{ts escape="sql"}ArchivedMailings{/ts}', '{ts escape="sql"}ArchivedMailings{/ts}', '', NULL, '', '46', '1', NULL ), 
( 'civicrm/admin/component', '{ts escape="sql"}Headers, Footers, andAutomatedMessages{/ts}', '{ts escape="sql"}Headers, Footers, andAutomatedMessages{/ts}', '', NULL, '', '46', '1', NULL ), 
( NULL, '{ts escape="sql"}Memberships{/ts}', '{ts escape="sql"}Memberships{/ts}', '', 'accessCiviMember', '', NULL, '1', NULL ), 
( 'civicrm/member', '{ts escape="sql"}Dashboard{/ts}', '{ts escape="sql"}CiviMemberDashboard{/ts}', '', NULL, '', '52', '1', NULL ), 
( NULL, '{ts escape="sql"}NewMember{/ts}', '{ts escape="sql"}NewMember{/ts}', '', NULL, '', '52', '1', NULL ), 
( 'civicrm/member/search', '{ts escape="sql"}FindMembers{/ts}', '{ts escape="sql"}FindMembers{/ts}', '', NULL, '', '52', '1', NULL ), 
( 'civicrm/member/import', '{ts escape="sql"}ImportMembers{/ts}', '{ts escape="sql"}ImportMembers{/ts}', '', NULL, '', '52', '1', NULL ), 
( NULL, '{ts escape="sql"}Other{/ts}', '{ts escape="sql"}Other{/ts}', '', 'accessCiviGrant, accessCiviCase', 'OR', NULL, '1', NULL ), 
( NULL, '{ts escape="sql"}Grants{/ts}', '{ts escape="sql"}Grants{/ts}', '', 'accessCiviGrant', '', '57', '1', NULL ), 
( 'civicrm/grant', '{ts escape="sql"}NewGrant{/ts}', '{ts escape="sql"}NewGrant{/ts}', '', NULL, '', '58', '1', NULL ), 
( NULL, '{ts escape="sql"}Cases{/ts}', '{ts escape="sql"}Cases{/ts}', '', 'accessCiviCase', '', '57', '1', NULL ), 
( 'civicrm/case', '{ts escape="sql"}Dashboard{/ts}', '{ts escape="sql"}CiviCaseDashboard{/ts}', '', NULL, '', '60', '1', NULL ), 
( NULL, '{ts escape="sql"}NewCaseforNewClient{/ts}', '{ts escape="sql"}NewCaseforNewClient{/ts}', '', NULL, '', '60', '1', NULL ), 
( 'civicrm/case/search', '{ts escape="sql"}FindCases{/ts}', '{ts escape="sql"}FindCases{/ts}', '', NULL, '', '60', '1', NULL ), 
( NULL, '{ts escape="sql"}Administer{/ts}', '{ts escape="sql"}Administer{/ts}', '', NULL, '', NULL, '1', NULL ), 
( 'civicrm/admin', '{ts escape="sql"}AdministrationConsole{/ts}', '{ts escape="sql"}AdministerCiviCRM{/ts}', '', NULL, '', '64', '1', NULL ), 
( NULL, '{ts escape="sql"}Customize{/ts}', '{ts escape="sql"}Customize{/ts}', '', NULL, '', '64', '1', NULL ), 
( 'civicrm/admin/custom/group', '{ts escape="sql"}CustomData{/ts}', '{ts escape="sql"}CustomData{/ts}', '', NULL, '', '66', '1', NULL ), 
( 'civicrm/admin/uf/group', '{ts escape="sql"}CiviCRMProfile{/ts}', '{ts escape="sql"}CiviCRMProfile{/ts}', '', NULL, '', '66', '1', NULL ), 
( 'civicrm/admin/options/custom_search&group=custom_search', '{ts escape="sql"}ManageCustomSearches{/ts}', '{ts escape="sql"}ManageCustomSearches{/ts}', '', NULL, '', '66', '1', NULL ), 
( NULL, '{ts escape="sql"}Configure{/ts}', '{ts escape="sql"}Configure{/ts}', '', NULL, '', '64', '1', NULL ), 
( 'civicrm/admin/configtask', '{ts escape="sql"}ConfigurationChecklist{/ts}', '{ts escape="sql"}ConfigurationChecklist{/ts}', '', NULL, '', '70', '1', NULL ), 
( 'civicrm/admin/setting', '{ts escape="sql"}GlobalSettings{/ts}', '{ts escape="sql"}GlobalSettings{/ts}', '', NULL, '', '70', '1', NULL ), 
( 'civicrm/admin/deduperules', '{ts escape="sql"}FindandMergeDuplicateContacts{/ts}', '{ts escape="sql"}FindandMergeDuplicateContacts{/ts}', '', '', '', '70', '1', NULL ), 
( 'civicrm/admin/mapping', '{ts escape="sql"}Import/ExportMappings{/ts}', '{ts escape="sql"}Import/ExportMappings{/ts}', '', NULL, '', '70', '1', NULL ), 
( 'civicrm/admin/messageTemplates', '{ts escape="sql"}MessageTemplates{/ts}', '{ts escape="sql"}MessageTemplates{/ts}', '', NULL, '', '70', '1', NULL ), 
( NULL, '{ts escape="sql"}Manage{/ts}', '{ts escape="sql"}Manage{/ts}', '', NULL, '', '64', '1', NULL ), 
( 'civicrm/admin/synchUser', '{ts escape="sql"}SynchronizeUserstoContacts{/ts}', '{ts escape="sql"}SynchronizeUserstoContacts{/ts}', '', NULL, '', '76', '1', NULL ), 
( 'civicrm/admin/access', '{ts escape="sql"}AccessControl{/ts}', '{ts escape="sql"}AccessControl{/ts}', '', NULL, '', '76', '1', NULL ), 
( NULL, '{ts escape="sql"}OptionLists{/ts}', '{ts escape="sql"}OptionLists{/ts}', '', NULL, '', '64', '1', NULL ), 
( 'civicrm/admin/options/activity_type&group=activity_type', '{ts escape="sql"}ActivityTypes{/ts}', '{ts escape="sql"}ActivityTypes{/ts}', '', NULL, '', '79', '1', NULL ), 
( 'civicrm/admin/options/gender&group=gender', '{ts escape="sql"}GenderOptions{/ts}', '{ts escape="sql"}GenderOptions{/ts}', '', NULL, '', '79', '1', NULL ), 
( NULL, '{ts escape="sql"}Help{/ts}', '{ts escape="sql"}Help{/ts}', '', NULL, '', NULL, '1', NULL ), 
( NULL, '{ts escape="sql"}Documentation{/ts}', '{ts escape="sql"}Documentation{/ts}', 'http://documentation.civicrm.org', NULL, 'AND', '82', '1', NULL ), 
( NULL, '{ts escape="sql"}Forums{/ts}', '{ts escape="sql"}Forums{/ts}', 'http://forum.civicrm.org', NULL, 'AND', '82', '1', NULL ), 
( NULL, '{ts escape="sql"}Participate{/ts}', '{ts escape="sql"}Participate{/ts}', 'http://civicrm.org/participate', NULL, 'AND', '82', '1', NULL ), 
( NULL, '{ts escape="sql"}About{/ts}', '{ts escape="sql"}About{/ts}', 'http://civicrm.org/aboutcivicrm', NULL, 'AND', '82', '1', NULL );

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
ALTER TABLE `civicrm_option_value` MODIFY `name` VARCHAR(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Stores a fixed (non-translated) name for this option value. Lookup functions should use the name as the key for the option value row.'
