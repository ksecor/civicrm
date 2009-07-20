-- This file provides template to civicrm_data.mysql. Inserts all base data needed for a new CiviCRM DB

SET @domainName := 'Default Domain Name';

-- Add components to system wide registry
-- We're doing it early to avoid constraint errors.
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviEvent'     , 'CRM_Event' );
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviContribute', 'CRM_Contribute' );
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviMember'    , 'CRM_Member' );
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviMail'      , 'CRM_Mailing' );
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviGrant'     , 'CRM_Grant' );
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviPledge'    , 'CRM_Pledge' );
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviCase'      , 'CRM_Case' );
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviReport'    , 'CRM_Report' );

INSERT INTO civicrm_address ( contact_id, location_type_id, is_primary, is_billing, street_address, street_number, street_number_suffix, street_number_predirectional, street_name, street_type, street_number_postdirectional, street_unit, supplemental_address_1, supplemental_address_2, supplemental_address_3, city, county_id, state_province_id, postal_code_suffix, postal_code, usps_adc, country_id, geo_code_1, geo_code_2, timezone)
      VALUES
      ( NULL, 1, 1, 1, 'S 15S El Camino Way E', 14, 'S', NULL, 'El Camino', 'Way', NULL, NULL, NULL, NULL, NULL, 'Collinsville', NULL, 1006, NULL, '6022', NULL, 1228, 41.8328, -72.9253, NULL);

SELECT @addId := id from civicrm_address where street_address = 'S 15S El Camino Way E';

INSERT INTO civicrm_email (contact_id, location_type_id, email, is_primary, is_billing, on_hold, hold_date, reset_date)
      VALUES
      (NULL, 1, 'domainemail@example.org', 0, 0, 0, NULL, NULL);

SELECT @emailId := id from civicrm_email where email = 'domainemail@example.org';

INSERT INTO civicrm_phone (contact_id, location_type_id, is_primary, is_billing, mobile_provider_id, phone, phone_type_id)
      VALUES
      (NULL, 1, 0, 0, NULL,'204 222-1001', 1);

SELECT @phoneId := id from civicrm_phone where phone = '204 222-1001';

INSERT INTO civicrm_loc_block ( address_id, email_id, phone_id, address_2_id, email_2_id, phone_2_id)
      VALUES
      ( @addId, @emailId, @phoneId, NULL,NULL,NULL);

SELECT @locBlockId := id from civicrm_loc_block where phone_id = @phoneId AND email_id = @emailId AND address_id = @addId;

INSERT INTO civicrm_domain (name, version, loc_block_id) VALUES (@domainName, '2.2', @locBlockId);
SELECT @domainID := id FROM civicrm_domain where name = 'Default Domain Name';

INSERT INTO civicrm_mail_settings (name, is_default, domain) VALUES ('default', true, 'FIXME.ORG');

-- Sample location types
INSERT INTO civicrm_location_type( name, vcard_name, description, is_reserved, is_active, is_default ) VALUES( '{ts escape="sql"}Home{/ts}', 'HOME', '{ts escape="sql"}Place of residence{/ts}', 0, 1, 1 );
INSERT INTO civicrm_location_type( name, vcard_name, description, is_reserved, is_active ) VALUES( '{ts escape="sql"}Work{/ts}', 'WORK', '{ts escape="sql"}Work location{/ts}', 0, 1 );
INSERT INTO civicrm_location_type( name, vcard_name, description, is_reserved, is_active ) VALUES( '{ts escape="sql"}Main{/ts}', NULL, '{ts escape="sql"}Main office location{/ts}', 0, 1 );
INSERT INTO civicrm_location_type( name, vcard_name, description, is_reserved, is_active ) VALUES( '{ts escape="sql"}Other{/ts}', NULL, '{ts escape="sql"}Other location{/ts}', 0, 1 );
-- the following location must stay with the untranslated Billing name, CRM-2064
INSERT INTO civicrm_location_type( name, vcard_name, description, is_reserved, is_active ) VALUES( 'Billing', NULL, '{ts escape="sql"}Billing Address location{/ts}', 1, 1 );

-- Sample relationship types
INSERT INTO civicrm_relationship_type( name_a_b,label_a_b, name_b_a,label_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 'Child of', '{ts escape="sql"}Child of{/ts}', 'Parent of', '{ts escape="sql"}Parent of{/ts}', '{ts escape="sql"}Parent/child relationship.{/ts}', 'Individual', 'Individual', 0 ),
          ( 'Spouse of', '{ts escape="sql"}Spouse of{/ts}', 'Spouse of', '{ts escape="sql"}Spouse of{/ts}', '{ts escape="sql"}Spousal relationship.{/ts}', 'Individual', 'Individual', 0 ),
          ( 'Sibling of', '{ts escape="sql"}Sibling of{/ts}', 'Sibling of', '{ts escape="sql"}Sibling of{/ts}', '{ts escape="sql"}Sibling relationship.{/ts}', 'Individual','Individual', 0 ),
          ( 'Employee of', '{ts escape="sql"}Employee of{/ts}', 'Employer of', '{ts escape="sql"}Employer of{/ts}', '{ts escape="sql"}Employment relationship.{/ts}','Individual','Organization', 1 ),
          ( 'Volunteer for', '{ts escape="sql"}Volunteer for{/ts}', 'Volunteer is', '{ts escape="sql"}Volunteer is{/ts}', '{ts escape="sql"}Volunteer relationship.{/ts}','Individual','Organization', 0 ),
          ( 'Head of Household for', '{ts escape="sql"}Head of Household for{/ts}', 'Head of Household is', '{ts escape="sql"}Head of Household is{/ts}', '{ts escape="sql"}Head of household.{/ts}','Individual','Household', 1 ),
          ( 'Household Member of', '{ts escape="sql"}Household Member of{/ts}', 'Household Member is', '{ts escape="sql"}Household Member is{/ts}', '{ts escape="sql"}Household membership.{/ts}','Individual','Household', 1 );

-- Relationship Types for CiviCase
INSERT INTO civicrm_relationship_type( name_a_b,label_a_b, name_b_a,label_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 'Case Coordinator is', 'Case Coordinator is', 'Case Coordinator', 'Case Coordinator', 'Case Coordinator', 'Individual', 'Individual', 0 );
INSERT INTO civicrm_relationship_type( name_a_b,label_a_b, name_b_a,label_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 'Supervised by', 'Supervised by', 'Supervisor', 'Supervisor', 'Immediate workplace supervisor', 'Individual', 'Individual', 0 );


-- Sample Tags
INSERT INTO civicrm_tag( name, description, parent_id )
    VALUES( '{ts escape="sql"}Non-profit{/ts}', '{ts escape="sql"}Any not-for-profit organization.{/ts}', NULL );
INSERT INTO civicrm_tag( name, description, parent_id )
    VALUES( '{ts escape="sql"}Company{/ts}', '{ts escape="sql"}For-profit organization.{/ts}', NULL );
INSERT INTO civicrm_tag( name, description, parent_id )
    VALUES( '{ts escape="sql"}Government Entity{/ts}', '{ts escape="sql"}Any governmental entity.{/ts}', NULL );
INSERT INTO civicrm_tag( name, description, parent_id )
    VALUES( '{ts escape="sql"}Major Donor{/ts}', '{ts escape="sql"}High-value supporter of our organization.{/ts}', NULL );
INSERT INTO civicrm_tag( name, description, parent_id )
    VALUES( '{ts escape="sql"}Volunteer{/ts}', '{ts escape="sql"}Active volunteers.{/ts}', NULL );

-- sample CiviCRM mailing components
INSERT INTO civicrm_mailing_component
    (name,component_type,subject,body_html,body_text,is_default,is_active)
VALUES
    ('{ts escape="sql"}Mailing Header{/ts}','Header','{ts escape="sql"}Descriptive Title for this Header{/ts}','{ts escape="sql"}Sample Header for HTML formatted content.{/ts}','{ts escape="sql"}Sample Header for TEXT formatted content.{/ts}',1,1),
    ('{ts escape="sql"}Mailing Footer{/ts}','Footer','{ts escape="sql"}Descriptive Title for this Footer.{/ts}','{ts escape="sql"}Sample Footer for HTML formatted content.{/ts}','{ts escape="sql"}Sample Footer for TEXT formatted content.{/ts}',1,1),
    ('{ts escape="sql"}Subscribe Message{/ts}','Subscribe','{ts escape="sql"}Subscription Confirmation Request{/ts}','{ts escape="sql"}You have a pending subscription to the {ldelim}subscribe.group{rdelim} mailing list. To confirm this subscription, reply to this email or click <a href="{ldelim}subscribe.url{rdelim}">here</a>.{/ts}','{ts escape="sql"}You have a pending subscription to the {ldelim}subscribe.group{rdelim} mailing list. To confirm this subscription, reply to this email or click on this link: {ldelim}subscribe.url{rdelim}{/ts}',1,1),
    ('{ts escape="sql"}Welcome Message{/ts}','Welcome','{ts escape="sql"}Your Subscription has been Activated{/ts}','{ts escape="sql"}Welcome. Your subscription to the {ldelim}welcome.group{rdelim} mailing list has been activated.{/ts}','{ts escape="sql"}Welcome. Your subscription to the {ldelim}welcome.group{rdelim} mailing list has been activated.{/ts}',1,1),
    ('{ts escape="sql"}Unsubscribe Message{/ts}','Unsubscribe','{ts escape="sql"}Un-subscribe Confirmation{/ts}','{ts escape="sql"}You have been un-subscribed from the following groups: {ldelim}unsubscribe.group{rdelim}. You can re-subscribe by mailing {ldelim}action.resubscribe{rdelim} or clicking <a href="{ldelim}action.resubscribeUrl{rdelim}">here</a>.{/ts}','{ts escape="sql"}You have been un-subscribed from the following groups: {ldelim}unsubscribe.group{rdelim}. You can re-subscribe by mailing {ldelim}action.resubscribe{rdelim} or clicking {ldelim}action.resubscribeUrl{rdelim}{/ts}',1,1),
    ('{ts escape="sql"}Resubscribe Message{/ts}','Resubscribe','{ts escape="sql"}Re-subscribe Confirmation{/ts}','{ts escape="sql"}You have been re-subscribed to the following groups: {ldelim}resubscribe.group{rdelim}. You can un-subscribe by mailing {ldelim}action.unsubscribe{rdelim} or clicking <a href="{ldelim}action.unsubscribeUrl{rdelim}">here</a>.{/ts}','{ts escape="sql"}You have been re-subscribed to the following groups: {ldelim}resubscribe.group{rdelim}. You can un-subscribe by mailing {ldelim}action.unsubscribe{rdelim} or clicking {ldelim}action.unsubscribeUrl{rdelim}{/ts}',1,1),
    ('{ts escape="sql"}Opt-out Message{/ts}','OptOut','{ts escape="sql"}Opt-out Confirmation{/ts}','{ts escape="sql"}Your email address has been removed from {ldelim}domain.name{rdelim} mailing lists.{/ts}','{ts escape="sql"}Your email address has been removed from {ldelim}domain.name{rdelim} mailing lists.{/ts}',1,1),
    ('{ts escape="sql"}Auto-responder{/ts}','Reply','{ts escape="sql"}Please Send Inquiries to Our Contact Email Address{/ts}','{ts escape="sql"}This is an automated reply from an un-attended mailbox. Please send any inquiries to the contact email address listed on our web-site.{/ts}','{ts escape="sql"}This is an automated reply from an un-attended mailbox. Please send any inquiries to the contact email address listed on our web-site.{/ts}',1,1);



-- contribution types
INSERT INTO
   civicrm_contribution_type(name, is_reserved, is_active, is_deductible)
VALUES
  ( '{ts escape="sql"}Donation{/ts}'             , 0, 1, 1 ),
  ( '{ts escape="sql"}Member Dues{/ts}'          , 0, 1, 1 ), 
  ( '{ts escape="sql"}Campaign Contribution{/ts}', 0, 1, 0 ),
  ( '{ts escape="sql"}Event Fee{/ts}'            , 0, 1, 0 );

-- option groups and values for 'preferred communication methods' , 'activity types', 'gender', etc.

INSERT INTO 
   `civicrm_option_group` (`name`, `description`, `is_reserved`, `is_active`) 
