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
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( @domain_id, '{ts}Billing{/ts}', NULL, '{ts}Billing Address location{/ts}', 1, 1 );

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
    (@domain_id,'{ts}Mailing Header{/ts}','Header','{ts}This is the Header{/ts}','{ts}HTML Body of Header{/ts}','{ts}Text Body of Header{/ts}',1,1),
    (@domain_id,'{ts}Mailing Footer{/ts}','Footer','{ts}This is the Footer{/ts}','{ts}HTML Body of Footer{/ts}','{ts}Text Body of Footer{/ts}',1,1),
    (@domain_id,'{ts}Subscribe Message{/ts}','Subscribe','{ts}Subscription confirmation request{/ts}','{ts}You have a pending subscription to {ldelim}subscribe.group{rdelim}. To confirm this subscription, reply to this email.{/ts}','{ts}You have a pending subscription to {ldelim}subscribe.group{rdelim}. To confirm this subscription, reply to this email.{/ts}',1,1),
    (@domain_id,'{ts}Welcome Message{/ts}','Welcome','{ts}Welcome{/ts}','{ts}Welcome to {ldelim}welcome.group{rdelim}!{/ts}','{ts}Welcome to {ldelim}welcome.group{rdelim}!{/ts}',1,1),
    (@domain_id,'{ts}Unsubscribe Message{/ts}','Unsubscribe','{ts}Unsubscribe results{/ts}','{ts}You have been unsubscribed from {ldelim}unsubscribe.group{rdelim}.{/ts}','{ts}You have been unsubscribed from {ldelim}unsubscribe.group{rdelim}.{/ts}',1,1),
    (@domain_id,'{ts}Opt-out Message{/ts}','OptOut','{ts}Goodbye{/ts}','{ts}You have been removed from {ldelim}domain.name{rdelim}. Goodbye.{/ts}','{ts}You have been removed from {ldelim}domain.name{rdelim}. Goodbye.{/ts}',1,1),
    (@domain_id,'{ts}Auto-responder{/ts}','Reply','{ts}Automated response{/ts}','{ts}Thank you for your reply.{/ts}','{ts}Thank you for your reply.{/ts}',1,1);



INSERT INTO civicrm_dupe_match (domain_id, entity_table , rule) VALUES ( @domain_id,'contact_individual','first_name AND last_name AND email');

-- contribution types
INSERT INTO
   civicrm_contribution_type(name, domain_id, is_reserved, is_active, is_deductible)
VALUES
  ( '{ts}Donation{/ts}'             , @domain_id, 0, 1, 1 ),
  ( '{ts}Member Dues{/ts}'          , @domain_id, 0, 1, 1 ), 
  ( '{ts}Campaign Contribution{/ts}', @domain_id, 0, 1, 0 ),
  ( '{ts}Event Fee{/ts}'            , @domain_id, 0, 1, 0 );

-- option groups and values for 'preferred communication methods' , 'activity types', 'gender', etc.

INSERT INTO 
   `civicrm_option_group` (`domain_id`, `name`, `description`, `is_reserved`, `is_active`) 
VALUES 
   (@domain_id, 'preferred_communication_method', '{ts}Preferred Communication Method{/ts}'     , 0, 1),
   (@domain_id, 'activity_type'                 , '{ts}Activity Type{/ts}'                      , 0, 1),
   (@domain_id, 'gender'                        , '{ts}Gender{/ts}'                             , 0, 1),
   (@domain_id, 'instant_messenger_service'     , '{ts}Instant Messenger (IM) screen-names{/ts}', 0, 1),
   (@domain_id, 'mobile_provider'               , '{ts}Mobile Phone Providers{/ts}'             , 0, 1),
   (@domain_id, 'individual_prefix'             , '{ts}Individual contact prefixes{/ts}'        , 0, 1),
   (@domain_id, 'individual_suffix'             , '{ts}Individual contact suffixes{/ts}'        , 0, 1),
   (@domain_id, 'acl_role'                      , '{ts}ACL Role{/ts}'                           , 0, 1),
   (@domain_id, 'accept_creditcard'             , '{ts}Accepted Credit Cards{/ts}'              , 0, 1),
   (@domain_id, 'payment_instrument'            , '{ts}Payment Instruments{/ts}'                , 0, 1),
   (@domain_id, 'contribution_status'           , '{ts}Contribution Status{/ts}'                , 0, 1),
   (@domain_id, 'participant_status'            , '{ts}Participant Status{/ts}'                 , 0, 1),
   (@domain_id, 'participant_role'              , '{ts}Participant Role{/ts}'                   , 0, 1),
   (@domain_id, 'event_type'                    , '{ts}Event Type{/ts}'                         , 0, 1),
   (@domain_id, 'contact_summary_options'       , '{ts}Contact Summary Options{/ts}'            , 0, 1),
   (@domain_id, 'edit_contact_options'          , '{ts}Edit Contact Options{/ts}'               , 0, 1),
   (@domain_id, 'advanced_search_options'       , '{ts}Advanced Search Options{/ts}'            , 0, 1),
   (@domain_id, 'user_dashboard_options'        , '{ts}user Dashboard Options{/ts}'             , 0, 1);
   
