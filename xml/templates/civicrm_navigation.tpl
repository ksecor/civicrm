-- Navigation Menu and Preferences

SELECT @domainID := id FROM civicrm_domain where name = 'Default Domain Name';

-- Initial default state of system preferences
{literal}
INSERT INTO 
     civicrm_preferences(domain_id, contact_id, is_domain, contact_view_options, contact_edit_options, advanced_search_options, user_dashboard_options, address_options, address_format, mailing_format, address_standardization_provider, address_standardization_userid, address_standardization_url, editor_id, mailing_backend, contact_autocomplete_options )
VALUES 
     (@domainID,NULL,1,'123456789101113','12345678910','1234567891011121315161718','1234578','1234568910111314','{contact.address_name}\n{contact.street_address}\n{contact.supplemental_address_1}\n{contact.supplemental_address_2}\n{contact.city}{, }{contact.state_province}{ }{contact.postal_code}\n{contact.country}','{contact.addressee}\n{contact.street_address}\n{contact.supplemental_address_1}\n{contact.supplemental_address_2}\n{contact.city}{, }{contact.state_province}{ }{contact.postal_code}\n{contact.country}',NULL,NULL,NULL,2,NULL,'12');
{/literal}

-- navigation 

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES
    (  @domainID, NULL, '{ts escape="sql"}Search...{/ts}',  'Search...',    NULL, '',  NULL, '1', NULL, 1 );

SET @searchlastID:=LAST_INSERT_ID();
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/contact/search&reset=1',                          '{ts escape="sql"}Find Contacts{/ts}',      'Find Contacts', NULL, '',                      @searchlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/contact/search/advanced&reset=1',                 '{ts escape="sql"}Find Contacts - Advanced Search{/ts}', 'Find Contacts - Advanced Search', NULL, '', @searchlastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/contact/search/custom&csid=15&reset=1',           '{ts escape="sql"}Full-text Search{/ts}',   'Full-text Search', NULL, '',                   @searchlastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/contact/search/builder&reset=1',                  '{ts escape="sql"}Search Builder{/ts}',     'Search Builder', NULL, '',                     @searchlastID, '1', '1',  4 ), 
    ( @domainID, 'civicrm/case/search&reset=1',                             '{ts escape="sql"}Find Cases{/ts}',         'Find Cases', 'access CiviCase', '',            @searchlastID, '1', NULL, 5 ), 
    ( @domainID, 'civicrm/contribute/search&reset=1',                       '{ts escape="sql"}Find Contributions{/ts}', 'Find Contributions', 'access CiviContribute', '',  @searchlastID, '1', NULL, 6 ), 
    ( @domainID, 'civicrm/mailing&reset=1',                                 '{ts escape="sql"}Find Mailings{/ts}',      'Find Mailings', 'access CiviMail', '',         @searchlastID, '1', NULL, 7 ), 
    ( @domainID, 'civicrm/member/search&reset=1',                           '{ts escape="sql"}Find Members{/ts}',       'Find Members', 'access CiviMember', '',        @searchlastID, '1', NULL, 8 ), 
    ( @domainID, 'civicrm/event/search&reset=1',                            '{ts escape="sql"}Find Participants{/ts}',  'Find Participants',  'access CiviEvent', '',   @searchlastID, '1', NULL, 9 ), 
    ( @domainID, 'civicrm/pledge/search&reset=1',                           '{ts escape="sql"}Find Pledges{/ts}',       'Find Pledges', 'access CiviPledge', '',        @searchlastID, '1', 1,    10 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, 'civicrm/contact/search/custom/list&reset=1',              '{ts escape="sql"}Custom Searches...{/ts}', 'Custom Searches...', NULL, '',                 @searchlastID, '1', NULL, 11 );