VALUES 
   ('preferred_communication_method', '{ts escape="sql"}Preferred Communication Method{/ts}'     , 0, 1),
   ('activity_type'                 , '{ts escape="sql"}Activity Type{/ts}'                      , 0, 1),
   ('gender'                        , '{ts escape="sql"}Gender{/ts}'                             , 0, 1),
   ('instant_messenger_service'     , '{ts escape="sql"}Instant Messenger (IM) screen-names{/ts}', 0, 1),
   ('mobile_provider'               , '{ts escape="sql"}Mobile Phone Providers{/ts}'             , 0, 1),
   ('individual_prefix'             , '{ts escape="sql"}Individual contact prefixes{/ts}'        , 0, 1),
   ('individual_suffix'             , '{ts escape="sql"}Individual contact suffixes{/ts}'        , 0, 1),
   ('acl_role'                      , '{ts escape="sql"}ACL Role{/ts}'                           , 0, 1),
   ('accept_creditcard'             , '{ts escape="sql"}Accepted Credit Cards{/ts}'              , 0, 1),
   ('payment_instrument'            , '{ts escape="sql"}Payment Instruments{/ts}'                , 0, 1),
   ('contribution_status'           , '{ts escape="sql"}Contribution Status{/ts}'                , 0, 1),
   ('pcp_status'                    , '{ts escape="sql"}PCP Status{/ts}'                         , 0, 1),
   ('participant_role'              , '{ts escape="sql"}Participant Role{/ts}'                   , 0, 1),
   ('event_type'                    , '{ts escape="sql"}Event Type{/ts}'                         , 0, 1),
   ('contact_view_options'          , '{ts escape="sql"}Contact View Options{/ts}'               , 0, 1),
   ('contact_edit_options'          , '{ts escape="sql"}Contact Edit Options{/ts}'               , 0, 1),
   ('advanced_search_options'       , '{ts escape="sql"}Advanced Search Options{/ts}'            , 0, 1),
   ('user_dashboard_options'        , '{ts escape="sql"}User Dashboard Options{/ts}'             , 0, 1),
   ('address_options'               , '{ts escape="sql"}Addressing Options{/ts}'                 , 0, 1),
   ('group_type'                    , '{ts escape="sql"}Group Type{/ts}'                         , 0, 1),
   ('grant_status'                  , '{ts escape="sql"}Grant status{/ts}'                       , 0, 1),
   ('grant_type'                    , '{ts escape="sql"}Grant Type{/ts}'                         , 0, 1),
   ('honor_type'                    , '{ts escape="sql"}Honor Type{/ts}'                         , 0, 1),
   ('custom_search'                 , '{ts escape="sql"}Custom Search{/ts}'                      , 0, 1),
   ('activity_status'               , '{ts escape="sql"}Activity Status{/ts}'                    , 0, 1),
   ('case_type'                     , '{ts escape="sql"}Case Type{/ts}'                          , 0, 1),
   ('case_status'                   , '{ts escape="sql"}Case Status{/ts}'                        , 0, 1),
   ('participant_listing'           , '{ts escape="sql"}Participant Listing{/ts}'                , 0, 1),
   ('safe_file_extension'           , '{ts escape="sql"}Safe File Extension{/ts}'                , 0, 1),
   ('from_email_address'            , '{ts escape="sql"}From Email Address{/ts}'                 , 0, 1),
   ('mapping_type'                  , '{ts escape="sql"}Mapping Type{/ts}'                       , 0, 1),
   ('wysiwyg_editor'                , '{ts escape="sql"}WYSIWYG Editor{/ts}'                     , 0, 1),
   ('recur_frequency_units'         , '{ts escape="sql"}Recurring Frequency Units{/ts}'          , 0, 1), 
   ('phone_type'                    , '{ts escape="sql"}Phone Type{/ts}'                         , 0, 1),
   ('custom_data_type'              , '{ts escape="sql"}Custom Data Type{/ts}'                   , 0, 1),  
   ('visibility'                    , '{ts escape="sql"}Visibility{/ts}'                         , 0, 1),
   ('mail_protocol'                 , '{ts escape="sql"}Mail Protocol{/ts}'                      , 0, 1),
   ('priority'                      , '{ts escape="sql"}Priority{/ts}'                           , 0, 1),
   ('redaction_rule'                , '{ts escape="sql"}Redaction Rule{/ts}'                     , 0, 1),	
   ('report_template'               , '{ts escape="sql"}Report Template{/ts}'                    , 0, 1),
   ('email_greeting'                , '{ts escape="sql"}Email Greeting Type{/ts}'                , 0, 1),
   ('postal_greeting'               , '{ts escape="sql"}Postal Greeting Type{/ts}'               , 0, 1),
   ('addressee'                     , '{ts escape="sql"}Addressee Type{/ts}'                     , 0, 1);
   
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
SELECT @option_group_id_pcp            := max(id) from civicrm_option_group where name = 'pcp_status';
SELECT @option_group_id_pRole          := max(id) from civicrm_option_group where name = 'participant_role';
SELECT @option_group_id_etype          := max(id) from civicrm_option_group where name = 'event_type';
SELECT @option_group_id_cvOpt          := max(id) from civicrm_option_group where name = 'contact_view_options';
SELECT @option_group_id_ceOpt          := max(id) from civicrm_option_group where name = 'contact_edit_options';
SELECT @option_group_id_asOpt          := max(id) from civicrm_option_group where name = 'advanced_search_options';
SELECT @option_group_id_udOpt          := max(id) from civicrm_option_group where name = 'user_dashboard_options';
SELECT @option_group_id_adOpt          := max(id) from civicrm_option_group where name = 'address_options';
SELECT @option_group_id_gType          := max(id) from civicrm_option_group where name = 'group_type';
SELECT @option_group_id_grantSt        := max(id) from civicrm_option_group where name = 'grant_status';
SELECT @option_group_id_grantTyp       := max(id) from civicrm_option_group where name = 'grant_type';
SELECT @option_group_id_honorTyp       := max(id) from civicrm_option_group where name = 'honor_type';
SELECT @option_group_id_csearch        := max(id) from civicrm_option_group where name = 'custom_search';
SELECT @option_group_id_acs            := max(id) from civicrm_option_group where name = 'activity_status';
SELECT @option_group_id_ct             := max(id) from civicrm_option_group where name = 'case_type';
SELECT @option_group_id_cas            := max(id) from civicrm_option_group where name = 'case_status';
SELECT @option_group_id_pl             := max(id) from civicrm_option_group where name = 'participant_listing';
SELECT @option_group_id_sfe            := max(id) from civicrm_option_group where name = 'safe_file_extension';
SELECT @option_group_id_mt             := max(id) from civicrm_option_group where name = 'mapping_type';
SELECT @option_group_id_we             := max(id) from civicrm_option_group where name = 'wysiwyg_editor';
SELECT @option_group_id_fu             := max(id) from civicrm_option_group where name = 'recur_frequency_units';
SELECT @option_group_id_pht            := max(id) from civicrm_option_group where name = 'phone_type';
SELECT @option_group_id_fma            := max(id) from civicrm_option_group where name = 'from_email_address';
SELECT @option_group_id_cdt            := max(id) from civicrm_option_group where name = 'custom_data_type';
SELECT @option_group_id_vis            := max(id) from civicrm_option_group where name = 'visibility';
SELECT @option_group_id_mp             := max(id) from civicrm_option_group where name = 'mail_protocol';
SELECT @option_group_id_priority       := max(id) from civicrm_option_group where name = 'priority';
SELECT @option_group_id_rr             := max(id) from civicrm_option_group where name = 'redaction_rule';
SELECT @option_group_id_emailGreeting  := max(id) from civicrm_option_group where name = 'email_greeting';
SELECT @option_group_id_postalGreeting := max(id) from civicrm_option_group where name = 'postal_greeting';
SELECT @option_group_id_addressee      := max(id) from civicrm_option_group where name = 'addressee';
SELECT @option_group_id_report         := max(id) from civicrm_option_group where name = 'report_template';

SELECT @contributeCompId := max(id) FROM civicrm_component where name = 'CiviContribute';
SELECT @eventCompId      := max(id) FROM civicrm_component where name = 'CiviEvent';
SELECT @memberCompId     := max(id) FROM civicrm_component where name = 'CiviMember';
SELECT @pledgeCompId     := max(id) FROM civicrm_component where name = 'CiviPledge';


