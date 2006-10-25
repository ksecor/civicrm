-- Handles all domain-keyed data. Included in civicrm_data.tpl for base initialization (@domain_id = 1).
-- When invoked by itself, includes logic to insert data for next available domain_id.

{if $context EQ "baseData"}
    set @domain_id = {$civicrmDomainId};
{else}
    -- This syntax apparently doesn't work in 4.0 and some 4.1 versions
    -- select max(id) + 1 from civicrm_domain into @domain_id;
    SELECT @domain_id := max(id) + 1 from civicrm_domain;
{/if}

SET @domain_name := CONCAT('Domain Name ',@domain_id);

INSERT INTO civicrm_domain( id, name, contact_name, email_domain ) 
    VALUES ( @domain_id, @domain_name, 'Domain Contact Name', 'FIXME.ORG' );

-- Sample location types
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active, is_default ) VALUES( @domain_id, '{ts}Home{/ts}', 'HOME', '{ts}Place of residence{/ts}', 0, 1, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( @domain_id, '{ts}Work{/ts}', 'WORK', '{ts}Work location{/ts}', 0, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( @domain_id, '{ts}Main{/ts}', NULL, '{ts}Main office location{/ts}', 0, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( @domain_id, '{ts}Other{/ts}', NULL, '{ts}Other location{/ts}', 0, 1 );

-- Sample relationship types
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( @domain_id, '{ts}Child of{/ts}', '{ts}Parent of{/ts}', '{ts}Parent/child relationship.{/ts}', 'Individual', 'Individual', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( @domain_id, '{ts}Spouse of{/ts}', '{ts}Spouse of{/ts}', '{ts}Spousal relationship.{/ts}', 'Individual', 'Individual', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( @domain_id, '{ts}Sibling of{/ts}','{ts}Sibling of{/ts}', '{ts}Sibling relationship.{/ts}','Individual','Individual', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( @domain_id, '{ts}Employee of{/ts}', '{ts}Employer of{/ts}', '{ts}Employment relationship.{/ts}','Individual','Organization', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( @domain_id, '{ts}Volunteer for{/ts}', '{ts}Volunteer is{/ts}', '{ts}Volunteer relationship.{/ts}','Individual','Organization', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( @domain_id, '{ts}Head of Household for{/ts}', '{ts}Head of Household is{/ts}', '{ts}Head of household.{/ts}','Individual','Household', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( @domain_id, '{ts}Household Member of{/ts}', '{ts}Household Member is{/ts}', '{ts}Household membership.{/ts}','Individual','Household', 0 );

-- Sample Tags
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( @domain_id, '{ts}Non-profit{/ts}', '{ts}Any not-for-profit organization.{/ts}', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( @domain_id, '{ts}Company{/ts}', '{ts}For-profit organization.{/ts}', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( @domain_id, '{ts}Government Entity{/ts}', '{ts}Any governmental entity.{/ts}', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( @domain_id, '{ts}Major Donor{/ts}', '{ts}High-value supporter of our organization.{/ts}', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( @domain_id, '{ts}Volunteer{/ts}', '{ts}Active volunteers.{/ts}', NULL );

-- sample CiviCRM mailing components
INSERT INTO civicrm_mailing_component
    (domain_id,name,component_type,subject,body_html,body_text,is_default,is_active)
VALUES
    (@domain_id,'Mailing Header','Header','This is the Header','HTML Body of Header','Text Body of Header',1,1),
    (@domain_id,'Mailing Footer','Footer','This is the Footer','HTML Body of Footer','Text Body of Footer',1,1),
    (@domain_id,'Subscribe Message','Subscribe','Subscription confirmation request','You have a pending subscription to {ldelim}subscribe.group{rdelim}.  To confirm this subscription, reply to this email.','You have a pending subscription to {ldelim}subscribe.group{rdelim}.  To confirm this subscription, reply to this email.',1,1),
    (@domain_id,'Welcome Message','Welcome','Welcome','Welcome to {ldelim}welcome.group{rdelim}!','Welcome to {ldelim}welcome.group{rdelim}!',1,1),
    (@domain_id,'Unsubscribe Message','Unsubscribe','Unsubscribe results','You have been unsubscribed from {ldelim}unsubscribe.group{rdelim}.','You have been unsubscribed from {ldelim}unsubscribe.group{rdelim}.',1,1),
    (@domain_id,'Opt-out Message','OptOut','Goodbye','You have been removed from {ldelim}domain.name{rdelim}.  Goodbye.','You have been removed from {ldelim}domain.name{rdelim}.  Goodbye.',1,1),
    (@domain_id,'Auto-responder','Reply','Automated response','Thank you for your reply.','Thank you for your reply.',1,1);



INSERT INTO civicrm_dupe_match (domain_id, entity_table , rule) VALUES ( @domain_id,'contact_individual','first_name AND last_name AND email');

-- contribution types
INSERT INTO
   civicrm_contribution_type(name, domain_id, is_reserved, is_active, is_deductible)
VALUES
  ( '{ts}Donation{/ts}'             , @domain_id, 0, 1, 1 ),
  ( '{ts}Member Dues{/ts}'          , @domain_id, 0, 1, 1 ), 
  ( '{ts}Campaign Contribution{/ts}', @domain_id, 0, 1, 0 );

-- payment instrument


-- option group and values for 'preferred communication methods' , 'activity types' and 'gender'

INSERT INTO 
   `civicrm_option_group` (`domain_id`, `name`, `description`, `is_reserved`, `is_active`) 
VALUES 
   (@domain_id, 'preferred_communication_method', 'Preferred communication method'     , 0, 1),
   (@domain_id, 'activity_type'                 , 'Activity Type'                      , 0, 1),
   (@domain_id, 'gender'                        , 'Gender'                             , 0, 1),
   (@domain_id, 'instant_messenger_service'     , 'Instant Messenger (IM) screen-names', 0, 1),
   (@domain_id, 'mobile_provider'               , 'Mobile Phone Providers'             , 0, 1),
   (@domain_id, 'individual_prefix'             , 'Individual contact prefixes.'       , 0, 1),
   (@domain_id, 'individual_suffix'             , 'Individual contact suffixes.'       , 0, 1),
   (@domain_id, 'acl_group'                     , 'ACL Group.'                         , 0, 1),
   (@domain_id, 'accept_creditcard'             , 'Accept Credit Card'                  , 0, 1),
   (@domain_id, 'payment_instrument'             ,'Payment Instrument'                  , 0, 1);

SELECT @option_group_id_pcm            := max(id) from civicrm_option_group where name = 'preferred_communication_method';
SELECT @option_group_id_act            := max(id) from civicrm_option_group where name = 'activity_type';
SELECT @option_group_id_gender         := max(id) from civicrm_option_group where name = 'gender';
SELECT @option_group_id_IMProvider     := max(id) from civicrm_option_group where name = 'instant_messenger_service';
SELECT @option_group_id_mobileProvider := max(id) from civicrm_option_group where name = 'mobile_provider';
SELECT @option_group_id_prefix := max(id) from civicrm_option_group where name = 'individual_prefix';
SELECT @option_group_id_suffix := max(id) from civicrm_option_group where name = 'individual_suffix';
SELECT @option_group_id_aclGroup       := max(id) from civicrm_option_group where name = 'acl_group';
SELECT @option_group_id_acc            := max(id) from civicrm_option_group where name = 'accept_creditcard';
SELECT @option_group_id_pi            := max(id) from civicrm_option_group where name =  'payment_instrument';

INSERT INTO 
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`) 
VALUES
   (@option_group_id_pcm, 'Phone', 1, NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_pcm, 'Email', 2, NULL, NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_pcm, 'Postal Mail', 3, NULL, NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_pcm, 'SMS', 4, NULL, NULL, 0, NULL, 4, NULL, 0, 0, 1),
   (@option_group_id_pcm, 'Fax', 5, NULL, NULL, 0, NULL, 5, NULL, 0, 0, 1),
 
   (@option_group_id_act, 'Meeting', 1, 'Meeting',NULL, 0, NULL, 1, 'Schedule a meeting', 0, 0, 1),
   (@option_group_id_act, 'Phone Call', 2, 'Phone Call', NULL,  0, NULL, 2, 'Schedule a Phone Call', 0, 0, 1),
   (@option_group_id_act, 'Email', 3, 'Email', NULL, 0, NULL, 3, 'Email Sent', 0, 0, 1),
   (@option_group_id_act, 'SMS', 4, 'SMS', NULL, 0, NULL, 4, 'SMS', 0, 0, 1),
   (@option_group_id_act, 'Event', 5,'Event', NULL, 0, NULL, 5, 'Event', 0, 0, 1),

   (@option_group_id_gender, 'Female',      1, 'Female',      NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_gender, 'Male',        2, 'Male',        NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_gender, 'Transgender', 3, 'Transgender', NULL, 0, NULL, 3, NULL, 0, 0, 1),

   (@option_group_id_IMProvider, 'Yahoo', 1, 'Yahoo', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'MSN',   2, 'Msn',   NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'AIM',   3, 'Aim',   NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'GTalk', 4, 'Gtalk', NULL, 0, NULL, 4, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'Jabber',5, 'Jabber',NULL, 0, NULL, 5, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'Skype', 6, 'Skype', NULL, 0, NULL, 6, NULL, 0, 0, 1),

   (@option_group_id_mobileProvider, 'Sprint'  , 1, 'Sprint'  , NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_mobileProvider, 'Verizon' , 2, 'Verizon' , NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_mobileProvider, 'Cingular', 3, 'Cingular', NULL, 0, NULL, 3, NULL, 0, 0, 1),

   (@option_group_id_prefix, 'Mrs', 1, 'Mrs', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_prefix, 'Ms',  2, 'Ms', NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_prefix, 'Mr',  3, 'Mr', NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_prefix, 'Dr',  4, 'Dr', NULL, 0, NULL, 4, NULL, 0, 0, 1),

   (@option_group_id_suffix, 'Jr',  1, 'Jr', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'Sr',  2, 'Sr', NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'II',  3, 'II', NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'III', 4, 'III', NULL, 0, NULL, 4, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'IV',  5, 'IV',  NULL, 0, NULL, 5, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'V',   6, 'V',   NULL, 0, NULL, 6, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'VI',  7, 'VI',  NULL, 0, NULL, 7, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'VII', 8, 'VII', NULL, 0, NULL, 8, NULL, 0, 0, 1),

   (@option_group_id_aclGroup, 'Administrator',  1, 'Admin', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_aclGroup, 'Authenticated',  2, 'Auth' , NULL, 0, NULL, 2, NULL, 0, 0, 1),

   (@option_group_id_acc, 'Visa',  1, 'Visa', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_acc, 'MasterCard',  2, 'MasterCard', NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_acc, 'American Express',  3, 'American Express', NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_acc, 'Discover',  4, 'Discover', NULL, 0, NULL, 4, NULL, 0, 0, 1),

  (@option_group_id_pi, 'Credit Card',  1, 'Credit Card', NULL, 0, NULL, 1, NULL, 0, 0, 1),
  (@option_group_id_pi, 'Debit Card',  2, 'Debit Card', NULL, 0, NULL, 2, NULL, 0, 0, 1),
  (@option_group_id_pi, 'Cash',  3, 'Cash', NULL, 0, NULL, 3, NULL, 0, 0, 1),
  (@option_group_id_pi, 'Check',  4, 'Check', NULL, 0, NULL, 4, NULL, 0, 0, 1),
  (@option_group_id_pi, 'EFT',  5, 'EFT', NULL, 0, NULL, 5, NULL, 0, 0, 1);


-- sample membership status entries
INSERT INTO
    civicrm_membership_status(domain_id, name, start_event, start_event_adjust_unit, start_event_adjust_interval, end_event, end_event_adjust_unit, end_event_adjust_interval, is_current_member, is_admin, weight, is_default, is_active)
VALUES
    (@domain_id,'New', 'join_date', null, null,'join_date','month',3, 1, 0, 1, 0, 1),
    (@domain_id,'Current', 'start_date', null, null,'end_date', null, null, 1, 0, 2, 1, 1),
    (@domain_id,'Grace', 'end_date', null, null,'end_date','month', 1, 1, 0, 3, 0, 1),
    (@domain_id,'Expired', 'end_date', 'month', 1, null, null, null, 0, 0, 4, 0, 1);

-- sample acl entries
INSERT INTO civicrm_acl( domain_id, name, deny, object_table, object_id, operation, entity_table, entity_id, is_active )
VALUES
  (@domain_id, 'All Permissions'   , 0, null                  , 0, 'Edit', 'civicrm_acl_group', 1, 1 ),
  (@domain_id, 'View All Contacts' , 0, 'civicrm_contact'     , 0, 'View', 'civicrm_acl_group', 1, 1 ),
  (@domain_id, 'Edit All Contacts' , 0, 'civicrm_contact'     , 0, 'Edit', 'civicrm_acl_group', 1, 1 ),
  (@domain_id, 'View All Contacts' , 0, 'civicrm_contact'     , 0, 'View', 'civicrm_acl_group', 2, 1 ),
  (@domain_id, 'Edit All Contacts' , 0, 'civicrm_contact'     , 0, 'Edit', 'civicrm_acl_group', 2, 1 ),
  (@domain_id, 'Manage Groups'     , 0, 'civicrm_group'       , 0, 'Edit', 'civicrm_acl_group', 1, 1 ),
  (@domain_id, 'Administer CiviCRM', 0, 'civicrm_group'       , 0, 'Edit', 'civicrm_acl_group', 1, 1 ),
  (@domain_id, 'Import'            , 0, 'civicrm_admin'       , 0, 'Edit', 'civicrm_acl_group', 1, 1 ),
  (@domain_id, 'Edit All Contacts' , 0, 'civicrm_saved_search', 0, 'All' , 'civicrm_acl_group', 1, 1 );