SET @customSearchlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/contact/search/custom&reset=1&csid=8',            '{ts escape="sql"}Activity Search{/ts}',                  'Activity Search',                  NULL, '', @customSearchlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/contact/search/custom&reset=1&csid=11',           '{ts escape="sql"}Contacts by Date Added{/ts}',           'Contacts by Date Added',           NULL, '', @customSearchlastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/contact/search/custom&reset=1&csid=2',            '{ts escape="sql"}Contributors by Aggregate Totals{/ts}', 'Contributors by Aggregate Totals', NULL, '', @customSearchlastID, '1', NULL, 3 ),
    ( @domainID, 'civicrm/contact/search/custom&reset=1&csid=6',            '{ts escape="sql"}Proximity Search{/ts}',                 'Proximity Search',                 NULL, '', @customSearchlastID, '1', NULL, 4 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES 
    ( @domainID, NULL,  '{ts escape="sql"}Contacts{/ts}', 'Contacts', NULL, '', NULL, '1', NULL, 3 );

SET @contactlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES        
    ( @domainID, 'civicrm/contact/add&reset=1&ct=Individual',               '{ts escape="sql"}New Individual{/ts}',         'New Individual',       'add contacts',     '',             @contactlastID, '1', NULL,  1 ), 
    ( @domainID, 'civicrm/contact/add&reset=1&ct=Household',                '{ts escape="sql"}New Household{/ts}',          'New Household',        'add contacts',     '',             @contactlastID, '1', NULL,  2 ), 
    ( @domainID, 'civicrm/contact/add&reset=1&ct=Organization',             '{ts escape="sql"}New Organization{/ts}',       'New Organization',     'add contacts',     '',             @contactlastID, '1', 1,     3 ), 
    ( @domainID, 'civicrm/activity&reset=1&action=add&context=standalone',  '{ts escape="sql"}New Activity{/ts}',           'New Activity',         NULL,               '',             @contactlastID, '1', NULL,  4 ), 
    ( @domainID, 'civicrm/activity/add&atype=3&action=add&reset=1&context=standalone', '{ts escape="sql"}New Email{/ts}',   'New Email',            NULL,               '',             @contactlastID, '1', '1',   5 ), 
    ( @domainID, 'civicrm/import/contact&reset=1',                          '{ts escape="sql"}Import Contacts{/ts}',        'Import Contacts',      'import contacts',  '',             @contactlastID, '1', NULL,  6 ), 
    ( @domainID, 'civicrm/import/activity&reset=1',                         '{ts escape="sql"}Import Activities{/ts}',      'Import Activities',    'import contacts',  '',             @contactlastID, '1', '1',   7 ), 
    ( @domainID, 'civicrm/group/add&reset=1',                               '{ts escape="sql"}New Group{/ts}',              'New Group',            'edit groups',      '',             @contactlastID, '1', NULL,  8 ), 
    ( @domainID, 'civicrm/group&reset=1',                                   '{ts escape="sql"}Manage Groups{/ts}',          'Manage Groups',        'edit groups,administer CiviCRM', 'AND', @contactlastID, '1', '1', 9 ), 
    ( @domainID, 'civicrm/admin/tag&reset=1&action=add',                    '{ts escape="sql"}New Tag{/ts}',                'New Tag',              'administer CiviCRM', '',           @contactlastID, '1', NULL, 10 ), 
    ( @domainID, 'civicrm/admin/tag&reset=1',                               '{ts escape="sql"}Manage Tags (Categories){/ts}', 'Manage Tags (Categories)', 'administer CiviCRM', '',     @contactlastID, '1', NULL, 11 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL,  '{ts escape="sql"}Contributions{/ts}', 'Contributions', 'access CiviContribute', '',      NULL,           '1', NULL,  4 );

SET @contributionlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, 'civicrm/contribute&reset=1',                              '{ts escape="sql"}Dashboard{/ts}',              'Dashboard',              'access CiviContribute', '', @contributionlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/contribute/add&reset=1&action=add&context=standalone', '{ts escape="sql"}New Contribution{/ts}',  'New Contribution',       'access CiviContribute,edit contributions', 'AND', @contributionlastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/contribute/search&reset=1',                       '{ts escape="sql"}Find Contributions{/ts}',     'Find Contributions',     'access CiviContribute', '', @contributionlastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/contribute/import&reset=1',                       '{ts escape="sql"}Import Contributions{/ts}',   'Import Contributions',   'access CiviContribute,edit contributions', 'AND', @contributionlastID, '1', '1',  4 );
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES 
    ( @domainID,NULL, '{ts escape="sql"}Pledges{/ts}',  'Pledges', 'access CiviPledge', '', @contributionlastID, '1',  1,   5 );
    
SET @pledgelastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/pledge&reset=1',                                  '{ts escape="sql"}Dashboard{/ts}',                  'Dashboard',                 'access CiviPledge',  '',  @pledgelastID,       '1', NULL, 1 ), 
    ( @domainID, 'civicrm/pledge/add&reset=1&action=add&context=standalone', '{ts escape="sql"}New Pledge{/ts}',                'New Pledge',                'access CiviPledge,edit pledges',  'AND',  @pledgelastID,       '1', NULL, 2 ),
    ( @domainID, 'civicrm/pledge/search&reset=1',                           '{ts escape="sql"}Find Pledges{/ts}',               'Find Pledges',              'access CiviPledge',  '',  @pledgelastID,       '1', NULL, 3 ), 
    ( @domainID, 'civicrm/admin/contribute&reset=1&action=add',             '{ts escape="sql"}New Contribution Page{/ts}',      'New Contribution Page',     'access CiviContribute,administer CiviCRM', 'AND',  @contributionlastID, '1', NULL, 6 ), 
    ( @domainID, 'civicrm/admin/contribute&reset=1',                        '{ts escape="sql"}Manage Contribution Pages{/ts}',  'Manage Contribution Pages', 'access CiviContribute,administer CiviCRM', 'AND',  @contributionlastID, '1', '1',  7 ), 
    ( @domainID, 'civicrm/admin/pcp&reset=1',                               '{ts escape="sql"}Personal Campaign Pages{/ts}',    'Personal Campaign Pages',   'access CiviContribute,administer CiviCRM', 'AND',  @contributionlastID, '1', NULL, 8 ), 
    ( @domainID, 'civicrm/admin/contribute/managePremiums&reset=1',         '{ts escape="sql"}Premiums (Thank-you Gifts){/ts}', 'Premiums',                  'access CiviContribute,administer CiviCRM', 'AND',  @contributionlastID, '1', NULL, 9 );
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}Events{/ts}',  'Events', 'access CiviEvent', '', NULL, '1', NULL, 5 );