INSERT INTO 
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`, `component_id`, `visibility_id`) 
VALUES
   (@option_group_id_pcm, '{ts escape="sql"}Phone{/ts}', 1, NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_pcm, '{ts escape="sql"}Email{/ts}', 2, NULL, NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_pcm, '{ts escape="sql"}Postal Mail{/ts}', 3, NULL, NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_pcm, '{ts escape="sql"}SMS{/ts}', 4, NULL, NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_pcm, '{ts escape="sql"}Fax{/ts}', 5, NULL, NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),
 
   (@option_group_id_act, '{ts escape="sql"}Meeting{/ts}',                            1, 'Meeting', NULL, 0, NULL, 1, NULL,                       				                                                0, 1, 1, NULL, NULL),
   (@option_group_id_act, '{ts escape="sql"}Phone Call{/ts}',                         2, 'Phone Call',          NULL, 0, NULL, 2, NULL,                                                          				0, 1, 1, NULL, NULL),
   (@option_group_id_act, '{ts escape="sql"}Email{/ts}',                              3, 'Email',               NULL, 1, NULL, 3, '{ts escape="sql"}Email sent.{/ts}',                                                          0, 1, 1, NULL, NULL),
   (@option_group_id_act, '{ts escape="sql"}Text Message (SMS){/ts}',                 4, 'SMS',                 NULL, 1, NULL, 4, '{ts escape="sql"}Text message (SMS) sent.{/ts}',                                             0, 1, 1, NULL, NULL),
   (@option_group_id_act, '{ts escape="sql"}Event Registration{/ts}',                 5, 'Event Registration',  NULL, 1, NULL, 5, '{ts escape="sql"}Online or offline event registration.{/ts}',                                0, 1, 1, 1, NULL),
   (@option_group_id_act, '{ts escape="sql"}Contribution{/ts}',                       6, 'Contribution',        NULL, 1, NULL, 6, '{ts escape="sql"}Online or offline contribution.{/ts}',                                      0, 1, 1, 2, NULL),
   (@option_group_id_act, '{ts escape="sql"}Membership Signup{/ts}',                  7, 'Membership Signup',   NULL, 1, NULL, 7, '{ts escape="sql"}Online or offline membership signup.{/ts}',                                 0, 1, 1, 3, NULL),
   (@option_group_id_act, '{ts escape="sql"}Membership Renewal{/ts}',                 8, 'Membership Renewal',  NULL, 1, NULL, 8, '{ts escape="sql"}Online or offline membership renewal.{/ts}',                                0, 1, 1, 3, NULL),
   (@option_group_id_act, '{ts escape="sql"}Tell a Friend{/ts}',                      9, 'Tell a Friend',       NULL, 1, NULL, 9, '{ts escape="sql"}Send information about a contribution campaign or event to a friend.{/ts}', 0, 1, 1, NULL, NULL),
   (@option_group_id_act, '{ts escape="sql"}Pledge Acknowledgment{/ts}',              10, 'Pledge Acknowledgment',  NULL, 1, NULL, 10, '{ts escape="sql"}Send Pledge Acknowledgment.{/ts}',                                     0, 1, 1, 6, NULL),
   (@option_group_id_act, '{ts escape="sql"}Pledge Reminder{/ts}',                    11, 'Pledge Reminder',    NULL, 1, NULL, 11, '{ts escape="sql"}Send Pledge Reminder.{/ts}',                                               0, 1, 1, 6, NULL),
   (@option_group_id_act, '{ts escape="sql"}Inbound Email{/ts}',                      12, 'Inbound Email',      NULL, 1, NULL, 12, '{ts escape="sql"}Inbound Email.{/ts}',                                                      0, 1, 1, NULL, NULL),
  
-- Activity Types for case activities
   (@option_group_id_act, '{ts escape="sql"}Open Case{/ts}',          13, 'Open Case',          NULL, 0,  0, 13, '', 0, 0, 1 ,7, NULL),
   (@option_group_id_act, '{ts escape="sql"}Follow up{/ts}',          14, 'Follow up',          NULL, 0,  0, 14, '', 0, 0, 1, 7, NULL ),
   (@option_group_id_act, '{ts escape="sql"}Change Case Type{/ts}',   15, 'Change Case Type',   NULL, 0,  0, 15, '', 0, 0, 1, 7, NULL ),  
   (@option_group_id_act, '{ts escape="sql"}Change Case Status{/ts}', 16, 'Change Case Status', NULL, 0,  0, 16, '', 0, 0, 1, 7, NULL ),  
   (@option_group_id_act, '{ts escape="sql"}Close Case{/ts}',         17, 'Close Case',         NULL, 0,  0, 17, '', 0, 0, 1, 7, NULL ),  

   (@option_group_id_act, '{ts escape="sql"}Membership Renewal Reminder{/ts}',        18, 'Membership Renewal Reminder',  NULL, 1, NULL, 18, '{ts escape="sql"}offline membership renewal reminder.{/ts}',                      0, 1, 1, 3, NULL),
   (@option_group_id_act, '{ts escape="sql"}Change Case Start Date{/ts}',         19, 'Change Case Start Date',         NULL, 0,  0, 19, '', 0, 0, 1, 7, NULL ), 
   (@option_group_id_act, '{ts escape="sql"}Bulk Email{/ts}',                         20, 'Bulk Email',         NULL, 1, NULL, 20, '{ts escape="sql"}Bulk Email Sent.{/ts}',                                                    0, 1, 1, NULL, NULL),
   (@option_group_id_act, '{ts escape="sql"}Assign Case Role{/ts}',                   21, 'Assign Case Role', NULL,0, 0, 21, '', 0, 0, 1, 7, NULL),
   (@option_group_id_act, '{ts escape="sql"}Remove Case Role{/ts}',                   22, 'Remove Case Role', NULL,0, 0, 22, '', 0, 0, 1, 7, NULL),
   (@option_group_id_gender, '{ts escape="sql"}Female{/ts}',      1, 'Female',      NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_gender, '{ts escape="sql"}Male{/ts}',        2, 'Male',        NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_gender, '{ts escape="sql"}Transgender{/ts}', 3, 'Transgender', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),

   (@option_group_id_IMProvider, 'Yahoo', 1, 'Yahoo', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_IMProvider, 'MSN',   2, 'Msn',   NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_IMProvider, 'AIM',   3, 'Aim',   NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_IMProvider, 'GTalk', 4, 'Gtalk', NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_IMProvider, 'Jabber',5, 'Jabber',NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_IMProvider, 'Skype', 6, 'Skype', NULL, 0, NULL, 6, NULL, 0, 0, 1, NULL, NULL),

   (@option_group_id_mobileProvider, 'Sprint'  , 1, 'Sprint'  , NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_mobileProvider, 'Verizon' , 2, 'Verizon' , NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_mobileProvider, 'Cingular', 3, 'Cingular', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),

   (@option_group_id_prefix, '{ts escape="sql"}Mrs{/ts}', 1, 'Mrs', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_prefix, '{ts escape="sql"}Ms{/ts}',  2, 'Ms', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_prefix, '{ts escape="sql"}Mr{/ts}',  3, 'Mr', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_prefix, '{ts escape="sql"}Dr{/ts}',  4, 'Dr', NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),

   (@option_group_id_suffix, '{ts escape="sql"}Jr{/ts}',  1, 'Jr', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_suffix, '{ts escape="sql"}Sr{/ts}',  2, 'Sr', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_suffix, 'II',  3, 'II', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_suffix, 'III', 4, 'III', NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_suffix, 'IV',  5, 'IV',  NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_suffix, 'V',   6, 'V',   NULL, 0, NULL, 6, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_suffix, 'VI',  7, 'VI',  NULL, 0, NULL, 7, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_suffix, 'VII', 8, 'VII', NULL, 0, NULL, 8, NULL, 0, 0, 1, NULL, NULL),

   (@option_group_id_aclRole, '{ts escape="sql"}Administrator{/ts}',  1, 'Admin', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_aclRole, '{ts escape="sql"}Authenticated{/ts}',  2, 'Auth' , NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL),

   (@option_group_id_acc, 'Visa'      ,  1, 'Visa'      , NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_acc, 'MasterCard',  2, 'MasterCard', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_acc, 'Amex'      ,  3, 'Amex'      , NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
   (@option_group_id_acc, 'Discover'  ,  4, 'Discover'  , NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_pi, '{ts escape="sql"}Credit Card{/ts}',  1, 'Credit Card', NULL, 0, NULL, 1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_pi, '{ts escape="sql"}Debit Card{/ts}',  2, 'Debit Card', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pi, '{ts escape="sql"}Cash{/ts}',  3, 'Cash', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pi, '{ts escape="sql"}Check{/ts}',  4, 'Check', NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pi, '{ts escape="sql"}EFT{/ts}',  5, 'EFT', NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_cs, '{ts escape="sql"}Completed{/ts}'  , 1, 'Completed'  , NULL, 0, NULL, 1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_cs, '{ts escape="sql"}Pending{/ts}'    , 2, 'Pending'    , NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_cs, '{ts escape="sql"}Cancelled{/ts}'  , 3, 'Cancelled'  , NULL, 0, NULL, 3, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_cs, '{ts escape="sql"}Failed{/ts}'     , 4, 'Failed'     , NULL, 0, NULL, 4, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_cs, '{ts escape="sql"}In Progress{/ts}', 5, 'In Progress', NULL, 0, NULL, 5, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_cs, '{ts escape="sql"}Overdue{/ts}'    , 6, 'Overdue'    , NULL, 0, NULL, 6, NULL, 0, 1, 1, NULL, NULL),

  (@option_group_id_pcp, '{ts escape="sql"}Waiting Review{/ts}', 1, 'Waiting Review', NULL, 0, NULL, 1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_pcp, '{ts escape="sql"}Approved{/ts}'      , 2, 'Approved'      , NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_pcp, '{ts escape="sql"}Not Approved{/ts}'  , 3, 'Not Approved'  , NULL, 0, NULL, 3, NULL, 0, 1, 1, NULL, NULL),

  (@option_group_id_pRole, '{ts escape="sql"}Attendee{/ts}',  1, 'Attendee',  NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pRole, '{ts escape="sql"}Volunteer{/ts}', 2, 'Volunteer', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pRole, '{ts escape="sql"}Host{/ts}',      3, 'Host',      NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pRole, '{ts escape="sql"}Speaker{/ts}',   4, 'Speaker',   NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_etype, '{ts escape="sql"}Conference{/ts}', 1, 'Conference',  NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_etype, '{ts escape="sql"}Exhibition{/ts}', 2, 'Exhibition',  NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_etype, '{ts escape="sql"}Fundraiser{/ts}', 3, 'Fundraiser',  NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_etype, '{ts escape="sql"}Meeting{/ts}',    4, 'Meeting',     NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_etype, '{ts escape="sql"}Performance{/ts}',5, 'Performance', NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_etype, '{ts escape="sql"}Workshop{/ts}',   6, 'Workshop',    NULL, 0, NULL, 6, NULL, 0, 0, 1, NULL, NULL),

-- note that these are not ts'ed since they are used for logic in most cases and not display
-- they are used for display only in the prefernces field settings
  (@option_group_id_cvOpt, '{ts escape="sql"}Activities{/ts}'   ,   1, 'activity', NULL, 0, NULL,  1,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Relationships{/ts}',   2, 'rel', NULL, 0, NULL,  2,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Groups{/ts}'       ,   3, 'group', NULL, 0, NULL,  3,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Notes{/ts}'        ,   4, 'note', NULL, 0, NULL,  4,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Tags{/ts}'         ,   5, 'tag', NULL, 0, NULL,  5,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Change Log{/ts}'   ,   6, 'log', NULL, 0, NULL,  6,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Contributions{/ts}',   7, 'CiviContribute', NULL, 0, NULL,  7,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Memberships{/ts}'  ,   8, 'CiviMember', NULL, 0, NULL,  8,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Events{/ts}'       ,   9, 'CiviEvent', NULL, 0, NULL,  9,  NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Cases{/ts}'        ,  10, 'CiviCase', NULL, 0, NULL,  10, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Grants{/ts}'       ,  11, 'CiviGrant', NULL, 0, NULL,  11, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_cvOpt, '{ts escape="sql"}Pledges{/ts}'      ,  13, 'CiviPledge', NULL, 0, NULL,  13, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_ceOpt, '{ts escape="sql"}Custom Data{/ts}'              ,   1, 'CustomData', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_ceOpt, '{ts escape="sql"}Address{/ts}'                  ,   2, 'Address', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_ceOpt, '{ts escape="sql"}Communication Preferences{/ts}',   3, 'CommunicationPreferences', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_ceOpt, '{ts escape="sql"}Notes{/ts}'                    ,   4, 'Notes', NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_ceOpt, '{ts escape="sql"}Demographics{/ts}'             ,   5, 'Demographics', NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_ceOpt, '{ts escape="sql"}Tags and Groups{/ts}'          ,   6, 'TagsAndGroups', NULL, 0, NULL, 6, NULL, 0, 0, 1, NULL, NULL),
 (@option_group_id_ceOpt, '{ts escape="sql"}Email{/ts}'                     ,   7, 'Email', NULL, 1, NULL, 7, NULL, 0, 0, 1, NULL, NULL),
 (@option_group_id_ceOpt, '{ts escape="sql"}Phone{/ts}'                     ,   8, 'Phone', NULL, 1, NULL, 8, NULL, 0, 0, 1, NULL, NULL),
 (@option_group_id_ceOpt, '{ts escape="sql"}Instant Messenger{/ts}'         ,   9, 'IM', NULL, 1, NULL, 9, NULL, 0, 0, 1, NULL, NULL),
 (@option_group_id_ceOpt, '{ts escape="sql"}Open ID{/ts}'                   ,   10, 'OpenID', NULL, 1, NULL, 10, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_asOpt, '{ts escape="sql"}Address Fields{/ts}'          ,   1, 'location', NULL, 0, NULL,  1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Custom Fields{/ts}'           ,   2, 'custom', NULL, 0, NULL,  2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Activities{/ts}'              ,   3, 'activity', NULL, 0, NULL,  4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Relationships{/ts}'           ,   4, 'relationship', NULL, 0, NULL,  5, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Notes{/ts}'                   ,   5, 'notes', NULL, 0, NULL,  6, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Change{/ts} Log'              ,   6, 'changeLog', NULL, 0, NULL,  7, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Contributions{/ts}'           ,   7, 'CiviContribute', NULL, 0, NULL,  8, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Memberships{/ts}'             ,   8, 'CiviMember', NULL, 0, NULL,  9, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Events{/ts}'                  ,   9, 'CiviEvent', NULL, 0, NULL, 10, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Cases{/ts}'                   ,  10, 'CiviCase', NULL, 0, NULL, 11, NULL, 0, 0, 1, NULL, NULL),
  {if 0} {* Temporary hack to eliminate Kabissa checkbox in site preferences. *}
    (@option_group_id_asOpt, 'Kabissa'                                     ,  11, NULL, NULL, 0, NULL, 13, NULL, 0, 0, 1, NULL, NULL),
  {/if}
  (@option_group_id_asOpt, 'Grants'                                        ,  12, 'CiviGrant', NULL, 0, NULL, 14, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Demographics{/ts}'            ,  13, 'demographics', NULL, 0, NULL, 15, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Pledges{/ts}'                 ,  15, 'CiviPledge', NULL, 0, NULL, 17, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Contact Type{/ts}'            ,  16, 'contactType', NULL, 0, NULL, 18, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Groups{/ts}'                  ,  17, 'groups', NULL, 0, NULL, 19, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_asOpt, '{ts escape="sql"}Tags{/ts}'                    ,  18, 'tags', NULL, 0, NULL, 20, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_udOpt, '{ts escape="sql"}Groups{/ts}'                     , 1, 'Groups', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_udOpt, '{ts escape="sql"}Contributions{/ts}'              , 2, 'CiviContribute', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_udOpt, '{ts escape="sql"}Memberships{/ts}'                , 3, 'CiviMember', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_udOpt, '{ts escape="sql"}Events{/ts}'                     , 4, 'CiviEvent', NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_udOpt, '{ts escape="sql"}My Contacts / Organizations{/ts}', 5, 'Permissioned Orgs', NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_udOpt, '{ts escape="sql"}Pledges{/ts}'                    , 7, 'CiviPledge', NULL, 0, NULL, 7, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_udOpt, '{ts escape="sql"}Personal Campaign Pages{/ts}'    , 8, 'PCP', NULL, 0, NULL, 8, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_adOpt, '{ts escape="sql"}Street Address{/ts}'    ,  1, 'street_address', NULL, 0, NULL,  1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}Addt'l Address 1{/ts}'  ,  2, 'supplemental_address_1', NULL, 0, NULL,  2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}Addt'l Address 2{/ts}'  ,  3, 'supplemental_address_2', NULL, 0, NULL,  3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}City{/ts}'              ,  4, 'city'          , NULL, 0, NULL,  4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}Zip / Postal Code{/ts}' ,  5, 'postal_code'   , NULL, 0, NULL,  5, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}Postal Code Suffix{/ts}',  6, 'postal_code_suffix', NULL, 0, NULL,  6, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}County{/ts}'            ,  7, 'county'        , NULL, 0, NULL,  7, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}State / Province{/ts}'  ,  8, 'state_province', NULL, 0, NULL,  8, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}Country{/ts}'           ,  9, 'country'       , NULL, 0, NULL,  9, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}Latitude{/ts}'          , 10, 'geo_code_1'    , NULL, 0, NULL, 10, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}Longitude{/ts}'         , 11, 'geo_code_2', NULL, 0, NULL, 11, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_adOpt, '{ts escape="sql"}Address Name{/ts}'      , 12, 'address_name', NULL, 0, NULL, 12, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_gType, 'Access Control'  , 1, NULL, NULL, 0, NULL, 1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_gType, 'Mailing List'    , 2, NULL, NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL),

  (@option_group_id_grantSt, '{ts escape="sql"}Pending{/ts}',  1, 'Pending',  NULL, 0, 1,    1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_grantSt, '{ts escape="sql"}Granted{/ts}',  2, 'Granted',  NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_grantSt, '{ts escape="sql"}Rejected{/ts}', 3, 'Rejected', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_grantTyp, '{ts escape="sql"}Emergency{/ts}'          , 1, 'Emergency'         , NULL, 0, 1,    1, NULL, 0, 0, 1, NULL, NULL),    
  (@option_group_id_grantTyp, '{ts escape="sql"}Family Support{/ts}'     , 2, 'Family Support'    , NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_grantTyp, '{ts escape="sql"}General Protection{/ts}' , 3, 'General Protection', NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_grantTyp, '{ts escape="sql"}Impunity{/ts}'           , 4, 'Impunity'          , NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_honorTyp, '{ts escape="sql"}In Honor of{/ts}'        , 1, 'In Honor of'       , NULL, 0, 1,    1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_honorTyp, '{ts escape="sql"}In Memory of{/ts}'       , 2, 'In Memory of'      , NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL),

  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_Sample'               , 1, 'CRM_Contact_Form_Search_Custom_Sample'      , NULL, 0, NULL, 1, '{ts escape="sql"}Household Name and State{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_ContributionAggregate', 2, 'CRM_Contact_Form_Search_Custom_ContributionAggregate', NULL, 0, NULL, 2, '{ts escape="sql"}Contribution Aggregate{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_Basic'                , 3, 'CRM_Contact_Form_Search_Custom_Basic'       , NULL, 0, NULL, 3, '{ts escape="sql"}Basic Search{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_Group'                , 4, 'CRM_Contact_Form_Search_Custom_Group'       , NULL, 0, NULL, 4, '{ts escape="sql"}Include / Exclude Contacts in a Group / Tag{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_PostalMailing'        , 5, 'CRM_Contact_Form_Search_Custom_PostalMailing', NULL, 0, NULL, 5, '{ts escape="sql"}Postal Mailing{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_Proximity'            , 6, 'CRM_Contact_Form_Search_Custom_Proximity', NULL, 0, NULL, 6, '{ts escape="sql"}Proximity Search{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_EventAggregate'       , 7, 'CRM_Contact_Form_Search_Custom_EventAggregate', NULL, 0, NULL, 7, '{ts escape="sql"}Event Aggregate{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_ActivitySearch'       , 8, 'CRM_Contact_Form_Search_Custom_ActivitySearch', NULL, 0, NULL, 8, '{ts escape="sql"}Activity Search{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_PriceSet'             , 9, 'CRM_Contact_Form_Search_Custom_PriceSet', NULL, 0, NULL, 9, '{ts escape="sql"}Price Set Details for Event Participants{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_ZipCodeRange'         ,10, 'CRM_Contact_Form_Search_Custom_ZipCodeRange', NULL, 0, NULL, 10, '{ts escape="sql"}Zip Code Range{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_DateAdded'            ,11, 'CRM_Contact_Form_Search_Custom_DateAdded', NULL, 0, NULL, 11, '{ts escape="sql"}Date Added to CiviCRM{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_MultipleValues'       ,12, 'CRM_Contact_Form_Search_Custom_MultipleValues', NULL, 0, NULL, 12, '{ts escape="sql"}Custom Group Multiple Values Listing{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_ContribSYBNT'         ,13, 'CRM_Contact_Form_Search_Custom_ContribSYBNT', NULL, 0, NULL, 13, '{ts escape="sql"}Contributions made in Year X and not Year Y{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_TagContributions'     ,14, 'CRM_Contact_Form_Search_Custom_TagContributions', NULL, 0, NULL, 14, '{ts escape="sql"}Find Contribution Amounts by Tag{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_csearch , 'CRM_Contact_Form_Search_Custom_FullText'             ,15, 'CRM_Contact_Form_Search_Custom_FullText', NULL, 0, NULL, 15, '{ts escape="sql"}Full-text Search{/ts}', 0, 0, 1, NULL, NULL),

-- report templates
  (@option_group_id_report , '{ts escape="sql"}Constituent Report (Summary){/ts}',            'contact/summary',                'CRM_Report_Form_Contact_Summary',                NULL, 0, NULL, 1,  '{ts escape="sql"}Provides a list of address and telephone information for constituent records in your system.{/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_report , '{ts escape="sql"}Constituent Report (Detail){/ts}',             'contact/detail',                 'CRM_Report_Form_Contact_Detail',                 NULL, 0, NULL, 2,  '{ts escape="sql"}Provides contact-related information on contributions, memberships, events and activities.{/ts}',   0, 0, 1, NULL, NULL),
  (@option_group_id_report , '{ts escape="sql"}Activity Report{/ts}',                         'activity',                       'CRM_Report_Form_Activity',                       NULL, 0, NULL, 3,  '{ts escape="sql"}Provides a list of constituent activity including activity statistics for one/all contacts during a given date range(required){/ts}', 0, 0, 1, NULL, NULL),
  (@option_group_id_report , '{ts escape="sql"}Walk / Phone List Report{/ts}',                'walklist',                       'CRM_Report_Form_Walklist',                       NULL, 0, NULL, 4,  '{ts escape="sql"}Provides a detailed report for your walk/phonelist for targetted contacts{/ts}', 0, 0, 0, NULL, NULL),
  (@option_group_id_report , '{ts escape="sql"}Current Employer Report{/ts}',                 'contact/currentEmployer',        'CRM_Report_Form_Contact_CurrentEmployer',        NULL, 0, NULL, 5,  '{ts escape="sql"}Provides detail list of employer employee relationships along with employment details Ex Join Date{/ts}', 0, 0, 0, NULL, NULL),
  (@option_group_id_report , '{ts escape="sql"}Donor Report (Summary){/ts}',                  'contribute/summary',             'CRM_Report_Form_Contribute_Summary',             NULL, 0, NULL, 6,  '{ts escape="sql"}Shows contribution statistics by month / week / year .. country / state .. type.{/ts}', 0, 0, 1, @contributeCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Donor Report (Detail){/ts}',                   'contribute/detail',              'CRM_Report_Form_Contribute_Detail',              NULL, 0, NULL, 7,  '{ts escape="sql"}Lists detailed contribution(s) for one / all contacts. Contribution summary report points to this report for specific details.{/ts}', 0, 0, 1, @contributeCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Donation Summary Report (Repeat){/ts}',        'contribute/repeat',              'CRM_Report_Form_Contribute_Repeat',              NULL, 0, NULL, 8,  '{ts escape="sql"}Given two date ranges, shows contacts (and their contributions) who contributed in both the date ranges with percentage increase / decrease.{/ts}', 0, 0, 1, @contributeCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Donation Summary Report (Organization){/ts}',  'contribute/organizationSummary', 'CRM_Report_Form_Contribute_OrganizationSummary', NULL, 0, NULL, 9,  '{ts escape="sql"}Displays a detailed contribution report for Organization relationships with contributors, as to if contribution done was  from an employee of some organization or from that Organization itself.{/ts}', 0, 0, 1, @contributeCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Donation Summary Report (Household){/ts}',     'contribute/householdSummary',    'CRM_Report_Form_Contribute_HouseholdSummary',    NULL, 0, NULL, 10, '{ts escape="sql"}Provides a detailed report for Contributions made by contributors(Or Household itself) who are having a relationship with household (For ex a Contributor is Head of Household for some household or is a member of.){/ts}', 0, 0, 1, @contributeCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Top Donors Report{/ts}',                       'contribute/topDonor',            'CRM_Report_Form_Contribute_TopDonor',            NULL, 0, NULL, 11, '{ts escape="sql"}Provides a list of the top donors during a time period you define. You can include as many donors as you want (for example, top 100 of your donors).{/ts}', 0, 0, 1, @contributeCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}SYBUNT Report{/ts}',                           'contribute/sybunt',              'CRM_Report_Form_Contribute_Sybunt',              NULL, 0, NULL, 12, '{ts escape="sql"}Some year(s) but not this year. Provides a list of constituents who donated at some time in the history of your organization but did not donate during the time period you specify.{/ts}', 0, 0, 1, @contributeCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}LYBUNT Report{/ts}',                           'contribute/lybunt',              'CRM_Report_Form_Contribute_Lybunt',              NULL, 0, NULL, 13, '{ts escape="sql"}Last year but not this year. Provides a list of constituents who donated last year but did not donate during the time period you specify as the current year.{/ts}', 0, 0, 1, @contributeCompId, NULL),	
  (@option_group_id_report , '{ts escape="sql"}Soft Credit Report{/ts}',                      'contribute/softcredit',          'CRM_Report_Form_Contribute_SoftCredit',          NULL, 0, NULL, 14, '{ts escape="sql"}Soft Credit details.{/ts}', 0, 0, 1,@contributeCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Membership Report (Summary){/ts}',             'member/summary',                 'CRM_Report_Form_Member_Summary',                 NULL, 0, NULL, 15, '{ts escape="sql"}Provides a summary of memberships by type and join date.{/ts}', 0, 0, 1, @memberCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Membership Report (Detail){/ts}',              'member/detail',                  'CRM_Report_Form_Member_Detail',                  NULL, 0, NULL, 16, '{ts escape="sql"}Provides a list of members along with their membership status and membership details (Join Date, Start Date, End Date).{/ts}', 0, 0, 1, @memberCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Membership Report (Lapsed){/ts}',              'member/lapse',                   'CRM_Report_Form_Member_Lapse',                   NULL, 0, NULL, 17, '{ts escape="sql"}Provides a list of memberships that lapsed or will lapse before the date you specify.{/ts}', 0, 0, 1, @memberCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Event Participant Report (List){/ts}',         'event/participantListing',       'CRM_Report_Form_Event_ParticipantListing',       NULL, 0, NULL, 18, '{ts escape="sql"}Provides lists of participants for an event.{/ts}', 0, 0, 1, @eventCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Event Income Report (Summary){/ts}',           'event/summary',                  'CRM_Report_Form_Event_Summary',                  NULL, 0, NULL, 19, '{ts escape="sql"}Provides an overview of event income. You can include key information such as event ID, registration, attendance, and income generated to help you determine the success of an event.{/ts}', 0, 0, 1, @eventCompId, NULL),			
  (@option_group_id_report , '{ts escape="sql"}Event Income Report (Detail){/ts}',            'event/income',                   'CRM_Report_Form_Event_Income',                   NULL, 0, NULL, 20, '{ts escape="sql"}Helps you to analyze the income generated by an event. The report can include details by participant type, status and payment method.{/ts}', 0, 0, 1, @eventCompId, NULL),
  (@option_group_id_report , '{ts escape="sql"}Pledge Report{/ts}',                           'pledge/summary',                 'CRM_Report_Form_Pledge_Summary',                 NULL, 0, NULL, 21, '{ts escape="sql"}Pledge Report{/ts}', 0, 0, 1, @pledgeCompId, NULL),			
  (@option_group_id_report , '{ts escape="sql"}Pledged But not Paid Report{/ts}',             'pledge/pbnp',                    'CRM_Report_Form_Pledge_Pbnp',                    NULL, 0, NULL, 22, '{ts escape="sql"}Pledged but not Paid Report{/ts}', 0, 0, 1, @pledgeCompId, NULL),  
  (@option_group_id_report , '{ts escape="sql"}Relationship Report{/ts}',                     'contact/relationship',           'CRM_Report_Form_Contact_Relationship',           NULL, 0, NULL, 23, '{ts escape="sql"}Relationship Report{/ts}', 0, 0, 1, NULL, NULL),  	

  (@option_group_id_acs, '{ts escape="sql"}Scheduled{/ts}',  1, 'Scheduled',  NULL, 0, 1,    1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_acs, '{ts escape="sql"}Completed{/ts}',  2, 'Completed',  NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_acs, '{ts escape="sql"}Cancelled{/ts}',  3, 'Cancelled',  NULL, 0, NULL, 3, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_acs, '{ts escape="sql"}Left Message{/ts}', 4, 'Left Message', NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_acs, '{ts escape="sql"}Unreachable{/ts}', 5, 'Unreachable', NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_acs, '{ts escape="sql"}Not Required{/ts}',  6, 'Not Required',  NULL, 0, NULL, 6, NULL, 0, 0, 1, NULL, NULL),

  (@option_group_id_cas, '{ts escape="sql"}Ongoing{/ts}' , 1, 'Open'  ,  NULL, 0, 1,    1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_cas, '{ts escape="sql"}Resolved{/ts}', 2, 'Closed',  NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_cas, '{ts escape="sql"}Urgent{/ts}'  , 3, 'Urgent',    NULL, 0, NULL, 3, NULL, 0, 1, 1, NULL, NULL),

  (@option_group_id_pl, '{ts escape="sql"}Name Only{/ts}'     , 1, 'Name Only'      ,  NULL, 0, 0, 1, 'CRM_Event_Page_ParticipantListing_Name', 0, 1, 1, NULL, NULL),
  (@option_group_id_pl, '{ts escape="sql"}Name and Email{/ts}', 2, 'Name and Email' ,  NULL, 0, 0, 2, 'CRM_Event_Page_ParticipantListing_NameAndEmail', 0, 1, 1, NULL, NULL),
  (@option_group_id_pl, '{ts escape="sql"}Name, Status and Register Date{/ts}' , 3, 'Name, Status and Register Date',  NULL, 0, 0, 3, 'CRM_Event_Page_ParticipantListing_NameStatusAndDate', 0, 1, 1, NULL, NULL),

  (@option_group_id_sfe, 'jpg'      ,  1, NULL   ,  NULL, 0, 0,  1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'jpeg'     ,  2, NULL   ,  NULL, 0, 0,  2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'png'      ,  3, NULL   ,  NULL, 0, 0,  3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'gif'      ,  4, NULL   ,  NULL, 0, 0,  4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'txt'      ,  5, NULL   ,  NULL, 0, 0,  5, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'pdf'      ,  6, NULL   ,  NULL, 0, 0,  6, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'doc'      ,  7, NULL   ,  NULL, 0, 0,  7, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'xls'      ,  8, NULL   ,  NULL, 0, 0,  8, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'rtf'      ,  9, NULL   ,  NULL, 0, 0,  9, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'csv'      , 10, NULL   ,  NULL, 0, 0, 10, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_sfe, 'ppt'      , 11, NULL   ,  NULL, 0, 0, 11, NULL, 0, 0, 1, NULL, NULL),
 
  (@option_group_id_we, 'TinyMCE'    , 1, NULL, NULL, 0, NULL, 1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_we, 'FCKEditor'  , 2, NULL, NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL), 

  (@option_group_id_mt, '{ts escape="sql"}Search Builder{/ts}',      1, 'Search Builder',      NULL, 0, 0,    1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Import Contact{/ts}',      2, 'Import Contact',      NULL, 0, 0,    2, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Import Activity{/ts}',     3, 'Import Activity',     NULL, 0, 0,    3, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Import Contribution{/ts}', 4, 'Import Contribution', NULL, 0, 0,    4, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Import Membership{/ts}',   5, 'Import Membership',   NULL, 0, 0,    5, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Import Participant{/ts}',  6, 'Import Participant',  NULL, 0, 0,    6, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Export Contact{/ts}',      7, 'Export Contact',      NULL, 0, 0,    7, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Export Contribution{/ts}', 8, 'Export Contribution', NULL, 0, 0,    8, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Export Membership{/ts}',   9, 'Export Membership',   NULL, 0, 0,    9, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Export Participant{/ts}', 10, 'Export Participant',  NULL, 0, 0,   10, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Export Pledge{/ts}',      11, 'Export Pledge',       NULL, 0, 0,   11, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_mt, '{ts escape="sql"}Export Case{/ts}',        12, 'Export Case',         NULL, 0, 0,   12, NULL, 0, 1, 1, NULL, NULL),

  (@option_group_id_fu, '{ts escape="sql"}daily{/ts}'    , 'day'  ,    'day',  NULL, 0, NULL, 1, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_fu, '{ts escape="sql"}weekly{/ts}'   , 'week' ,   'week',  NULL, 0, NULL, 2, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_fu, '{ts escape="sql"}monthly{/ts}'  , 'month',  'month',  NULL, 0, NULL, 3, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_fu, '{ts escape="sql"}yearly{/ts}'   , 'year' ,   'year',  NULL, 0, NULL, 4, NULL, 0, 1, 1, NULL, NULL),

-- phone types.
  (@option_group_id_pht, '{ts escape="sql"}Phone{/ts}' ,        1, 'Phone'      , NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pht, '{ts escape="sql"}Mobile{/ts}',        2, 'Mobile'     , NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pht, '{ts escape="sql"}Fax{/ts}'   ,        3, 'Fax'        , NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pht, '{ts escape="sql"}Pager{/ts}' ,        4, 'Pager'      , NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_pht, '{ts escape="sql"}Voicemail{/ts}' ,    5, 'Voicemail'  , NULL, 0, NULL, 5, NULL, 0, 0, 1, NULL, NULL),

-- from email address.
  (@option_group_id_fma, '"FIXME"<info@FIXME.ORG>', '1', '"FIXME"<info@FIXME.ORG>', NULL, 0, 1, 1, '{ts escape="sql"}Default domain email address and from name.{/ts}', 0, 0, 1, NULL, NULL ),

-- custom data types.
  (@option_group_id_cdt, 'Participant Role', '1', 'ParticipantRole', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL ),
  (@option_group_id_cdt, 'Participant Event Name', '2', 'ParticipantEventName', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL ),

-- visibility.
  (@option_group_id_vis, 'Public', 1, 'public', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL ),
  (@option_group_id_vis, 'Admin', 2, 'admin', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL ),	

-- mail protocol.
  (@option_group_id_mp, 'IMAP',    1, 'IMAP',    NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL ),
  (@option_group_id_mp, 'Maildir', 2, 'Maildir', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL ),	
  (@option_group_id_mp, 'POP3',    3, 'POP3',    NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL ),
  (@option_group_id_mp, 'Localdir', 4, 'Localdir', NULL, 0, NULL, 4, NULL, 0, 0, 1, NULL, NULL ),

-- priority
  (@option_group_id_priority, '{ts escape="sql"}Urgent{/ts}', 1, 'Urgent', NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_priority, '{ts escape="sql"}Normal{/ts}', 2, 'Normal', NULL, 0, NULL, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_priority, '{ts escape="sql"}Low{/ts}',    3, 'Low',    NULL, 0, NULL, 3, NULL, 0, 0, 1, NULL, NULL),

-- redaction rule
  (@option_group_id_rr, 'Vancouver', 'city_', NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_rr, '{literal}/(19|20)(\\d{2})-(\\d{1,2})-(\\d{1,2})/{/literal}', 'date_', NULL, NULL, 1, NULL, 2, NULL, 0, 0, 1, NULL, NULL),

-- email greeting.
  (@option_group_id_emailGreeting, '{literal}Dear {contact.first_name}{/literal}',                                                 1, '{literal}Dear {contact.first_name}{/literal}',                                                 NULL,    1, 1, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_emailGreeting, '{literal}Dear {contact.individual_prefix} {contact.first_name} {contact.last_name}{/literal}', 2, '{literal}Dear {contact.individual_prefix} {contact.first_name} {contact.last_name}{/literal}', NULL,    1, 0, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_emailGreeting, '{literal}Dear {contact.individual_prefix} {contact.last_name}{/literal}',                      3, '{literal}Dear {contact.individual_prefix} {contact.last_name}{/literal}',                      NULL,    1, 0, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_emailGreeting, '{literal}Customized{/literal}',                                                                4, '{literal}Customized{/literal}',                                                                NULL, NULL, 0, 4, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_emailGreeting, '{literal}Dear {contact.household_name}{/literal}',                                             5, '{literal}Dear {contact.househols_name}{/literal}',                                             NULL,    2, 1, 5, NULL, 0, 0, 1, NULL, NULL),
-- postal greeting.
  (@option_group_id_postalGreeting, '{literal}Dear {contact.first_name}{/literal}',                                                 1, '{literal}Dear {contact.first_name}{/literal}',                                                 NULL,    1, 1, 1, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_postalGreeting, '{literal}Dear {contact.individual_prefix} {contact.first_name} {contact.last_name}{/literal}', 2, '{literal}Dear {contact.individual_prefix} {contact.first_name} {contact.last_name}{/literal}', NULL,    1, 0, 2, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_postalGreeting, '{literal}Dear {contact.individual_prefix} {contact.last_name}{/literal}',                      3, '{literal}Dear {contact.individual_prefix} {contact.last_name}{/literal}',                      NULL,    1, 0, 3, NULL, 0, 0, 1, NULL, NULL),
  (@option_group_id_postalGreeting, '{literal}Customized{/literal}',                                                                4, '{literal}Customized{/literal}',                                                                NULL, NULL, 0, 4, NULL, 0, 1, 1, NULL, NULL),
  (@option_group_id_postalGreeting, '{literal}Dear {contact.household_name}{/literal}',                                             5, '{literal}Dear {contact.househols_name}{/literal}',                                             NULL,    2, 1, 5, NULL, 0, 0, 1, NULL, NULL),
-- addressee.
  (@option_group_id_addressee, '{literal}{contact.individual_prefix}{ } {contact.first_name}{ }{contact.middle_name}{ }{contact.last_name}{ }{contact.individual_suffix}{/literal}',          '1', '{literal}}{contact.individual_prefix}{ } {contact.first_name}{ }{contact.middle_name}{ }{contact.last_name}{ }{contact.individual_suffix}{/literal}',         NULL ,   '1', '1', '1', NULL , '0', '0', '1', NULL , NULL),
  (@option_group_id_addressee, '{literal}{contact.household_name}{/literal}',    '2', '{literal}{contact.household_name}{/literal}',    NULL ,   '2', '0', '2', NULL , '0', '0', '1', NULL , NULL),
  (@option_group_id_addressee, '{literal}{contact.organization_name}{/literal}', '3', '{literal}{contact.organization_name}{/literal}', NULL ,   '3', '0', '3', NULL , '0', '0', '1', NULL , NULL),
  (@option_group_id_addressee, '{literal}Customized{/literal}',                  '4', '{literal}Customized{/literal}',                  NULL , NULL , '0', '4', NULL , '0', '1', '1', NULL , NULL);

-- /*******************************************************
-- *
-- * Encounter Medium Option Values (for case activities)
-- *
-- *******************************************************/
INSERT INTO `civicrm_option_group` (name, label, description, is_reserved, is_active)
    VALUES  ('encounter_medium', 'Encounter Medium', 'Encounter medium for case activities (e.g. In Person, By Phone, etc.)', 1, 1);
SELECT @option_group_id_medium        := max(id) from civicrm_option_group where name = 'encounter_medium';
INSERT INTO
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`)
VALUES
    (@option_group_id_medium, 'In Person',  1, 'in_person', NULL, 0,  0, 1, NULL, 0, 1, 1),
    (@option_group_id_medium, 'Phone',  2, 'phone', NULL, 0,  1, 2, NULL, 0, 1, 1),
    (@option_group_id_medium, 'Email',  3, 'email', NULL, 0,  0, 3, NULL, 0, 1, 1),
    (@option_group_id_medium, 'Fax',  4, 'fax', NULL, 0,  0, 4, NULL, 0, 1, 1),
    (@option_group_id_medium, 'Letter Mail',  5, 'letter_mail', NULL, 0,  0, 5, NULL, 0, 1, 1);