SELECT @option_group_id_pcm            := max(id) from civicrm_option_group where name = 'preferred_communication_method';
SELECT @option_group_id_act            := max(id) from civicrm_option_group where name = 'activity_type';
SELECT @option_group_id_gender         := max(id) from civicrm_option_group where name = 'gender';
SELECT @option_group_id_IMProvider     := max(id) from civicrm_option_group where name = 'instant_messenger_service';
SELECT @option_group_id_mobileProvider := max(id) from civicrm_option_group where name = 'mobile_provider';
SELECT @option_group_id_prefix         := max(id) from civicrm_option_group where name = 'individual_prefix';
SELECT @option_group_id_suffix         := max(id) from civicrm_option_group where name = 'individual_suffix';
SELECT @option_group_id_aclRole        := max(id) from civicrm_option_group where name = 'acl_role';
SELECT @option_group_id_acc            := max(id) from civicrm_option_group where name = 'accept_creditcard';
SELECT @option_group_id_pi             := max(id) from civicrm_option_group where name = 'payment_instrument';
SELECT @option_group_id_cs             := max(id) from civicrm_option_group where name = 'contribution_status';
SELECT @option_group_id_ps             := max(id) from civicrm_option_group where name = 'participant_status';
SELECT @option_group_id_pRole          := max(id) from civicrm_option_group where name = 'participant_role';
SELECT @option_group_id_etype          := max(id) from civicrm_option_group where name = 'event_type';
SELECT @option_group_id_csOpt          := max(id) from civicrm_option_group where name = 'contact_summary_options';
SELECT @option_group_id_ecOpt          := max(id) from civicrm_option_group where name = 'edit_contact_options';
SELECT @option_group_id_asOpt          := max(id) from civicrm_option_group where name = 'advanced_search_options';
SELECT @option_group_id_udOpt          := max(id) from civicrm_option_group where name = 'user_dashboard_options';