SET @eventlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/event&reset=1',                                   '{ts escape="sql"}Dashboard{/ts}',          'CiviEvent Dashboard',  'access CiviEvent', '',    @eventlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/participant/add&reset=1&action=add&context=standalone', '{ts escape="sql"}Register Event Participant{/ts}', 'Register Event Participant', 'access CiviEvent,edit event participants', 'AND', @eventlastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/event/search&reset=1',                            '{ts escape="sql"}Find Participants{/ts}',  'Find Participants',    'access CiviEvent', '',    @eventlastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/event/import&reset=1',                            '{ts escape="sql"}Import Participants{/ts}','Import Participants',  'access CiviEvent,edit event participants', 'AND',    @eventlastID, '1', '1',  4 ), 
    ( @domainID, 'civicrm/event/add&reset=1&action=add',                    '{ts escape="sql"}New Event{/ts}',          'New Event',            'access CiviEvent,administer CiviCRM', 'AND',    @eventlastID, '1', NULL, 5 ), 
    ( @domainID, 'civicrm/event/manage&reset=1',                            '{ts escape="sql"}Manage Events{/ts}',      'Manage Events',        'access CiviEvent,administer CiviCRM', 'AND',    @eventlastID, '1', 1, 6 ), 
    ( @domainID, 'civicrm/admin/eventTemplate&reset=1',                     '{ts escape="sql"}Event Templates{/ts}',    'Event Templates',      'access CiviEvent,administer CiviCRM', 'AND',    @eventlastID, '1', 1, 7 ), 
    ( @domainID, 'civicrm/admin/price&reset=1&action=add',                  '{ts escape="sql"}New Price Set{/ts}',      'New Price Set',        'access CiviEvent,administer CiviCRM', 'AND',    @eventlastID, '1', NULL, 8 ), 
    ( @domainID, 'civicrm/event/price&reset=1',                             '{ts escape="sql"}Manage Price Sets{/ts}',  'Manage Price Sets',    'access CiviEvent,administer CiviCRM', 'AND',    @eventlastID, '1', NULL, 9 );
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES 
    ( @domainID, NULL, '{ts escape="sql"}Mailings{/ts}', 'Mailings', 'access CiviMail', '', NULL, '1', NULL, 6 );

SET @mailinglastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/mailing/send&reset=1',                            '{ts escape="sql"}New Mailing{/ts}', 'New Mailing',                                          'access CiviMail', '', @mailinglastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/mailing/browse/unscheduled&reset=1&scheduled=false', '{ts escape="sql"}Draft and Unscheduled Mailings{/ts}', 'Draft and Unscheduled Mailings', 'access CiviMail', '', @mailinglastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/mailing/browse/scheduled&reset=1&scheduled=true', '{ts escape="sql"}Scheduled and Sent Mailings{/ts}', 'Scheduled and Sent Mailings',          'access CiviMail', '', @mailinglastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/mailing/browse/archived&reset=1',                 '{ts escape="sql"}Archived Mailings{/ts}', 'Archived Mailings',                              'access CiviMail', '', @mailinglastID, '1', 1,    4 ), 
    ( @domainID, 'civicrm/admin/component&reset=1',                         '{ts escape="sql"}Headers, Footers, and Automated Messages{/ts}', 'Headers, Footers, and Automated Messages', 'access CiviMail,administer CiviCRM', 'AND', @mailinglastID, '1', NULL, 5 ),
    ( @domainID, 'civicrm/admin/messageTemplates&reset=1',                  '{ts escape="sql"}Message Templates{/ts}', 'Message Templates',                 'administer CiviCRM', '', @mailinglastID, '1', NULL, 6 ),
    ( @domainID, 'civicrm/admin/options/from_email&group=from_email_address&reset=1', '{ts escape="sql"}From Email Addresses{/ts}', 'From Email Addresses', 'administer CiviCRM', '', @mailinglastID, '1', NULL, 7 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES 
    ( @domainID, NULL, '{ts escape="sql"}Memberships{/ts}', 'Memberships', 'access CiviMember', '', NULL, '1', NULL, 7 );

SET @memberlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/member&reset=1',                              '{ts escape="sql"}Dashboard{/ts}',           'Dashboard',       'access CiviMember', '', @memberlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/member/add&reset=1&action=add&context=standalone', '{ts escape="sql"}New Membership{/ts}', 'New Membership',  'access CiviMember,edit memberships', 'AND', @memberlastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/member/search&reset=1',                       '{ts escape="sql"}Find Members{/ts}',        'Find Members',    'access CiviMember', '', @memberlastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/member/import&reset=1',                       '{ts escape="sql"}Import Members{/ts}',      'Import Members',  'access CiviMember,edit memberships', 'AND', @memberlastID, '1', NULL, 4 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}Other{/ts}', 'Other', 'access CiviGrant,access CiviCase', 'OR', NULL, '1', NULL, 9 );
    
SET @otherlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES
    ( @domainID, NULL, '{ts escape="sql"}Cases{/ts}', 'Cases', 'access CiviCase', '', @otherlastID, '1', NULL, 1 );

SET @caselastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/case&reset=1',        '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', 'access CiviCase', '',       @caselastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/case/add&reset=1&action=add&atype=13&context=standalone', '{ts escape="sql"}New Case{/ts}', 'New Case', 'access CiviCase,add contacts', 'AND', @caselastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/case/search&reset=1', '{ts escape="sql"}Find Cases{/ts}', 'Find Cases', 'access CiviCase', '',     @caselastID, '1', 1, 3 );
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}Grants{/ts}', 'Grants', 'access CiviGrant', '', @otherlastID, '1', NULL, 2 );

SET @grantlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES        
    ( @domainID, 'civicrm/grant&reset=1',           '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', 'access CiviGrant', '',       @grantlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/grant/add&reset=1&action=add&context=standalone', '{ts escape="sql"}New Grant{/ts}', 'New Grant', 'access CiviGrant,edit grants', 'AND', @grantlastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/grant/search&reset=1',    '{ts escape="sql"}Find Grants{/ts}', 'Find Grants', 'access CiviGrant', '',   @grantlastID, '1', 1, 3 );
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES 
    ( @domainID, NULL, '{ts escape="sql"}Administer{/ts}', 'Administer', 'administer CiviCRM', '', NULL, '1', NULL, 10 );