-- sample membership status entries
INSERT INTO
    civicrm_membership_status(name, start_event, start_event_adjust_unit, start_event_adjust_interval, end_event, end_event_adjust_unit, end_event_adjust_interval, is_current_member, is_admin, weight, is_default, is_active, is_reserved)
VALUES
    ('{ts escape="sql"}New{/ts}', 'join_date', null, null,'join_date','month',3, 1, 0, 1, 0, 1, 0),
    ('{ts escape="sql"}Current{/ts}', 'start_date', null, null,'end_date', null, null, 1, 0, 2, 1, 1, 0),
    ('{ts escape="sql"}Grace{/ts}', 'end_date', null, null,'end_date','month', 1, 1, 0, 3, 0, 1, 0),
    ('{ts escape="sql"}Expired{/ts}', 'end_date', 'month', 1, null, null, null, 0, 0, 4, 0, 1, 0),
    ('{ts escape="sql"}Pending{/ts}', 'join_date', null, null,'join_date',null,null, 0, 0, 5, 0, 1, 1),
    ('{ts escape="sql"}Cancelled{/ts}', 'join_date', null, null,'join_date',null,null, 0, 0, 6, 0, 1, 0),
    ('{ts escape="sql"}Deceased{/ts}', null, null, null, null, null, null, 0, 1, 7, 0, 1, 1);