INSERT INTO 
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`) 
VALUES
   (@option_group_id_pcm, '{ts}Phone{/ts}', 1, NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_pcm, '{ts}Email{/ts}', 2, NULL, NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_pcm, '{ts}Postal Mail{/ts}', 3, NULL, NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_pcm, '{ts}SMS{/ts}', 4, NULL, NULL, 0, NULL, 4, NULL, 0, 0, 1),
   (@option_group_id_pcm, '{ts}Fax{/ts}', 5, NULL, NULL, 0, NULL, 5, NULL, 0, 0, 1),
 
   (@option_group_id_act, '{ts}Meeting{/ts}', 1, 'Meeting',NULL, 0, NULL, 1, 'Schedule a meeting', 0, 1, 1),
   (@option_group_id_act, '{ts}Phone Call{/ts}', 2, 'Phone Call', NULL,  0, NULL, 2, 'Schedule a Phone Call', 0, 1, 1),
   (@option_group_id_act, '{ts}Email{/ts}', 3, 'Email', NULL, 0, NULL, 3, 'Email Sent', 0, 1, 1),
   (@option_group_id_act, '{ts}SMS{/ts}', 4, 'SMS', NULL, 0, NULL, 4, 'SMS', 0, 1, 1),
   (@option_group_id_act, '{ts}Event{/ts}', 5,'Event', NULL, 0, NULL, 5, 'Event', 0, 0, 1),

   (@option_group_id_gender, '{ts}Female{/ts}',      1, 'Female',      NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_gender, '{ts}Male{/ts}',        2, 'Male',        NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_gender, '{ts}Transgender{/ts}', 3, 'Transgender', NULL, 0, NULL, 3, NULL, 0, 0, 1),

   (@option_group_id_IMProvider, 'Yahoo', 1, 'Yahoo', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'MSN',   2, 'Msn',   NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'AIM',   3, 'Aim',   NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'GTalk', 4, 'Gtalk', NULL, 0, NULL, 4, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'Jabber',5, 'Jabber',NULL, 0, NULL, 5, NULL, 0, 0, 1),
   (@option_group_id_IMProvider, 'Skype', 6, 'Skype', NULL, 0, NULL, 6, NULL, 0, 0, 1),

   (@option_group_id_mobileProvider, 'Sprint'  , 1, 'Sprint'  , NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_mobileProvider, 'Verizon' , 2, 'Verizon' , NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_mobileProvider, 'Cingular', 3, 'Cingular', NULL, 0, NULL, 3, NULL, 0, 0, 1),

   (@option_group_id_prefix, '{ts}Mrs{/ts}', 1, 'Mrs', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_prefix, '{ts}Ms{/ts}',  2, 'Ms', NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_prefix, '{ts}Mr{/ts}',  3, 'Mr', NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_prefix, '{ts}Dr{/ts}',  4, 'Dr', NULL, 0, NULL, 4, NULL, 0, 0, 1),

   (@option_group_id_suffix, '{ts}Jr{/ts}',  1, 'Jr', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_suffix, '{ts}Sr{/ts}',  2, 'Sr', NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'II',  3, 'II', NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'III', 4, 'III', NULL, 0, NULL, 4, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'IV',  5, 'IV',  NULL, 0, NULL, 5, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'V',   6, 'V',   NULL, 0, NULL, 6, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'VI',  7, 'VI',  NULL, 0, NULL, 7, NULL, 0, 0, 1),
   (@option_group_id_suffix, 'VII', 8, 'VII', NULL, 0, NULL, 8, NULL, 0, 0, 1),

   (@option_group_id_aclRole, '{ts}Administrator{/ts}',  1, 'Admin', NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_aclRole, '{ts}Authenticated{/ts}',  2, 'Auth' , NULL, 0, NULL, 2, NULL, 0, 0, 1),

   (@option_group_id_acc, 'Visa'      ,  1, 'Visa'      , NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_acc, 'MasterCard',  2, 'MasterCard', NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_acc, 'Amex'      ,  3, 'Amex'      , NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_acc, 'Discover'  ,  4, 'Discover'  , NULL, 0, NULL, 4, NULL, 0, 0, 1),

  (@option_group_id_pi, '{ts}Credit Card{/ts}',  1, 'Credit Card', NULL, 0, NULL, 1, NULL, 0, 0, 1),
  (@option_group_id_pi, '{ts}Debit Card{/ts}',  2, 'Debit Card', NULL, 0, NULL, 2, NULL, 0, 0, 1),
  (@option_group_id_pi, '{ts}Cash{/ts}',  3, 'Cash', NULL, 0, NULL, 3, NULL, 0, 0, 1),
  (@option_group_id_pi, '{ts}Check{/ts}',  4, 'Check', NULL, 0, NULL, 4, NULL, 0, 0, 1),
  (@option_group_id_pi, '{ts}EFT{/ts}',  5, 'EFT', NULL, 0, NULL, 5, NULL, 0, 0, 1),

  (@option_group_id_cs, '{ts}Completed{/ts}'  , 1, 'Completed'  , NULL, 0, NULL, 1, NULL, 0, 0, 1),
  (@option_group_id_cs, '{ts}Pending{/ts}'    , 2, 'Pending'    , NULL, 0, NULL, 2, NULL, 0, 0, 1),
  (@option_group_id_cs, '{ts}Cancelled{/ts}'  , 3, 'Cancelled'  , NULL, 0, NULL, 3, NULL, 0, 0, 1),
  (@option_group_id_cs, '{ts}Failed{/ts}'     , 4, 'Failed'     , NULL, 0, NULL, 4, NULL, 0, 0, 1),
  (@option_group_id_cs, '{ts}In Progress{/ts}', 5, 'In Progress', NULL, 0, NULL, 5, NULL, 0, 0, 1),

  (@option_group_id_ps, '{ts}Registered{/ts}', 1, 'Registered', NULL, 0, NULL, 1, NULL, 0, 1, 1),
  (@option_group_id_ps, '{ts}Attended{/ts}',   2, 'Attended',   NULL, 0, NULL, 2, NULL, 0, 0, 1),
  (@option_group_id_ps, '{ts}No-show{/ts}',    3, 'No-show',    NULL, 0, NULL, 3, NULL, 0, 0, 1),
  (@option_group_id_ps, '{ts}Cancelled{/ts}',  4, 'Cancelled',  NULL, 0, NULL, 4, NULL, 0, 1, 1),

  (@option_group_id_pRole, '{ts}Attendee{/ts}',  1, 'Attendee',  NULL, 0, NULL, 1, NULL, 0, 0, 1),
  (@option_group_id_pRole, '{ts}Volunteer{/ts}', 2, 'Volunteer', NULL, 0, NULL, 2, NULL, 0, 0, 1),
  (@option_group_id_pRole, '{ts}Host{/ts}',      3, 'Host',      NULL, 0, NULL, 3, NULL, 0, 0, 1),
  (@option_group_id_pRole, '{ts}Speaker{/ts}',   4, 'Speaker',   NULL, 0, NULL, 4, NULL, 0, 0, 1),

  (@option_group_id_etype, '{ts}Conference{/ts}', 1, 'Conference',  NULL, 0, NULL, 1, NULL, 0, 0, 1 ),
  (@option_group_id_etype, '{ts}Exhibition{/ts}', 2, 'Exhibition',  NULL, 0, NULL, 2, NULL, 0, 0, 1 ),
  (@option_group_id_etype, '{ts}Fundraiser{/ts}', 3, 'Fundraiser',  NULL, 0, NULL, 3, NULL, 0, 0, 1 ),
  (@option_group_id_etype, '{ts}Meeting{/ts}',    4, 'Meeting',     NULL, 0, NULL, 4, NULL, 0, 0, 1 ),
  (@option_group_id_etype, '{ts}Performance{/ts}',5, 'Performance', NULL, 0, NULL, 5, NULL, 0, 0, 1 ),
  (@option_group_id_etype, '{ts}Workshop{/ts}',   6, 'Workshop',    NULL, 0, NULL, 6, NULL, 0, 0, 1 ),

  (@option_group_id_csOpt, '{ts}Summary{/ts}'      ,   1, NULL, NULL, 0, NULL,  1, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Activities{/ts}'   ,   2, NULL, NULL, 0, NULL,  2, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Relationships{/ts}',   3, NULL, NULL, 0, NULL,  3, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Groups{/ts}'       ,   4, NULL, NULL, 0, NULL,  4, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Notes{/ts}'        ,   5, NULL, NULL, 0, NULL,  5, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Tags{/ts}'         ,   6, NULL, NULL, 0, NULL,  6, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Change Log{/ts}'   ,   7, NULL, NULL, 0, NULL,  7, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Contributions{/ts}',   8, NULL, NULL, 0, NULL,  8, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Memberships{/ts}'  ,   9, NULL, NULL, 0, NULL,  9, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Events{/ts}'       ,  10, NULL, NULL, 0, NULL, 10, NULL, 0, 0, 1 ),

  (@option_group_id_ecOpt, '{ts}Communication Preferences{/ts}',   1, NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1 ),
  (@option_group_id_ecOpt, '{ts}Demographics{/ts}'             ,   2, NULL, NULL, 0, NULL, 2, NULL, 0, 0, 1 ),
  (@option_group_id_ecOpt, '{ts}Tags and Groups{/ts}'          ,   3, NULL, NULL, 0, NULL, 3, NULL, 0, 0, 1 ),

  (@option_group_id_asOpt, '{ts}Search Criteria{/ts}'     ,   1, NULL, NULL, 0, NULL,  1, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Address Fields{/ts}'      ,   2, NULL, NULL, 0, NULL,  2, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Custom Fields{/ts}'       ,   3, NULL, NULL, 0, NULL,  3, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Activity History{/ts}'    ,   4, NULL, NULL, 0, NULL,  4, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Scheduled Activities{/ts}',   5, NULL, NULL, 0, NULL,  5, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Relationships{/ts}'       ,   6, NULL, NULL, 0, NULL,  6, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Notes{/ts}'               ,   7, NULL, NULL, 0, NULL,  7, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Change Log{/ts}'          ,   8, NULL, NULL, 0, NULL,  8, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Contributions{/ts}'       ,   9, NULL, NULL, 0, NULL,  9, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Memberships{/ts}'         ,  10, NULL, NULL, 0, NULL, 10, NULL, 0, 0, 1 ),
  (@option_group_id_csOpt, '{ts}Events{/ts}'              ,  11, NULL, NULL, 0, NULL, 11, NULL, 0, 0, 1 ),

  (@option_group_id_udOpt, '{ts}Groups{/ts}'       , 1, NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1 ),
  (@option_group_id_udOpt, '{ts}Contributions{/ts}', 2, NULL, NULL, 0, NULL, 2, NULL, 0, 0, 1 ),
  (@option_group_id_udOpt, '{ts}Memberships{/ts}'  , 3, NULL, NULL, 0, NULL, 3, NULL, 0, 0, 1 ),
  (@option_group_id_udOpt, '{ts}Events{/ts}'       , 4, NULL, NULL, 0, NULL, 4, NULL, 0, 0, 1 );

-- sample membership status entries
INSERT INTO
    civicrm_membership_status(domain_id, name, start_event, start_event_adjust_unit, start_event_adjust_interval, end_event, end_event_adjust_unit, end_event_adjust_interval, is_current_member, is_admin, weight, is_default, is_active)
VALUES
    (@domain_id,'{ts}New{/ts}', 'join_date', null, null,'join_date','month',3, 1, 0, 1, 0, 1),
    (@domain_id,'{ts}Current{/ts}', 'start_date', null, null,'end_date', null, null, 1, 0, 2, 1, 1),
    (@domain_id,'{ts}Grace{/ts}', 'end_date', null, null,'end_date','month', 1, 1, 0, 3, 0, 1),
    (@domain_id,'{ts}Expired{/ts}', 'end_date', 'month', 1, null, null, null, 0, 0, 4, 0, 1);