SET @adminlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/admin&reset=1', '{ts escape="sql"}Administration Console{/ts}', 'Administration Console', 'administer CiviCRM', '', @adminlastID, '1', NULL, 1 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}Customize{/ts}', 'Customize', 'administer CiviCRM', '', @adminlastID, '1', NULL, 2 );

SET @CustomizelastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES            
    ( @domainID, 'civicrm/admin/custom/group&reset=1',      '{ts escape="sql"}Custom Data{/ts}',     'Custom Data',     'administer CiviCRM', '', @CustomizelastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/admin/uf/group&reset=1',          '{ts escape="sql"}CiviCRM Profile{/ts}', 'CiviCRM Profile', 'administer CiviCRM', '', @CustomizelastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/admin/menu&reset=1',              '{ts escape="sql"}Navigation Menu{/ts}', 'Navigation Menu', 'administer CiviCRM', '', @CustomizelastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/admin/options/custom_search&reset=1&group=custom_search', '{ts escape="sql"}Manage Custom Searches{/ts}', 'Manage Custom Searches', 'administer CiviCRM', '', @CustomizelastID, '1', NULL, 4 );
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}Configure{/ts}', 'Configure', 'administer CiviCRM', '', @adminlastID, '1', NULL, 3 );

SET @configurelastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/admin/configtask&reset=1',                    '{ts escape="sql"}Configuration Checklist{/ts}', 'Configuration Checklist', 'administer CiviCRM', '', @configurelastID, '1', NULL, 1 );
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, 'civicrm/admin/setting&reset=1',                       '{ts escape="sql"}Global Settings{/ts}',         'Global Settings',         'administer CiviCRM', '', @configurelastID, '1', NULL, 2 );

SET @globalSettinglastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/admin/setting/component&reset=1',             '{ts escape="sql"}Enable CiviCRM Components{/ts}', 'Enable Components', 'administer CiviCRM', '',   @globalSettinglastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/admin/setting/preferences/display&reset=1',   '{ts escape="sql"}Site Preferences (screen and form configuration){/ts}', 'Site Preferences', 'administer CiviCRM', '', @globalSettinglastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/admin/setting/path&reset=1',                  '{ts escape="sql"}Directories{/ts}',        'Directories',      'administer CiviCRM', '',        @globalSettinglastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/admin/setting/url&reset=1',                   '{ts escape="sql"}Resource URLs{/ts}',      'Resource URLs',    'administer CiviCRM', '',      @globalSettinglastID, '1', NULL, 4 ), 
    ( @domainID, 'civicrm/admin/setting/smtp&reset=1',                  '{ts escape="sql"}Outbound Email (SMTP/Sendmail){/ts}', 'Outbound Email', 'administer CiviCRM', '', @globalSettinglastID, '1', NULL, 5 ), 
    ( @domainID, 'civicrm/admin/setting/mapping&reset=1',               '{ts escape="sql"}Mapping and Geocoding{/ts}', 'Mapping and Geocoding',   'administer CiviCRM', '', @globalSettinglastID, '1', NULL, 6 ), 
    ( @domainID, 'civicrm/admin/paymentProcessor&reset=1',              '{ts escape="sql"}Payment Processors{/ts}', 'Payment Processors', 'administer CiviCRM', '', @globalSettinglastID, '1', NULL, 7  ), 
    ( @domainID, 'civicrm/admin/setting/localization&reset=1',          '{ts escape="sql"}Localization{/ts}',       'Localization',     'administer CiviCRM', '',       @globalSettinglastID, '1', NULL, 8  ), 
    ( @domainID, 'civicrm/admin/setting/preferences/address&reset=1',   '{ts escape="sql"}Address Settings{/ts}',   'Address Settings', 'administer CiviCRM', '',   @globalSettinglastID, '1', NULL, 9  ), 
    ( @domainID, 'civicrm/admin/setting/search&reset=1',                '{ts escape="sql"}Search Settings{/ts}',    'Search Settings',  'administer CiviCRM', '',    @globalSettinglastID, '1', NULL, 10 ), 
    ( @domainID, 'civicrm/admin/setting/date&reset=1',                  '{ts escape="sql"}Date Formats{/ts}',       'Date Formats',     'administer CiviCRM', '',       @globalSettinglastID, '1', NULL, 11 ), 
    ( @domainID, 'civicrm/admin/setting/uf&reset=1',                    '{ts escape="sql"}CMS Integration{/ts}',    'CMS Integration',  'administer CiviCRM', '',    @globalSettinglastID, '1', NULL, 12 ), 
    ( @domainID, 'civicrm/admin/setting/misc&reset=1',                  '{ts escape="sql"}Miscellaneous (version check, reCAPTCHA...){/ts}', 'Miscellaneous', 'administer CiviCRM', '', @globalSettinglastID, '1', NULL, 13 ), 
    ( @domainID, 'civicrm/admin/options/safe_file_extension&group=safe_file_extension&reset=1', '{ts escape="sql"}Safe File Extensions{/ts}', 'Safe File Extensions', 'administer CiviCRM', '', @globalSettinglastID, '1', NULL, 14 ), 
    ( @domainID, 'civicrm/admin/setting/debug&reset=1',                 '{ts escape="sql"}Debugging{/ts}',      'Debugging', 'administer CiviCRM', '',              @globalSettinglastID, '1', NULL, 15 ), 
    
    ( @domainID, 'civicrm/admin/mapping&reset=1',                      '{ts escape="sql"}Import/Export Mappings{/ts}', 'Import/Export Mappings',   'administer CiviCRM', '', @configurelastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/admin/messageTemplates&reset=1',             '{ts escape="sql"}Message Templates{/ts}',      'Message Templates',        'administer CiviCRM', '', @configurelastID, '1', NULL, 4 ), 
    ( @domainID, 'civicrm/admin/domain&action=update&reset=1',         '{ts escape="sql"}Domain Information{/ts}',     'Domain Information',       'administer CiviCRM', '', @configurelastID, '1', NULL, 5 ), 
    ( @domainID, 'civicrm/admin/options/from_email_address&group=from_email_address&reset=1', '{ts escape="sql"}FROM Email Addresses{/ts}', 'FROM Email Addresses',    'administer CiviCRM', '', @configurelastID, '1', NULL, 6 ), 
    ( @domainID, 'civicrm/admin/setting/updateConfigBackend&reset=1',  '{ts escape="sql"}Update Directory Path and URL{/ts}', 'Update Directory Path and URL',         'administer CiviCRM', '', @configurelastID, '1', NULL, 7 );
    
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES         
    ( @domainID, NULL, '{ts escape="sql"}Manage{/ts}', 'Manage', 'administer CiviCRM', '', @adminlastID, '1', NULL, 4 );

SET @managelastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, 'civicrm/admin/deduperules&reset=1',  '{ts escape="sql"}Find and Merge Duplicate Contacts{/ts}', 'Find and Merge Duplicate Contacts', 'administer CiviCRM', '', @managelastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/admin/access&reset=1',       '{ts escape="sql"}Access Control{/ts}',                    'Access Control',                    'administer CiviCRM', '', @managelastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/admin/synchUser&reset=1',    '{ts escape="sql"}Synchronize Users to Contacts{/ts}',     'Synchronize Users to Contacts',     'administer CiviCRM', '', @managelastID, '1', NULL, 3 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}Option Lists{/ts}', 'Option Lists', 'administer CiviCRM', '', @adminlastID, '1', NULL, 5 );
    