{literal}
-- Initial state of system preferences
INSERT INTO 
     civicrm_preferences(domain_id, contact_id, is_domain, location_count, contact_view_options, contact_edit_options, advanced_search_options, user_dashboard_options, address_options, address_format, mailing_format, address_standardization_provider, address_standardization_userid, address_standardization_url, editor_id, mailing_backend )
VALUES 
     (@domainID,NULL,1,1,'123456789101113','12345678910','1234567891011121315161718','1234578','1234568910111314','{contact.address_name}\n{contact.street_address}\n{contact.supplemental_address_1}\n{contact.supplemental_address_2}\n{contact.city}{, }{contact.state_province}{ }{contact.postal_code}\n{contact.country}','{contact.addressee}\n{contact.street_address}\n{contact.supplemental_address_1}\n{contact.supplemental_address_2}\n{contact.city}{, }{contact.state_province}{ }{contact.postal_code}\n{contact.country}',NULL,NULL,NULL,2,NULL);
{/literal}

INSERT INTO `civicrm_preferences_date`
  (name, start, end, minute_increment, format, description)
VALUES
  ( 'activityDate'    ,  20, 10,  0, null,        'Date for activities including contributions: receive, receipt, cancel. membership: join, start, renew. case: start, end.'         ),
  ( 'activityDatetime',  20, 10, 1, null,        'Date and time for activity: scheduled. participant: registered.'                                                                  ),
  ( 'birth'           , 100,  0,  0, null,        'Birth and deceased dates.'                                                                                                        ),
  ( 'creditCard'      ,   0, 10,  0, 'M Y',       'Month and year only for credit card expiration.'                                                                                  ),
  ( 'custom'          ,  20, 20, 15, 'Y M d h i A', 'Uses date range passed in by form field. Can pass in a posix date part parameter. Start and end offsets defined here are ignored.'),
  ( 'datetime'        ,  10,  3, 15, null,        'General date and time.'                                                                                                           ),
  ( 'duration'        ,   0,  0, 15, 'H i',       'Durations in hours and minutes.'                                                                                                  ),
  ( 'fixed'           ,   0,  5,  0, null,        'Not used ?'                                                                                                                       ),
  ( 'mailing'         ,   0,  1, 15, 'Y M d h i A', 'Date and time. Used for scheduling mailings.'                                                                                      ),
  ( 'manual'          ,  20, 20,  0, null,        'Date only. For non-general cases. Uses date range passed in by form field. Start and end offsets defined here are ignored.'       ),
  ( 'relative'        ,  20, 20,  0, null,        'Used in search forms.'                                                                                                            );


-- various processor options
--
-- Table structure for table `civicrm_payment_processor_type`
--

INSERT INTO `civicrm_payment_processor_type` 
 (name, title, description, is_active, is_default, user_name_label, password_label, signature_label, subject_label, class_name, url_site_default, url_api_default, url_recur_default, url_button_default, url_site_test_default, url_api_test_default, url_recur_test_default, url_button_test_default, billing_mode, is_recur )
VALUES 
 ('PayPal_Standard','{ts escape="sql"}PayPal - Website Payments Standard{/ts}',NULL,1,0,'{ts escape="sql"}Merchant Account Email{/ts}',NULL,NULL,NULL,'Payment_PayPalImpl','https://www.paypal.com/',NULL,'https://www.paypal.com/',NULL,'https://www.sandbox.paypal.com/',NULL,'https://www.sandbox.paypal.com/',NULL,4,1),
 ('PayPal','{ts escape="sql"}PayPal - Website Payments Pro{/ts}',NULL,1,0,'{ts escape="sql"}User Name{/ts}','{ts escape="sql"}Password{/ts}','{ts escape="sql"}Signature{/ts}',NULL,'Payment_PayPalImpl','https://www.paypal.com/','https://api-3t.paypal.com/',NULL,'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif','https://www.sandbox.paypal.com/','https://api-3t.sandbox.paypal.com/',NULL,'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',3,NULL),
 ('PayPal_Express','{ts escape="sql"}PayPal - Express{/ts}',NULL,1,0,'{ts escape="sql"}User Name{/ts}','{ts escape="sql"}Password{/ts}','{ts escape="sql"}Signature{/ts}',NULL,'Payment_PayPalImpl','https://www.paypal.com/','https://api-3t.paypal.com/',NULL,'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif','https://www.sandbox.paypal.com/','https://api-3t.sandbox.paypal.com/',NULL,'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',2,NULL),
 ('Google_Checkout','{ts escape="sql"}Google Checkout{/ts}',NULL,1,0,'{ts escape="sql"}Merchant ID{/ts}','{ts escape="sql"}Key{/ts}',NULL,NULL,'Payment_Google','https://checkout.google.com/',NULL,NULL,'https://checkout.google.com/buttons/checkout.gif?merchant_id=YOURMERCHANTIDHERE&w=160&h=43&style=white&variant=text&loc=en_US','https://sandbox.google.com/checkout/',NULL,NULL,'https://sandbox.google.com/checkout/buttons/checkout.gif?merchant_id=YOURMERCHANTIDHERE&w=160&h=43&style=white&variant=text&loc=en_US',4,NULL),
 ('Moneris','{ts escape="sql"}Moneris{/ts}',NULL,1,0,'{ts escape="sql"}User Name{/ts}','{ts escape="sql"}Password{/ts}','{ts escape="sql"}Store ID{/ts}',NULL,'Payment_Moneris','https://www3.moneris.com/',NULL,NULL,NULL,'https://esqa.moneris.com/',NULL,NULL,NULL,1,1),
 ('AuthNet_AIM','{ts escape="sql"}Authorize.Net - AIM{/ts}',NULL,1,0,'{ts escape="sql"}API Login{/ts}','{ts escape="sql"}Payment Key{/ts}','{ts escape="sql"}MD5 Hash{/ts}',NULL,'Payment_AuthorizeNet','https://secure.authorize.net/gateway/transact.dll',NULL,'https://api.authorize.net/xml/v1/request.api',NULL,'https://test.authorize.net/gateway/transact.dll',NULL,'https://apitest.authorize.net/xml/v1/request.api',NULL,1,NULL),
 ('PayJunction','{ts escape="sql"}PayJunction{/ts}',NULL,1,0,'User Name','Password',NULL,NULL,'Payment_PayJunction','https://payjunction.com/quick_link',NULL,NULL,NULL,'https://demo.payjunction.com/quick_link',NULL,NULL,NULL,1,1),
 ('eWAY', '{ts escape="sql"}eWAY (Single Currency){/ts}',NULL,1,0,'Customer ID',NULL,NULL,NULL,'Payment_eWAY','https://www.eway.com.au/gateway_cvn/xmlpayment.asp',NULL,NULL,NULL,'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp',NULL,NULL,NULL,1,0),
 ('Payment_Express', '{ts escape="sql"}DPS Payment Express{/ts}',NULL,1,0,'User ID','Key','Mac Key - pxaccess only',NULL,'Payment_PaymentExpress','https://www.paymentexpress.com/pleaseenteraurl',NULL,NULL,NULL,'https://www.paymentexpress.com/pleaseenteratesturl',NULL,NULL,NULL,4,0),
 ('ClickAndPledge', '{ts escape="sql"}Click and Pledge{/ts}',NULL,1,0,'Customer ID',NULL,NULL,NULL,'Payment_ClickAndPledge','http://www.clickandpledge.com/',NULL,NULL,NULL,'http://www.clickandpledge.com/',NULL,NULL,NULL,4,0),
 ('Dummy','{ts escape="sql"}Dummy Payment Processor{/ts}',NULL,1,1,'{ts escape="sql"}User Name{/ts}',NULL,NULL,NULL,'Payment_Dummy',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL),
('Elavon','{ts escape="sql"}Elavon Payment Processor{/ts}','{ts escape="sql"}Elavon / Nova Virtual Merchant{/ts}',1,0,'{ts escape="sql"}SSL Merchant ID {/ts}','{ts escape="sql"}SSL User ID{/ts}','{ts escape="sql"}SSL PIN{/ts}',NULL,'Payment_Elavon','https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do',NULL,NULL,NULL,'https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do',NULL,NULL,NULL,1,0);

-- the fuzzy default dedupe rules
INSERT INTO civicrm_dedupe_rule_group (contact_type, threshold, level, is_default) VALUES ('Individual', 20, 'Fuzzy', true);
SELECT @drgid := MAX(id) FROM civicrm_dedupe_rule_group;
INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES (@drgid, 'civicrm_contact', 'first_name', 5),
       (@drgid, 'civicrm_contact', 'last_name',  7),
       (@drgid, 'civicrm_email'  , 'email',     10);

INSERT INTO civicrm_dedupe_rule_group (contact_type, threshold, level, is_default) VALUES ('Organization', 10, 'Fuzzy', true);
SELECT @drgid := MAX(id) FROM civicrm_dedupe_rule_group;
INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES (@drgid, 'civicrm_contact', 'organization_name', 10),
       (@drgid, 'civicrm_email'  , 'email',             10);

INSERT INTO civicrm_dedupe_rule_group (contact_type, threshold, level, is_default) VALUES ('Household', 10, 'Fuzzy', true);
SELECT @drgid := MAX(id) FROM civicrm_dedupe_rule_group;
INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES (@drgid, 'civicrm_contact', 'household_name', 10),
       (@drgid, 'civicrm_email'  , 'email',          10);

-- the strict dedupe rules
INSERT INTO civicrm_dedupe_rule_group (contact_type, threshold, level, is_default) VALUES ('Individual', 10, 'Strict', true);
SELECT @drgid := MAX(id) FROM civicrm_dedupe_rule_group;
INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES (@drgid, 'civicrm_email', 'email', 10);

INSERT INTO civicrm_dedupe_rule_group (contact_type, threshold, level, is_default) VALUES ('Organization', 10, 'Strict', true);
SELECT @drgid := MAX(id) FROM civicrm_dedupe_rule_group;
INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES (@drgid, 'civicrm_contact', 'organization_name', 10),
       (@drgid, 'civicrm_email'  , 'email',             10);

INSERT INTO civicrm_dedupe_rule_group (contact_type, threshold, level, is_default) VALUES ('Household', 10, 'Strict', true);
SELECT @drgid := MAX(id) FROM civicrm_dedupe_rule_group;
INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES (@drgid, 'civicrm_contact', 'household_name', 10),
       (@drgid, 'civicrm_email'  , 'email',          10);

-- Sample counties (state-province and country lists defined in a separate tpl files)
INSERT INTO civicrm_county (name, state_province_id) VALUES ('Alameda', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('Contra Costa', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('Marin', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('San Francisco', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('San Mateo', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('Santa Clara', 1004);

-- Bounce classification patterns
INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('AOL', '{ts escape="sql"}AOL Terms of Service complaint{/ts}', 1);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (1, 'Client TOS Notification');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Away', '{ts escape="sql"}Recipient is on vacation{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (2, '(be|am)? (out of|away from) (the|my)? (office|computer|town)'),
    (2, 'i am on vacation');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Dns', '{ts escape="sql"}Unable to resolve recipient domain{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (3, 'name(server entry| lookup failure)'),
    (3, 'no (mail server|matches to nameserver query|dns entries)'),
    (3, 'reverse dns entry');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Host', '{ts escape="sql"}Unable to deliver to destintation mail server{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (4, '(unknown|not local) host'),
    (4, 'all hosts have been failing'),
    (4, 'allowed rcpthosts'),
    (4, 'connection (refused|timed out)'),
    (4, 'couldn\'t find any host named'),
    (4, 'error involving remote host'),
    (4, 'host unknown'),
    (4, 'invalid host name'),
    (4, 'isn\'t in my control/locals file'),
    (4, 'local configuration error'),
    (4, 'not a gateway'),
    (4, 'server is down or unreachable'),
    (4, 'too many connections'),
    (4, 'unable to connect');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Inactive', '{ts escape="sql"}User account is no longer active{/ts}', 1);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (5, '(my )?e-?mail( address)? has changed'),
    (5, 'account (inactive|expired|deactivated)'),
    (5, 'account is locked'),
    (5, 'changed \w+( e-?mail)? address'),
    (5, 'deactivated mailbox'),
    (5, 'disabled or discontinued'),
    (5, 'inactive user'),
    (5, 'is inactive on this domain'),
    (5, 'mail receiving disabled'),
    (5, 'mail( ?)address is administrative?ly disabled'),
    (5, 'mailbox (temporarily disabled|currently suspended)'),
    (5, 'no longer (accepting mail|on server|in use|with|employed|on staff|works for|using this account)'),
    (5, 'not accepting mail'),
    (5, 'please use my new e-?mail address'),
    (5, 'this address no longer accepts mail'),
    (5, 'user account suspended');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Invalid', '{ts escape="sql"}Email address is not valid{/ts}', 1);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (6, '(user|recipient( name)?) is not recognized'),
    (6, '554 delivery error'),
    (6, 'address does not exist'),
    (6, 'address(es)? could not be found'),
    (6, 'addressee unknown'),
    (6, 'bad destination'),
    (6, 'badly formatted address'),
    (6, 'can\'t open mailbox for'),
    (6, 'cannot deliver'),
    (6, 'delivery to the following recipients failed'),
    (6, 'destination addresses were unknown'),
    (6, 'did not reach the following recipient'),
    (6, 'does not exist'),
    (6, 'does not like recipient'),
    (6, 'does not specify a valid notes mail file'),
    (6, 'illegal alias'),
    (6, 'invalid (mailbox|(e-?mail )?address|recipient|final delivery)'),
    (6, 'invalid( or unknown)?( virtual)? user'),
    (6, 'mail delivery to this user is not allowed'),
    (6, 'mailbox (not found|unavailable|name not allowed)'),
    (6, 'message could not be forwarded'),
    (6, 'missing or malformed local(-| )part'),
    (6, 'no e-?mail address registered'),
    (6, 'no such (mail drop|mailbox( \w+)?|(e-?mail )?address|recipient|(local )?user)( here)?'),
    (6, 'no mailbox here by that name'),
    (6, 'not (listed in|found in directory|known at this site|our customer)'),
    (6, 'not a valid( (user|mailbox))?'),
    (6, 'not present in directory entry'),
    (6, 'recipient (does not exist|(is )?unknown)'),
    (6, 'this user doesn\'t have a yahoo.com address'),
    (6, 'unavailable to take delivery of the message'),
    (6, 'unavailable mailbox'),
    (6, 'unknown (local( |-)part|recipient)'),
    (6, 'unknown( or illegal)? user( account)?'),
    (6, 'unrecognized recipient'),
    (6, 'unregistered address'),
    (6, 'user (unknown|does not exist)'),
    (6, 'user doesn\'t have an? \w+ account'),
    (6, 'user(\'s e-?mail name is)? not found');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Loop', '{ts escape="sql"}Mail routing error{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (7, '(mail|routing) loop'),
    (7, 'excessive recursion'),
    (7, 'loop detected'),
    (7, 'maximum hop count exceeded'),
    (7, 'message was forwarded more than the maximum allowed times'),
    (7, 'too many hops');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Quota', '{ts escape="sql"}User inbox is full{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (8, '(disk|over the allowed|exceed(ed|s)?|storage) quota'),
    (8, '522_mailbox_full'),
    (8, 'exceeds allowed message count'),
    (8, 'file too large'),
    (8, 'full mailbox'),
    (8, 'mailbox ((for user \w+ )?is )?full'),
    (8, 'mailbox has exceeded the limit'),
    (8, 'mailbox( exceeds allowed)? size'),
    (8, 'no space left for this user'),
    (8, 'over\s?quota'),
    (8, 'quota (for the mailbox )?has been exceeded'),
    (8, 'quota (usage|violation|exceeded)'),
    (8, 'recipient storage full'),
    (8, 'not able to receive more mail');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Relay', '{ts escape="sql"}Unable to reach destination mail server{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (9, 'cannot find your hostname'),
    (9, 'ip name lookup'),
    (9, 'not configured to relay mail'),
    (9, 'relay (not permitted|access denied)'),
    (9, 'relayed mail to .+? not allowed'),
    (9, 'sender ip must resolve'),
    (9, 'unable to relay');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Spam', '{ts escape="sql"}Message caught by a content filter{/ts}', 1);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (10, '(bulk( e-?mail)|content|attachment blocking|virus|mail system) filters?'),
    (10, '(hostile|questionable|unacceptable) content'),
    (10, 'address .+? has not been verified'),
    (10, 'anti-?spam (polic\w+|software)'),
    (10, 'anti-?virus gateway has detected'),
    (10, 'blacklisted'),
    (10, 'blocked message'),
    (10, 'content control'),
    (10, 'delivery not authorized'),
    (10, 'does not conform to our e-?mail policy'),
    (10, 'excessive spam content'),
    (10, 'message looks suspicious'),
    (10, 'open relay'),
    (10, 'sender was rejected'),
    (10, 'spam(check| reduction software| filters?)'),
    (10, 'blocked by a user configured filter'),
    (10, 'detected as spam');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Syntax', '{ts escape="sql"}Error in SMTP transaction{/ts}', 3);

INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (11, 'nonstandard smtp line terminator'),
    (11, 'syntax error in from address'),
    (11, 'unknown smtp code');

-- add sample profile

INSERT INTO civicrm_uf_group
    (id, name,                 group_type,           title,                                      is_cms_user, is_reserved, help_post) VALUES
    (1,  NULL,                 'Individual,Contact',  '{ts escape="sql"}Name and Address{/ts}',   0,           0,           NULL),
    (2,  NULL,                 'Individual,Contact',  '{ts escape="sql"}Supporter Profile{/ts}',  2,           0,           '<p><strong>{ts escape="sql"}The information you provide will NOT be shared with any third party organisations.{/ts}</strong></p><p>{ts escape="sql"}Thank you for getting involved in our campaign!{/ts}</p>'),
    (3,  'participant_status', 'Participant',         '{ts escape="sql"}Participant Status{/ts}', 0,           1,           NULL),
    (4,  'new_individual',     'Individual,Contact',  '{ts escape="sql"}New Individual{/ts}'    , 0,           1,           NULL),
    (5,  'new_organization',   'Organization,Contact','{ts escape="sql"}New Organization{/ts}'  , 0,           1,           NULL),
    (6,  'new_household',      'Household,Contact',   '{ts escape="sql"}New Household{/ts}'     , 0,           1,           NULL);

INSERT INTO civicrm_uf_join
   (is_active,module,entity_table,entity_id,weight,uf_group_id)
VALUES
   (1, 'User Registration',NULL, NULL,1,1),
   (1, 'User Account', NULL, NULL, 1, 1),
   (1, 'Profile', NULL, NULL, 1, 1),
   (1, 'Profile', NULL, NULL, 2, 2),
   (1, 'Profile', NULL, NULL, 3, 4),
   (1, 'Profile', NULL, NULL, 4, 5),
   (1, 'Profile', NULL, NULL, 5, 6)
   ;
   
INSERT INTO civicrm_uf_field
       (id, uf_group_id, field_name,              is_required, is_reserved, weight, visibility,                  in_selector, is_searchable, location_type_id, label,                                         field_type,    help_post) VALUES
       (1,  1,           'first_name',            1,           0,           1,      'Public Pages and Listings', 0,           1,             NULL,             '{ts escape="sql"}First Name{/ts}',            'Individual',  NULL),
       (2,  1,           'last_name',             1,           0,           2,      'Public Pages and Listings', 0,           1,             NULL,             '{ts escape="sql"}Last Name{/ts}',             'Individual',  '{ts escape="sql"}First and last name will be shared with other visitors to the site.{/ts}'),
       (3,  1,           'street_address',        0,           0,           3,      'User and User Admin Only',  0,           0,             1,                '{ts escape="sql"}Street Address (Home){/ts}', 'Contact',     NULL),
       (4,  1,           'city',                  0,           0,           4,      'User and User Admin Only',  0,           0,             1,                '{ts escape="sql"}City (Home){/ts}',           'Contact',     NULL),
       (5,  1,           'postal_code',           0,           0,           5,      'User and User Admin Only',  0,           0,             1,                '{ts escape="sql"}Postal Code (Home){/ts}',    'Contact',     NULL),
       (6,  1,           'country',               0,           0,           6,      'Public Pages and Listings', 0,           1,             1,                '{ts escape="sql"}Country (Home){/ts}',        'Contact',     '{ts escape="sql"}Your state/province and country of residence will be shared with others so folks can find others in their community.{/ts}'),
       (7,  1,           'state_province',        0,           0,           7,      'Public Pages and Listings', 1,           1,             1,                '{ts escape="sql"}State (Home){/ts}',          'Contact',     NULL),
       (8,  2,           'first_name',            1,           0,           1,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}First Name{/ts}',            'Individual',  NULL),
       (9,  2,           'last_name',             1,           0,           2,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Last Name{/ts}',             'Individual',  NULL),
       (10, 2,           'email',                 1,           0,           3,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Email Address{/ts}',         'Contact',     NULL),
       (11, 3,           'participant_status_id', 1,           1,           1,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Participant Status{/ts}',    'Participant', NULL),
       (12, 4,           'first_name',            1,           0,           1,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}First Name{/ts}',            'Individual',  NULL),
       (13, 4,           'last_name',             1,           0,           2,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Last Name{/ts}',             'Individual',  NULL),
       (14, 4,           'email',                 0,           0,           3,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Email Address{/ts}',         'Contact',     NULL),
       (15, 5,           'organization_name',     1,           0,           2,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Organization Name{/ts}',     'Organization',NULL),
       (16, 5,           'email',                 0,           0,           3,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Email Address{/ts}',         'Contact',     NULL),
       (17, 6,           'household_name',        1,           0,           2,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Household Name{/ts}',        'Household',   NULL),
       (18, 6,           'email',                 0,           0,           3,      'User and User Admin Only',  0,           0,             NULL,             '{ts escape="sql"}Email Address{/ts}',         'Contact',     NULL);

INSERT INTO civicrm_participant_status_type
  (id, name,                    label,                                         class,      is_reserved, is_active, is_counted, weight, visibility_id) VALUES
  (1,  'Registered',            '{ts escape="sql"}Registered{/ts}',            'Positive', 1,           1,         1,          1,      1            ),
  (2,  'Attended',              '{ts escape="sql"}Attended{/ts}',              'Positive', 0,           1,         1,          2,      2            ),
  (3,  'No-show',               '{ts escape="sql"}No-show{/ts}',               'Negative', 0,           1,         0,          3,      2            ),
  (4,  'Cancelled',             '{ts escape="sql"}Cancelled{/ts}',             'Negative', 1,           1,         0,          4,      2            ),
  (5,  'Pending from pay later','{ts escape="sql"}Pending from pay later{/ts}','Pending',  1,           1,         1,          5,      2            ),
  (6,  'On waitlist',           '{ts escape="sql"}On waitlist{/ts}',           'Waiting',  1,           1,         0,          6,      2            ),
  (7,  'Awaiting approval',     '{ts escape="sql"}Awaiting approval{/ts}',     'Waiting',  1,           1,         1,          7,      2            ),
  (8,  'Pending from waitlist', '{ts escape="sql"}Pending from waitlist{/ts}', 'Pending',  1,           1,         1,          8,      2            ),
  (9,  'Pending from approval', '{ts escape="sql"}Pending from approval{/ts}', 'Pending',  1,           1,         1,          9,      2            ),
  (10, 'Rejected',              '{ts escape="sql"}Rejected{/ts}',              'Negative', 1,           1,         0,          10,     2            ),
  (11, 'Expired',               '{ts escape="sql"}Expired{/ts}',               'Negative', 1,           1,         0,          11,     2            );

INSERT INTO civicrm_navigation
( id, domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight ) VALUES

( 1, @domainID,  NULL, '{ts escape="sql"}Search...{/ts}', 'Search...', NULL, '', NULL, '1', NULL, 1 ), 
( 2, @domainID,  'civicrm/contact/search&reset=1', '{ts escape="sql"}Find Contacts{/ts}', 'Find Contacts', NULL, '', '1', '1', NULL, 1 ), 
( 3, @domainID,  'civicrm/contact/search/advanced&reset=1', '{ts escape="sql"}Find Contacts - Advanced Search{/ts}', 'Find Contacts - Advanced Search', NULL, '', '1', '1', NULL, 2 ), 
( 4, @domainID,  'civicrm/contact/search/custom&csid=15&reset=1', '{ts escape="sql"}Full-text Search{/ts}', 'Full-text Search', NULL, '', '1', '1', NULL, 3 ), 
( 5, @domainID,  'civicrm/contact/search/builder&reset=1', '{ts escape="sql"}Search Builder{/ts}', 'Search Builder', NULL, '', '1', '1', '1', 4 ), 
( 6, @domainID,  'civicrm/case/search&reset=1', '{ts escape="sql"}Find Cases{/ts}', 'Find Cases', 'access CiviCase', '', '1', '1', NULL, 5 ), 
( 7, @domainID,  'civicrm/contribute/search&reset=1', '{ts escape="sql"}Find Contributions{/ts}', 'Find Contributions', 'access CiviContribute', '', '1', '1', NULL, 6 ), 
( 8, @domainID,  'civicrm/mailing&reset=1', '{ts escape="sql"}Find Mailings{/ts}', 'Find Mailings', 'access CiviMail', '', '1', '1', NULL, 7 ), 
( 9, @domainID,  'civicrm/member/search&reset=1', '{ts escape="sql"}Find Members{/ts}', 'Find Members', 'access CiviMember', '', '1', '1', NULL, 8 ), 
( 10, @domainID, 'civicrm/event/search&reset=1', '{ts escape="sql"}Find Participants{/ts}', 'Find Participants',  'access CiviEvent', '', '1', '1', NULL, 9 ), 
( 11, @domainID, 'civicrm/pledge/search&reset=1', '{ts escape="sql"}Find Pledges{/ts}', 'Find Pledges', 'access CiviPledge', '', '1', '1', 1, 10 ), 

( 12, @domainID, 'civicrm/contact/search/custom/list&reset=1', '{ts escape="sql"}Custom Searches...{/ts}', 'Custom Searches...', NULL, '', '1', '1', NULL, 11 ), 
( 13, @domainID, 'civicrm/contact/search/custom&reset=1&csid=8', '{ts escape="sql"}Activity Search{/ts}', 'Activity Search', NULL, '', '12', '1', NULL, 1 ), 
( 14, @domainID, 'civicrm/contact/search/custom&reset=1&csid=11', '{ts escape="sql"}Contacts by Date Added{/ts}', 'Contacts by Date Added', NULL, '', '12', '1', NULL, 2 ), 
( 15, @domainID, 'civicrm/contact/search/custom&reset=1&csid=2', '{ts escape="sql"}Contributors by Aggregate Totals{/ts}', 'Contributors by Aggregate Totals', NULL, '', '12', '1', NULL, 3 ), 
( 16, @domainID, 'civicrm/contact/search/custom&reset=1&csid=6', '{ts escape="sql"}Proximity Search{/ts}', 'Proximity Search', NULL, '', '12', '1', NULL, 4 ), 

( 17, @domainID, NULL, '{ts escape="sql"}Contacts{/ts}', 'Contacts', NULL, '', NULL, '1', NULL, 3 ), 
( 18, @domainID, 'civicrm/contact/add&reset=1&ct=Individual', '{ts escape="sql"}New Individual{/ts}', 'New Individual', NULL, '', '17', '1', NULL, 1 ), 
( 19, @domainID, 'civicrm/contact/add&reset=1&ct=Household', '{ts escape="sql"}New Household{/ts}', 'New Household', NULL, '', '17', '1', NULL, 2 ), 
( 20, @domainID, 'civicrm/contact/add&reset=1&ct=Organization', '{ts escape="sql"}New Organization{/ts}', 'New Organization', NULL, '', '17', '1', 1, 3 ), 
( 21, @domainID, 'civicrm/activity&reset=1&action=add&context=standalone', '{ts escape="sql"}New Activity{/ts}', 'New Activity', NULL, '', '17', '1', NULL, 4 ), 
( 22, @domainID, 'civicrm/contact/view/activity&atype=3&action=add&reset=1&context=standalone', '{ts escape="sql"}New Email{/ts}', 'New Email', NULL, '', '17', '1', '1', 5 ), 
( 23, @domainID, 'civicrm/import/contact&reset=1', '{ts escape="sql"}Import Contacts{/ts}', 'Import Contacts', NULL, '', '17', '1', NULL, 6 ), 
( 24, @domainID, 'civicrm/import/activity&reset=1', '{ts escape="sql"}Import Activities{/ts}', 'Import Activities', NULL, '', '17', '1', '1', 7 ), 
( 25, @domainID, 'civicrm/group/add&reset=1', '{ts escape="sql"}New Group{/ts}', 'New Group', NULL, '', '17', '1', NULL, 8 ), 
( 26, @domainID, 'civicrm/group&reset=1', '{ts escape="sql"}Manage Groups{/ts}', 'Manage Groups', NULL, '', '17', '1', '1', 9 ), 
( 27, @domainID,'civicrm/admin/tag&reset=1&action=add', '{ts escape="sql"}New Tag{/ts}', 'New Tag', NULL, '', '17', '1', NULL, 10 ), 
( 28, @domainID,'civicrm/admin/tag&reset=1', '{ts escape="sql"}Manage Tags (Categories){/ts}', 'Manage Tags (Categories)', NULL, '', '17', '1', NULL, 11 ), 

( 29, @domainID,NULL, '{ts escape="sql"}Contributions{/ts}', 'Contributions', 'access CiviContribute', '', NULL, '1', NULL, 4 ), 
( 30, @domainID,'civicrm/contribute&reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '29', '1', NULL, 1 ), 
( 31, @domainID,'civicrm/contact/view/contribution&reset=1&action=add&context=standalone', '{ts escape="sql"}New Contribution{/ts}', 'New Contribution', NULL, '', '29', '1', NULL, 2 ), 
( 32, @domainID, 'civicrm/contribute/search&reset=1', '{ts escape="sql"}Find Contributions{/ts}', 'Find Contributions', NULL, '', '29', '1', NULL, 3 ), 
( 33, @domainID, 'civicrm/contribute/import&reset=1', '{ts escape="sql"}Import Contributions{/ts}', 'Import Contributions', NULL, '', '29', '1', '1', 4 ),
( 34, @domainID,NULL, '{ts escape="sql"}Pledges{/ts}', 'Pledges', 'access CiviPledge', '', 29, '1', 1, 5 ), 
( 35, @domainID,'civicrm/pledge&reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '34', '1', NULL, 1 ), 
( 36, @domainID,'civicrm/pledge/search&reset=1', '{ts escape="sql"}Find Pledges{/ts}', 'Find Pledges', NULL, '', '34', '1', NULL, 2 ), 
( 37, @domainID, 'civicrm/admin/contribute&reset=1&action=add', '{ts escape="sql"}New Contribution Page{/ts}', 'New Contribution Page', NULL, '', '29', '1', NULL, 6 ), 
( 38, @domainID, 'civicrm/admin/contribute&reset=1', '{ts escape="sql"}Manage Contribution Pages{/ts}', 'Manage Contribution Pages', NULL, '', '29', '1', '1', 7 ), 
( 39, @domainID, 'civicrm/admin/pcp&reset=1', '{ts escape="sql"}Personal Campaign Pages{/ts}', 'Personal Campaign Pages', NULL, '', '29', '1', NULL, 8 ), 
( 40, @domainID, 'civicrm/admin/contribute/managePremiums&reset=1', '{ts escape="sql"}Premiums (Thank-you Gifts){/ts}', 'Premiums', NULL, '', '29', '1', NULL, 9	 ), 

( 41, @domainID, NULL, '{ts escape="sql"}Events{/ts}', 'Events', 'access CiviEvent', '', NULL, '1', NULL, 5 ), 
( 42, @domainID, 'civicrm/event&reset=1', '{ts escape="sql"}Dashboard{/ts}', 'CiviEvent Dashboard', NULL, '', '41', '1', NULL, 1 ), 
( 43, @domainID, 'civicrm/contact/view/participant&reset=1&action=add&context=standalone', '{ts escape="sql"}Register Event Participant{/ts}', 'Register Event Participant', NULL, '', '41', '1', NULL, 2 ), 
( 44, @domainID, 'civicrm/event/search&reset=1', '{ts escape="sql"}Find Participants{/ts}', 'Find Participants', NULL, '', '41', '1', NULL, 3 ), 
( 45, @domainID, 'civicrm/event/import&reset=1', '{ts escape="sql"}Import Participants{/ts}', 'Import Participants', NULL, '', '41', '1', '1', 4 ), 
( 46, @domainID, 'civicrm/event/add&reset=1&action=add', '{ts escape="sql"}New Event{/ts}', 'New Event', NULL, '', '41', '1', NULL, 5 ), 
( 47, @domainID, 'civicrm/event/manage&reset=1', '{ts escape="sql"}Manage Events{/ts}', 'Manage Events', NULL, '', '41', '1', 1, 6 ), 
( 48, @domainID, 'civicrm/admin/eventTemplate&reset=1', '{ts escape="sql"}Event Templates{/ts}', 'Event Templates', 'access CiviEvent,administer CiviCRM', '', '41', '1', 1, 7 ), 
( 49, @domainID, 'civicrm/admin/price&reset=1&action=add', '{ts escape="sql"}New Price Set{/ts}', 'New Price Set', NULL, '', '41', '1', NULL, 8 ), 
( 50, @domainID, 'civicrm/event/price&reset=1', '{ts escape="sql"}Manage Price Sets{/ts}', 'Manage Price Sets', NULL, '', '41', '1', NULL, 9 ),

( 51, @domainID, NULL, '{ts escape="sql"}Mailings{/ts}', 'Mailings', 'access CiviMail', '', NULL, '1', NULL, 6 ), 
( 52, @domainID, 'civicrm/mailing/send&reset=1', '{ts escape="sql"}New Mailing{/ts}', 'New Mailing', NULL, '', '51', '1', NULL, 1 ), 
( 53, @domainID, 'civicrm/mailing/browse/unscheduled&reset=1&scheduled=false', '{ts escape="sql"}Draft and Unscheduled Mailings{/ts}', 'Draft and Unscheduled Mailings', NULL, '', '51', '1', NULL, 2 ), 
( 54, @domainID, 'civicrm/mailing/browse/scheduled&reset=1&scheduled=true', '{ts escape="sql"}Scheduled and Sent Mailings{/ts}', 'Scheduled and Sent Mailings', NULL, '', '51', '1', NULL, 3 ), 
( 55, @domainID, 'civicrm/mailing/browse/archived&reset=1', '{ts escape="sql"}Archived Mailings{/ts}', 'Archived Mailings', NULL, '', '51', '1', 1, 4 ), 
( 56, @domainID, 'civicrm/admin/component&reset=1', '{ts escape="sql"}Headers, Footers, and Automated Messages{/ts}', 'Headers, Footers, and Automated Messages', NULL, '', '51', '1', NULL, 5 ), 
( 57, @domainID, 'civicrm/admin/messageTemplates&reset=1', '{ts escape="sql"}Message Templates{/ts}', 'Message Templates', NULL, '', '51', '1', NULL, 6 ), 
( 58, @domainID, 'civicrm/admin/options/from_email&group=from_email_address&reset=1', '{ts escape="sql"}From Email Addresses{/ts}', 'From Email Addresses', NULL, '', '51', '1', NULL, 7 ), 

( 59, @domainID, NULL, '{ts escape="sql"}Memberships{/ts}', 'Memberships', 'access CiviMember', '', NULL, '1', NULL, 7 ), 
( 60, @domainID, 'civicrm/member&reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '59', '1', NULL, 1 ), 
( 61, @domainID, 'civicrm/contact/view/membership&reset=1&action=add&context=standalone', '{ts escape="sql"}New Membership{/ts}', 'New Membership', NULL, '', '59', '1', NULL, 2 ), 
( 62, @domainID, 'civicrm/member/search&reset=1', '{ts escape="sql"}Find Members{/ts}', 'Find Members', NULL, '', '59', '1', NULL, 3 ), 
( 63, @domainID, 'civicrm/member/import&reset=1', '{ts escape="sql"}Import Members{/ts}', 'Import Members', NULL, '', '59', '1', NULL, 4 ), 

( 64, @domainID, NULL, '{ts escape="sql"}Other{/ts}', 'Other', 'access CiviGrant,access CiviCase', 'OR', NULL, '1', NULL, 8 ), 
( 65, @domainID, NULL, '{ts escape="sql"}Cases{/ts}', 'Cases', 'access CiviCase', '', '64', '1', NULL, 1 ), 
( 66, @domainID, 'civicrm/case&reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '65', '1', NULL, 1 ), 
( 67, @domainID, 'civicrm/contact/view/case&reset=1&action=add&atype=13&context=standalone', '{ts escape="sql"}New Case{/ts}', 'New Case', NULL, '', '65', '1', NULL, 2 ), 
( 68, @domainID, 'civicrm/case/search&reset=1', '{ts escape="sql"}Find Cases{/ts}', 'Find Cases', NULL, '', '65', '1', 1, 3 ), 

( 69, @domainID, NULL, '{ts escape="sql"}Grants{/ts}', 'Grants', 'access CiviGrant', '', '64', '1', NULL, 2 ),
( 70, @domainID, 'civicrm/grant&reset=1', '{ts escape="sql"}Dashboard{/ts}', 'Dashboard', NULL, '', '69', '1', NULL, 1 ), 
( 71, @domainID, 'civicrm/contact/view/grant&reset=1&action=add&context=standalone', '{ts escape="sql"}New Grant{/ts}', 'New Grant', NULL, '', '69', '1', NULL, 2 ), 
( 72, @domainID, 'civicrm/grant/search&reset=1', '{ts escape="sql"}Find Grants{/ts}', 'Find Grants', NULL, '', '69', '1', 1, 3 ), 

( 73, @domainID, NULL, '{ts escape="sql"}Administer{/ts}', 'Administer', 'administer CiviCRM', '', NULL, '1', NULL, 9 ), 
( 74, @domainID, 'civicrm/admin&reset=1', '{ts escape="sql"}Administration Console{/ts}', 'Administration Console', NULL, '', '73', '1', NULL, 1 ), 

( 75, @domainID, NULL, '{ts escape="sql"}Customize{/ts}', 'Customize', NULL, '', '73', '1', NULL, 2 ), 
( 76, @domainID, 'civicrm/admin/custom/group&reset=1', '{ts escape="sql"}Custom Data{/ts}', 'Custom Data', NULL, '', '75', '1', NULL, 1 ), 
( 77, @domainID, 'civicrm/admin/uf/group&reset=1', '{ts escape="sql"}CiviCRM Profile{/ts}', 'CiviCRM Profile', NULL, '', '75', '1', NULL, 2 ), 
( 78, @domainID, 'civicrm/admin/menu&reset=1', '{ts escape="sql"}Navigation Menu{/ts}', 'Navigation Menu', NULL, '', '75', '1', NULL, 3 ), 
( 79, @domainID, 'civicrm/admin/options/custom_search&reset=1&group=custom_search', '{ts escape="sql"}Manage Custom Searches{/ts}', 'Manage Custom Searches', NULL, '', '75', '1', NULL, 4 ), 

( 80, @domainID, NULL, '{ts escape="sql"}Configure{/ts}', 'Configure', NULL, '', '73', '1', NULL, 3 ), 
( 81, @domainID, 'civicrm/admin/configtask&reset=1', '{ts escape="sql"}Configuration Checklist{/ts}', 'Configuration Checklist', NULL, '', '80', '1', NULL, 1 ), 

( 82, @domainID, 'civicrm/admin/setting&reset=1', '{ts escape="sql"}Global Settings{/ts}', 'Global Settings', NULL, '', '80', '1', NULL, 2 ), 
( 83, @domainID, 'civicrm/admin/setting/component&reset=1', '{ts escape="sql"}Enable CiviCRM Components{/ts}', 'Enable Components', NULL, '', '82', '1', NULL, 1 ), 
( 84, @domainID, 'civicrm/admin/setting/preferences/display&reset=1', '{ts escape="sql"}Site Preferences (screen and form configuration){/ts}', 'Site Preferences', NULL, '', '82', '1', NULL, 2 ), 
( 85, @domainID, 'civicrm/admin/setting/path&reset=1', '{ts escape="sql"}Directories{/ts}', 'Directories', NULL, '', '82', '1', NULL, 3 ), 
( 86, @domainID, 'civicrm/admin/setting/url&reset=1', '{ts escape="sql"}Resource URLs{/ts}', 'Resource URLs', NULL, '', '82', '1', NULL, 4 ), 
( 87, @domainID, 'civicrm/admin/setting/smtp&reset=1', '{ts escape="sql"}Outbound Email (SMTP/Sendmail){/ts}', 'Outbound Email', NULL, '', '82', '1', NULL, 5 ), 
( 88, @domainID, 'civicrm/admin/setting/mapping&reset=1', '{ts escape="sql"}Mapping and Geocoding{/ts}', 'Mapping and Geocoding', NULL, '', '82', '1', NULL, 6 ), 
( 89, @domainID, 'civicrm/admin/paymentProcessor&reset=1', '{ts escape="sql"}Payment Processors{/ts}', 'Payment Processors', NULL, '', '82', '1', NULL, 7 ), 
( 90, @domainID, 'civicrm/admin/setting/localization&reset=1', '{ts escape="sql"}Localization{/ts}', 'Localization', NULL, '', '82', '1', NULL, 8 ), 
( 91, @domainID, 'civicrm/admin/setting/preferences/address&reset=1', '{ts escape="sql"}Address Settings{/ts}', 'Address Settings', NULL, '', '82', '1', NULL, 9 ), 
( 92, @domainID, 'civicrm/admin/setting/date&reset=1', '{ts escape="sql"}Date Formats{/ts}', 'Date Formats', NULL, '', '82', '1', NULL, 10 ), 
( 93, @domainID, 'civicrm/admin/setting/uf&reset=1', '{ts escape="sql"}CMS Integration{/ts}', 'CMS Integration', NULL, '', '82', '1', NULL, 11 ), 
( 94, @domainID, 'civicrm/admin/setting/misc&reset=1', '{ts escape="sql"}Miscellaneous (version check, search, reCAPTCHA...){/ts}', 'Miscellaneous', NULL, '', '82', '1', NULL, 12 ), 
( 95, @domainID, 'civicrm/admin/options/safe_file_extension&group=safe_file_extension&reset=1', '{ts escape="sql"}Safe File Extensions{/ts}', 'Safe File Extensions', NULL, '', '82', '1', NULL, 13 ), 
( 96, @domainID, 'civicrm/admin/setting/debug&reset=1', '{ts escape="sql"}Debugging{/ts}', 'Debugging', NULL, '', '82', '1', NULL, 14 ), 

( 97, @domainID, 'civicrm/admin/mapping&reset=1', '{ts escape="sql"}Import/Export Mappings{/ts}', 'Import/Export Mappings', NULL, '', '80', '1', NULL, 3 ), 
( 98, @domainID, 'civicrm/admin/messageTemplates&reset=1', '{ts escape="sql"}Message Templates{/ts}', 'Message Templates', NULL, '', '80', '1', NULL, 4 ), 
( 99, @domainID, 'civicrm/admin/domain&action=update&reset=1', '{ts escape="sql"}Domain Information{/ts}', 'Domain Information', NULL, '', '80', '1', NULL, 5 ), 
( 100, @domainID, 'civicrm/admin/options/from_email_address&group=from_email_address&reset=1', '{ts escape="sql"}FROM Email Addresses{/ts}', 'FROM Email Addresses', NULL, '', '80', '1', NULL, 6 ), 
( 101, @domainID, 'civicrm/admin/setting/updateConfigBackend&reset=1', '{ts escape="sql"}Update Directory Path and URL{/ts}', 'Update Directory Path and URL', NULL, '', '80', '1', NULL, 7 ), 

( 102, @domainID, NULL, '{ts escape="sql"}Manage{/ts}', 'Manage', NULL, '', '73', '1', NULL, 4 ), 
( 103, @domainID, 'civicrm/admin/deduperules&reset=1', '{ts escape="sql"}Find and Merge Duplicate Contacts{/ts}', 'Find and Merge Duplicate Contacts', '', '', '102', '1', NULL, 1 ), 
( 104, @domainID, 'civicrm/admin/access&reset=1', '{ts escape="sql"}Access Control{/ts}', 'Access Control', NULL, '', '102', '1', NULL, 2 ), 
( 105, @domainID, 'civicrm/admin/synchUser&reset=1', '{ts escape="sql"}Synchronize Users to Contacts{/ts}', 'Synchronize Users to Contacts', NULL, '', '102', '1', NULL, 3 ), 

( 106, @domainID, NULL, '{ts escape="sql"}Option Lists{/ts}', 'Option Lists', NULL, '', '73', '1', NULL, 5 ), 
( 107, @domainID, 'civicrm/admin/options/activity_type&reset=1&group=activity_type', '{ts escape="sql"}Activity Types{/ts}', 'Activity Types', NULL, '', '106', '1', NULL, 1 ), 
( 108, @domainID, 'civicrm/admin/reltype&reset=1', '{ts escape="sql"}Relationship Types{/ts}', 'Relationship Types', NULL, '', '106', '1', NULL, 2 ), 
( 109, @domainID, 'civicrm/admin/tag&reset=1', '{ts escape="sql"}Tags (Categories){/ts}', 'Tags (Categories)', NULL, '', '106', '1', 1, 3 ), 
( 110, @domainID, 'civicrm/admin/options/gender&reset=1&group=gender', '{ts escape="sql"}Gender Options{/ts}', 'Gender Options', NULL, '', '106', '1', NULL, 4 ), 
( 111, @domainID, 'civicrm/admin/options/individual_prefix&group=individual_prefix&reset=1', '{ts escape="sql"}Individual Prefixes (Ms, Mr...){/ts}', 'Individual Prefixes (Ms, Mr...)', NULL, '', '106', '1', NULL, 5 ), 
( 112, @domainID, 'civicrm/admin/options/individual_suffix&group=individual_suffix&reset=1', '{ts escape="sql"}Individual Suffixes (Jr, Sr...){/ts}', 'Individual Suffixes (Jr, Sr...)', NULL, '', '106', '1', 1, 6 ), 
( 113, @domainID, 'civicrm/admin/options/addressee&group=addressee&reset=1', '{ts escape="sql"}Addressee Formats{/ts}', 'Addressee Formats', NULL, '', '106', '1', NULL, 7 ), 
( 114, @domainID, 'civicrm/admin/options/email_greeting&group=email_greeting&reset=1', '{ts escape="sql"}Email Greetings{/ts}', 'Email Greetings', NULL, '', '106', '1', NULL, 8 ), 
( 115, @domainID, 'civicrm/admin/options/postal_greeting&group=postal_greeting&reset=1', '{ts escape="sql"}Postal Greetings{/ts}', 'Postal Greetings', NULL, '', '106', '1', 1, 9 ), 
( 116, @domainID, 'civicrm/admin/options/instant_messenger_service&group=instant_messenger_service&reset=1', '{ts escape="sql"}Instant Messenger Services{/ts}', 'Instant Messenger Services', NULL, '', '106', '1', NULL, 10 ), 
( 117, @domainID, 'civicrm/admin/locationType&reset=1', '{ts escape="sql"}Location Types (Home, Work...){/ts}', 'Location Types (Home, Work...)', NULL, '', '106', '1', NULL, 11 ), 
( 118, @domainID, 'civicrm/admin/options/mobile_provider&group=mobile_provider&reset=1', '{ts escape="sql"}Mobile Phone Providers{/ts}', 'Mobile Phone Providers', NULL, '', '106', '1', NULL, 12 ), 
( 119, @domainID, 'civicrm/admin/options/phone_type&group=phone_type&reset=1', '{ts escape="sql"}Phone Types{/ts}', 'Phone Types', NULL, '', '106', '1', NULL, 13 ), 
( 120, @domainID, 'civicrm/admin/options/preferred_communication_method&group=preferred_communication_method&reset=1', '{ts escape="sql"}Preferred Communication Methods{/ts}', 'Preferred Communication Methods', NULL, '', '106', '1', NULL, 14 ), 

( 121, @domainID, NULL, '{ts escape="sql"}CiviCase{/ts}', 'CiviCase', 'access CiviCase,administer CiviCRM', 'AND', '73', '1', NULL, 6 ), 
( 122, @domainID, 'civicrm/admin/options/case_type&group=case_type&reset=1', '{ts escape="sql"}Case Types{/ts}', 'Case Types', 'access CiviCase,administer CiviCRM', '', '121', '1', NULL, 1 ), 
( 123, @domainID, 'civicrm/admin/options/redaction_rule&group=redaction_rule&reset=1', '{ts escape="sql"}Redaction Rules{/ts}', 'Redaction Rules', 'access CiviCase,administer CiviCRM', '', '121', '1', NULL, 2 ), 

( 124, @domainID, NULL, '{ts escape="sql"}CiviContribute{/ts}', 'CiviContribute', 'access CiviContribute,administer CiviCRM', 'AND', '73', '1', NULL, 7 ), 
( 125, @domainID, 'civicrm/admin/contribute&reset=1&action=add', '{ts escape="sql"}New Contribution Page{/ts}', 'New Contribution Page', NULL, '', '124', '1', NULL, 6 ), 
( 126, @domainID, 'civicrm/admin/contribute&reset=1', '{ts escape="sql"}Manage Contribution Pages{/ts}', 'Manage Contribution Pages', NULL, '', '124', '1', '1', 7 ), 
( 127, @domainID, 'civicrm/admin/pcp&reset=1', '{ts escape="sql"}Personal Campaign Pages{/ts}', 'Personal Campaign Pages', NULL, '', '124', '1', NULL, 8 ), 
( 128, @domainID, 'civicrm/admin/contribute/managePremiums&reset=1', '{ts escape="sql"}Premiums (Thank-you Gifts){/ts}', 'Premiums', NULL, '', '124', '1', 1, 9	 ), 
( 129, @domainID, 'civicrm/admin/contribute/contributionType&reset=1', '{ts escape="sql"}Contribution Types{/ts}', 'Contribution Types', NULL, '', '124', '1', NULL, 10	 ), 
( 130, @domainID, 'civicrm/admin/options/payment_instrument&group=payment_instrument&reset=1', '{ts escape="sql"}Payment Instruments{/ts}', 'Payment Instruments', NULL, '', '124', '1', NULL, 11	 ), 
( 131, @domainID, 'civicrm/admin/options/accept_creditcard&group=accept_creditcard&reset=1', '{ts escape="sql"}Accepted Credit Cards{/ts}', 'Accepted Credit Cards', NULL, '', '124', '1', NULL, 12	 ), 

( 132, @domainID, NULL, '{ts escape="sql"}CiviEvent{/ts}', 'CiviEvent', 'access CiviEvent,administer CiviCRM', 'AND', '73', '1', NULL, 8 ), 
( 133, @domainID, 'civicrm/event/add&reset=1&action=add', '{ts escape="sql"}New Event{/ts}', 'New Event', NULL, '', '132', '1', NULL, 1 ), 
( 134, @domainID, 'civicrm/event/manage&reset=1', '{ts escape="sql"}Manage Events{/ts}', 'Manage Events', NULL, '', '132', '1', 1, 2 ), 
( 135, @domainID, 'civicrm/admin/eventTemplate&reset=1', '{ts escape="sql"}Event Templates{/ts}', 'Event Templates', 'access CiviEvent,administer CiviCRM', '', '132', '1', 1, 3 ), 
( 136, @domainID, 'civicrm/admin/price&reset=1&action=add', '{ts escape="sql"}New Price Set{/ts}', 'New Price Set', NULL, '', '132', '1', NULL, 4 ), 
( 137, @domainID, 'civicrm/event/price&reset=1', '{ts escape="sql"}Manage Price Sets{/ts}', 'Manage Price Sets', NULL, '', '132', '1', 1, 5 ),
( 138, @domainID, 'civicrm/admin/options/participant_listing&group=participant_listing&reset=1', '{ts escape="sql"}Participant Listing Templates{/ts}', 'Participant Listing Templates', NULL, '', '132', '1', NULL, 6 ), 
( 139, @domainID, 'civicrm/admin/options/event_type&group=event_type&reset=1', '{ts escape="sql"}Event Types{/ts}', 'Event Types', NULL, '', '132', '1', NULL, 7 ), 
( 140, @domainID, 'civicrm/admin/participant_status&reset=1', '{ts escape="sql"}Participant Statuses{/ts}', 'Participant Statuses', NULL, '', '132', '1', NULL, 8 ), 
( 141, @domainID, 'civicrm/admin/options/participant_role&group=participant_role&reset=1', '{ts escape="sql"}Participant Roles{/ts}', 'Participant Roles', NULL, '', '132', '1', NULL, 9 ), 

( 142, @domainID, NULL, '{ts escape="sql"}CiviGrant{/ts}', 'CiviGrant', 'access CiviGrant,administer CiviCRM', 'AND', '73', '1', NULL, 9 ), 
( 143, @domainID, 'civicrm/admin/options/grant_type&group=grant_type&reset=1', '{ts escape="sql"}Grant Types{/ts}', 'Grant Types', 'access CiviGrant,administer CiviCRM', '', '142', '1', NULL, 1 ), 

( 144, @domainID, NULL, '{ts escape="sql"}CiviMail{/ts}', 'CiviMail', 'access CiviMail,administer CiviCRM', 'AND', '73', '1', NULL, 10 ), 
( 145, @domainID, 'civicrm/admin/component&reset=1', '{ts escape="sql"}Headers, Footers, and Automated Messages{/ts}', 'Headers, Footers, and Automated Messages', NULL, '', '144', '1', NULL, 1 ), 
( 146, @domainID, 'civicrm/admin/messageTemplates&reset=1', '{ts escape="sql"}Message Templates{/ts}', 'Message Templates', NULL, '', '144', '1', NULL, 2 ), 
( 147, @domainID, 'civicrm/admin/options/from_email&group=from_email_address&reset=1', '{ts escape="sql"}From Email Addresses{/ts}', 'From Email Addresses', NULL, '', '144', '1', NULL, 3 ), 
( 148, @domainID, 'civicrm/admin/mailSettings&reset=1', '{ts escape="sql"}Mail Accounts{/ts}', 'Mail Accounts', NULL, '', '144', '1', NULL, 4 ), 

( 149, @domainID, NULL, '{ts escape="sql"}CiviMember{/ts}', 'CiviMember', 'access CiviMember,administer CiviCRM', 'AND', '73', '1', NULL, 11 ), 
( 150, @domainID, 'civicrm/admin/member/membershipType&reset=1', '{ts escape="sql"}Membership Types{/ts}', 'Membership Types', 'access CiviMember,administer CiviCRM', '', '149', '1', NULL, 1 ), 
( 151, @domainID, 'civicrm/admin/member/membershipStatus&reset=1', '{ts escape="sql"}Membership Status Rules{/ts}', 'Membership Status Rules', 'access CiviMember,administer CiviCRM', '', '149', '1', NULL, 2 ), 

( 152, @domainID, NULL, '{ts escape="sql"}CiviReport{/ts}', 'CiviReport', 'access CiviReport,administer CiviCRM', 'AND', '73', '1', NULL, 12 ), 
( 153, @domainID, 'civicrm/report/list&reset=1', '{ts escape="sql"}Manage Reports{/ts}', 'Manage Reports', NULL, '', '152', '1', NULL, 1 ), 
( 154, @domainID, 'civicrm/admin/report/template/list&reset=1', '{ts escape="sql"}Create Reports from Templates{/ts}', 'Create Reports from Templates', NULL, '', '152', '1', NULL, 2 ), 
( 155, @domainID, 'civicrm/admin/report/options/report_template&reset=1', '{ts escape="sql"}Manage Templates{/ts}', 'Manage Templates', NULL, '', '152', '1', NULL, 3 ), 

( 156, @domainID, NULL, '{ts escape="sql"}Help{/ts}', 'Help', NULL, '', NULL, '1', NULL, 10 ), 
( 157, @domainID, 'http://documentation.civicrm.org', '{ts escape="sql"}Documentation{/ts}', 'Documentation', NULL, 'AND', '156', '1', NULL, 1 ), 
( 158, @domainID, 'http://forum.civicrm.org', '{ts escape="sql"}Community Forums{/ts}', 'Community Forums', NULL, 'AND', '156', '1', NULL, 2 ), 
( 159, @domainID, 'http://civicrm.org/participate', '{ts escape="sql"}Participate{/ts}', 'Participate', NULL, 'AND', '156', '1', NULL, 3 ), 
( 160, @domainID, 'http://civicrm.org/aboutcivicrm', '{ts escape="sql"}About{/ts}', 'About', NULL, 'AND', '156', '1', NULL, 4 );