SET @optionListlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES         
    ( @domainID, 'civicrm/admin/options/activity_type&reset=1&group=activity_type',                                    '{ts escape="sql"}Activity Types{/ts}',         'Activity Types',                           'administer CiviCRM', '',   @optionListlastID, '1', NULL,  1 ), 
    ( @domainID, 'civicrm/admin/reltype&reset=1',                                                                      '{ts escape="sql"}Relationship Types{/ts}',     'Relationship Types',                       'administer CiviCRM', '',   @optionListlastID, '1', NULL,  2 ), 
    ( @domainID, 'civicrm/admin/tag&reset=1',                                                                          '{ts escape="sql"}Tags (Categories){/ts}',      'Tags (Categories)',                        'administer CiviCRM', '',   @optionListlastID, '1', 1,     3 ), 
    ( @domainID, 'civicrm/admin/options/gender&reset=1&group=gender',                                                  '{ts escape="sql"}Gender Options{/ts}',         'Gender Options',                           'administer CiviCRM', '',   @optionListlastID, '1', NULL,  4 ), 
    ( @domainID, 'civicrm/admin/options/individual_prefix&group=individual_prefix&reset=1',                            '{ts escape="sql"}Individual Prefixes (Ms, Mr...){/ts}', 'Individual Prefixes (Ms, Mr...)', 'administer CiviCRM', '',   @optionListlastID, '1', NULL,  5 ), 
    ( @domainID, 'civicrm/admin/options/individual_suffix&group=individual_suffix&reset=1',                            '{ts escape="sql"}Individual Suffixes (Jr, Sr...){/ts}', 'Individual Suffixes (Jr, Sr...)', 'administer CiviCRM', '',   @optionListlastID, '1', 1,     6 ), 
    ( @domainID, 'civicrm/admin/options/addressee&group=addressee&reset=1',                                            '{ts escape="sql"}Addressee Formats{/ts}',      'Addressee Formats',                        'administer CiviCRM', '',   @optionListlastID, '1', NULL,  7 ), 
    ( @domainID, 'civicrm/admin/options/email_greeting&group=email_greeting&reset=1',                                  '{ts escape="sql"}Email Greetings{/ts}',        'Email Greetings',                          'administer CiviCRM', '',   @optionListlastID, '1', NULL,  8 ), 
    ( @domainID, 'civicrm/admin/options/postal_greeting&group=postal_greeting&reset=1',                                '{ts escape="sql"}Postal Greetings{/ts}',       'Postal Greetings',                         'administer CiviCRM', '',   @optionListlastID, '1', 1,     9 ), 
    ( @domainID, 'civicrm/admin/options/instant_messenger_service&group=instant_messenger_service&reset=1',            '{ts escape="sql"}Instant Messenger Services{/ts}',     'Instant Messenger Services',       'administer CiviCRM', '',   @optionListlastID, '1', NULL, 10 ), 
    ( @domainID, 'civicrm/admin/locationType&reset=1',                                                                 '{ts escape="sql"}Location Types (Home, Work...){/ts}', 'Location Types (Home, Work...)',   'administer CiviCRM', '',   @optionListlastID, '1', NULL, 11 ), 
    ( @domainID, 'civicrm/admin/options/mobile_provider&group=mobile_provider&reset=1',                                '{ts escape="sql"}Mobile Phone Providers{/ts}', 'Mobile Phone Providers',                   'administer CiviCRM', '',   @optionListlastID, '1', NULL, 12 ), 
    ( @domainID, 'civicrm/admin/options/phone_type&group=phone_type&reset=1',                                          '{ts escape="sql"}Phone Types{/ts}',            'Phone Types',                              'administer CiviCRM', '',   @optionListlastID, '1', NULL, 13 ), 
    ( @domainID, 'civicrm/admin/options/preferred_communication_method&group=preferred_communication_method&reset=1','{ts escape="sql"}Preferred Communication Methods{/ts}', 'Preferred Communication Methods',   'administer CiviCRM', '',   @optionListlastID, '1', NULL, 14 ),
    ( @domainID, 'civicrm/admin/options/subtype&reset=1',                                                              '{ts escape="sql"}Contact Types{/ts}',       'Contact Types',                         'administer CiviCRM', '',   @optionListlastID, '1', NULL, 15 ); 

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL,  '{ts escape="sql"}CiviCase{/ts}', 'CiviCase', 'access CiviCase,administer CiviCRM', 'AND', @adminlastID, '1', NULL, 6 );

SET @adminCaselastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/admin/options/case_type&group=case_type&reset=1',            '{ts escape="sql"}Case Types{/ts}',      'Case Types',      'access CiviCase,administer CiviCRM', 'AND', @adminCaselastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/admin/options/redaction_rule&group=redaction_rule&reset=1',  '{ts escape="sql"}Redaction Rules{/ts}', 'Redaction Rules', 'access CiviCase,administer CiviCRM', 'AND', @adminCaselastID, '1', NULL, 2 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES 
    ( @domainID, NULL,  '{ts escape="sql"}CiviContribute{/ts}', 'CiviContribute', 'access CiviContribute,administer CiviCRM', 'AND', @adminlastID, '1', NULL, 7 );
    
SET @adminContributelastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES
    ( @domainID, 'civicrm/admin/contribute&reset=1&action=add',            '{ts escape="sql"}New Contribution Page{/ts}',      'New Contribution Page',     'access CiviContribute,administer CiviCRM', 'AND', @adminContributelastID, '1', NULL, 6 ), 
    ( @domainID, 'civicrm/admin/contribute&reset=1',                       '{ts escape="sql"}Manage Contribution Pages{/ts}',  'Manage Contribution Pages', 'access CiviContribute,administer CiviCRM', 'AND', @adminContributelastID, '1', '1',  7 ), 
    ( @domainID, 'civicrm/admin/pcp&reset=1',                              '{ts escape="sql"}Personal Campaign Pages{/ts}',    'Personal Campaign Pages',   'access CiviContribute,administer CiviCRM', 'AND', @adminContributelastID, '1', NULL, 8 ), 
    ( @domainID, 'civicrm/admin/contribute/managePremiums&reset=1',        '{ts escape="sql"}Premiums (Thank-you Gifts){/ts}', 'Premiums',                  'access CiviContribute,administer CiviCRM', 'AND', @adminContributelastID, '1', 1,    9 ), 
    ( @domainID, 'civicrm/admin/contribute/contributionType&reset=1',      '{ts escape="sql"}Contribution Types{/ts}',         'Contribution Types',        'access CiviContribute,administer CiviCRM', 'AND', @adminContributelastID, '1', NULL, 10), 
    ( @domainID, 'civicrm/admin/options/payment_instrument&group=payment_instrument&reset=1',  '{ts escape="sql"}Payment Instruments{/ts}',    'Payment Instruments',   'access CiviContribute,administer CiviCRM', 'AND', @adminContributelastID, '1', NULL, 11 ), 
    ( @domainID, 'civicrm/admin/options/accept_creditcard&group=accept_creditcard&reset=1',    '{ts escape="sql"}Accepted Credit Cards{/ts}',  'Accepted Credit Cards', 'access CiviContribute,administer CiviCRM', 'AND', @adminContributelastID, '1', NULL, 12 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES         
    ( @domainID, NULL, '{ts escape="sql"}CiviEvent{/ts}', 'CiviEvent', 'access CiviEvent,administer CiviCRM', 'AND', @adminlastID, '1', NULL, 8 );

SET @adminEventlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/event/add&reset=1&action=add',                   '{ts escape="sql"}New Event{/ts}',          'New Event',                        'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/event/manage&reset=1',                           '{ts escape="sql"}Manage Events{/ts}',      'Manage Events',                    'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', 1,    2 ), 
    ( @domainID, 'civicrm/admin/eventTemplate&reset=1',                    '{ts escape="sql"}Event Templates{/ts}',    'Event Templates',                  'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', 1,    3 ), 
    ( @domainID, 'civicrm/admin/price&reset=1&action=add',                 '{ts escape="sql"}New Price Set{/ts}',      'New Price Set',                    'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', NULL, 4 ), 
    ( @domainID, 'civicrm/event/price&reset=1',                            '{ts escape="sql"}Manage Price Sets{/ts}',  'Manage Price Sets',                'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', 1,    5 ),
    ( @domainID, 'civicrm/admin/options/participant_listing&group=participant_listing&reset=1', '{ts escape="sql"}Participant Listing Templates{/ts}', 'Participant Listing Templates', 'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', NULL, 6 ), 
    ( @domainID, 'civicrm/admin/options/event_type&group=event_type&reset=1',  '{ts escape="sql"}Event Types{/ts}',    'Event Types',                      'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', NULL, 7 ), 
    ( @domainID, 'civicrm/admin/participant_status&reset=1',                   '{ts escape="sql"}Participant Statuses{/ts}', 'Participant Statuses',       'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', NULL, 8 ), 
    ( @domainID, 'civicrm/admin/options/participant_role&group=participant_role&reset=1', '{ts escape="sql"}Participant Roles{/ts}', 'Participant Roles',  'access CiviEvent,administer CiviCRM', 'AND', @adminEventlastID, '1', NULL, 9 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}CiviGrant{/ts}', 'CiviGrant', 'access CiviGrant,administer CiviCRM', 'AND', @adminlastID, '1', NULL, 9 );

SET @adminGrantlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, 'civicrm/admin/options/grant_type&group=grant_type&reset=1', '{ts escape="sql"}Grant Types{/ts}', 'Grant Types', 'access CiviGrant,administer CiviCRM', 'AND', @adminGrantlastID, '1', NULL, 1 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}CiviMail{/ts}', 'CiviMail', 'access CiviMail,administer CiviCRM', 'AND', @adminlastID, '1', NULL, 10 );

SET @adminMailinglastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/admin/component&reset=1',            '{ts escape="sql"}Headers, Footers, and Automated Messages{/ts}', 'Headers, Footers, and Automated Messages', 'access CiviMail,administer CiviCRM', 'AND', @adminMailinglastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/admin/messageTemplates&reset=1',     '{ts escape="sql"}Message Templates{/ts}', 'Message Templates', 'administer CiviCRM', '',   @adminMailinglastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/admin/options/from_email&group=from_email_address&reset=1', '{ts escape="sql"}From Email Addresses{/ts}', 'From Email Addresses', 'administer CiviCRM', '', @adminMailinglastID, '1', NULL, 3 ), 
    ( @domainID, 'civicrm/admin/mailSettings&reset=1',         '{ts escape="sql"}Mail Accounts{/ts}', 'Mail Accounts', 'access CiviMail,administer CiviCRM', 'AND',           @adminMailinglastID, '1', NULL, 4 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}CiviMember{/ts}', 'CiviMember', 'access CiviMember,administer CiviCRM', 'AND', @adminlastID, '1', NULL, 11 );

SET @adminMemberlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, 'civicrm/admin/member/membershipType&reset=1',    '{ts escape="sql"}Membership Types{/ts}',        'Membership Types',        'access CiviMember,administer CiviCRM', 'AND', @adminMemberlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/admin/member/membershipStatus&reset=1',  '{ts escape="sql"}Membership Status Rules{/ts}', 'Membership Status Rules', 'access CiviMember,administer CiviCRM', 'AND', @adminMemberlastID, '1', NULL, 2 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL,                                             '{ts escape="sql"}CiviReport{/ts}',              'CiviReport',              'access CiviReport,administer CiviCRM', 'AND', @adminlastID, '1', NULL, 12 );

SET @adminReportlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, 'civicrm/report/list&reset=1',                            '{ts escape="sql"}Reports Listing{/ts}',  'Reports Listing', 'access CiviReport',    '', @adminReportlastID, '1', NULL, 1 ), 
    ( @domainID, 'civicrm/admin/report/template/list&reset=1',             '{ts escape="sql"}Create Reports from Templates{/ts}', 'Create Reports from Templates', 'administer Reports', '', @adminReportlastID, '1', NULL, 2 ), 
    ( @domainID, 'civicrm/admin/report/options/report_template&reset=1',   '{ts escape="sql"}Manage Templates{/ts}', 'Manage Templates', 'administer Reports',  '', @adminReportlastID, '1', NULL, 3 ),
    ( @domainID, 'civicrm/admin/report/register&reset=1',                  '{ts escape="sql"}Register Report{/ts}',  'Register Report',  'administer Reports',  '', @adminReportlastID, '1', NULL, 4 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}Help{/ts}', 'Help', NULL, '',  NULL, '1', NULL, 11);

SET @adminHelplastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'http://documentation.civicrm.org',   '{ts escape="sql"}Documentation{/ts}',      'Documentation',    NULL, 'AND', @adminHelplastID, '1', NULL, 1 ), 
    ( @domainID, 'http://forum.civicrm.org',           '{ts escape="sql"}Community Forums{/ts}',   'Community Forums', NULL, 'AND', @adminHelplastID, '1', NULL, 2 ), 
    ( @domainID, 'http://civicrm.org/participate',     '{ts escape="sql"}Participate{/ts}',        'Participate',      NULL, 'AND', @adminHelplastID, '1', NULL, 3 ), 
    ( @domainID, 'http://civicrm.org/aboutcivicrm',    '{ts escape="sql"}About{/ts}',              'About',            NULL, 'AND', @adminHelplastID, '1', NULL, 4 );

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES     
    ( @domainID, NULL, '{ts escape="sql"}Reports{/ts}', 'Reports', 'access CiviReport', '',  NULL, '1', NULL, 8 );

SET @reportlastID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/report/list&reset=1',                 '{ts escape="sql"}Reports Listing{/ts}',                'Reports Listing',                 'access CiviReport', '', @reportlastID, '1', 1,    1 ), 
    ( @domainID, 'civicrm/admin/report/template/list&reset=1',  '{ts escape="sql"}Create Reports from Templates{/ts}',  'Create Reports from Templates', 'administer Reports',  '', @reportlastID, '1', 1,    2 ), 
    
    ( @domainID, 'civicrm/report/instance/1&reset=1',     '{ts escape="sql"}Constituent Report (Summary){/ts}',       '{literal}Constituent Report (Summary){/literal}',     'administer CiviCRM',       '',  @reportlastID,  '1', NULL, 3 ),
    ( @domainID, 'civicrm/report/instance/2&reset=1',     '{ts escape="sql"}Constituent Report (Detail){/ts}',        '{literal}Constituent Report (Detail){/literal}',      'administer CiviCRM',       '',  @reportlastID,  '1', NULL, 4 ),
    ( @domainID, 'civicrm/report/instance/3&reset=1',     '{ts escape="sql"}Donor Report (Summary){/ts}',             '{literal}Donor Report (Summary){/literal}',           'access CiviContribute',    '',  @reportlastID,  '1', NULL, 5 ),
    ( @domainID, 'civicrm/report/instance/4&reset=1',     '{ts escape="sql"}Donor Report (Detail){/ts}',              '{literal}Donor Report (Detail){/literal}',            'access CiviContribute',    '',  @reportlastID,  '1', NULL, 6 ),
    ( @domainID, 'civicrm/report/instance/5&reset=1',     '{ts escape="sql"}Donation Summary Report (Repeat){/ts}',   '{literal}Donation Summary Report (Repeat){/literal}', 'access CiviContribute',    '',  @reportlastID,  '1', NULL, 7 ),
    ( @domainID, 'civicrm/report/instance/6&reset=1',     '{ts escape="sql"}SYBUNT Report{/ts}',                      'SYBUNT Report',                                       'access CiviContribute',    '',  @reportlastID,  '1', NULL, 8 ),
    ( @domainID, 'civicrm/report/instance/7&reset=1',     '{ts escape="sql"}LYBUNT Report{/ts}',                      'LYBUNT Report',                                       'access CiviContribute',    '',  @reportlastID,  '1', NULL, 9 ),
    ( @domainID, 'civicrm/report/instance/8&reset=1',     '{ts escape="sql"}Soft Credit Report{/ts}',                 'Soft Credit Report',                                  'access CiviContribute',    '',  @reportlastID,  '1', NULL, 10 ),
    ( @domainID, 'civicrm/report/instance/9&reset=1',     '{ts escape="sql"}Membership Report (Summary){/ts}',        '{literal}Membership Report (Summary){/literal}',      'access CiviMember',        '',  @reportlastID,  '1', NULL, 11 ),
    ( @domainID, 'civicrm/report/instance/10&reset=1',    '{ts escape="sql"}Membership Report (Detail){/ts}',         '{literal}Membership Report (Detail){/literal}',       'access CiviMember',        '',  @reportlastID,  '1', NULL, 12 ),
    ( @domainID, 'civicrm/report/instance/11&reset=1',    '{ts escape="sql"}Membership Report (Lapsed){/ts}',         '{literal}Membership Report (Lapsed){/literal}',       'access CiviMember',        '',  @reportlastID,  '1', NULL, 13 ),
    ( @domainID, 'civicrm/report/instance/12&reset=1',    '{ts escape="sql"}Event Participant Report (List){/ts}',    '{literal}Event Participant Report (List){/literal}',  'access CiviEvent',         '',  @reportlastID,  '1', NULL, 14 ),
    ( @domainID, 'civicrm/report/instance/13&reset=1',    '{ts escape="sql"}Event Income Report (Summary){/ts}',      '{literal}Event Income Report (Summary){/literal}',    'access CiviEvent',         '',  @reportlastID,  '1', NULL, 15 ),
    ( @domainID, 'civicrm/report/instance/14&reset=1',    '{ts escape="sql"}Event Income Report (Detail){/ts}',       '{literal}Event Income Report (Detail){/literal}',     'access CiviEvent',         '',  @reportlastID,  '1', NULL, 16 ),
    ( @domainID, 'civicrm/report/instance/15&reset=1',    '{ts escape="sql"}Attendee List{/ts}',                      'Attendee List',                                       'access CiviEvent',         '',  @reportlastID,  '1', NULL, 17 ),    
    ( @domainID, 'civicrm/report/instance/16&reset=1',    '{ts escape="sql"}Activity Report{/ts}',                    'activity',                                            'administer CiviCRM',       '',  @reportlastID,  '1', NULL, 18 ),
    ( @domainID, 'civicrm/report/instance/17&reset=1',    '{ts escape="sql"}Relationship Report{/ts}',                'Relationship Report',                                 'administer CiviCRM',       '',  @reportlastID,  '1', NULL, 19 ),
    ( @domainID, 'civicrm/report/instance/18&reset=1',    '{ts escape="sql"}Donation Summary Report (Organization){/ts}', '{literal}Donation Summary Report (Organization){/literal}', 'access CiviContribute', '',  @reportlastID,  '1', NULL, 20 ),
    ( @domainID, 'civicrm/report/instance/19&reset=1',    '{ts escape="sql"}Donation Summary Report (Household){/ts}',    '{literal}Donation Summary Report (Household){/literal}',    'access CiviContribute', '',  @reportlastID,  '1', NULL, 21 ),
    ( @domainID, 'civicrm/report/instance/20&reset=1',    '{ts escape="sql"}Top Donors Report{/ts}',                  'Top Donors Report',            'access CiviContribute',   '',  @reportlastID,  '1', NULL, 22 ),    
    ( @domainID, 'civicrm/report/instance/21&reset=1',    '{ts escape="sql"}Pledge Summary Report{/ts}',              'Pledge Summary Report',        'access CiviPledge',       '',  @reportlastID,  '1', NULL, 23 ),
    ( @domainID, 'civicrm/report/instance/22&reset=1',    '{ts escape="sql"}Pledged But not Paid Report{/ts}',        'Pledged But not Paid Report',  'access CiviPledge',       '',  @reportlastID,  '1', NULL, 24 );
