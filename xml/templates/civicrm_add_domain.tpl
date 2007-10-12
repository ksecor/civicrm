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

INSERT INTO civicrm_domain( id, name, email_name, email_address, email_domain, version ) 
    VALUES ( @domain_id, 'Domain Contact Name', 'FIXME', 'info@FIXME.ORG', 'FIXME.ORG', '2.0' );

-- Sample location types
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active, is_default ) VALUES( @domain_id, '{ts}Home{/ts}', 'HOME', '{ts}Place of residence{/ts}', 0, 1, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( @domain_id, '{ts}Work{/ts}', 'WORK', '{ts}Work location{/ts}', 0, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( @domain_id, '{ts}Main{/ts}', NULL, '{ts}Main office location{/ts}', 0, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( @domain_id, '{ts}Other{/ts}', NULL, '{ts}Other location{/ts}', 0, 1 );
-- the following location must stay with the untranslated Billing name, CRM-2064
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( @domain_id, 'Billing', NULL, '{ts}Billing Address location{/ts}', 1, 1 );

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
    (@domain_id,'{ts}Mailing Header{/ts}','Header','{ts}Descriptive Title for this Header{/ts}','{ts}Sample Header for HTML formatted content.{/ts}','{ts}Sample Header for TEXT formatted content.{/ts}',1,1),
    (@domain_id,'{ts}Mailing Footer{/ts}','Footer','{ts}Descriptive Title for this Footer.{/ts}','{ts}Sample Footer for HTML formatted content.{/ts}','{ts}Sample Footer for TEXT formatted content.{/ts}',1,1),
    (@domain_id,'{ts}Subscribe Message{/ts}','Subscribe','{ts}Subscription Confirmation Request{/ts}','{ts}You have a pending subscription to the {ldelim}subscribe.group{rdelim} mailing list. To confirm this subscription, reply to this email or click <a href="{ldelim}subscribe.url{rdelim}">here</a>.{/ts}','{ts}You have a pending subscription to the {ldelim}subscribe.group{rdelim} mailing list. To confirm this subscription, reply to this email or click on this link: {ldelim}subscribe.url{rdelim}{/ts}',1,1),
    (@domain_id,'{ts}Welcome Message{/ts}','Welcome','{ts}Your Subscription has been Activated{/ts}','{ts}Welcome. Your subscription to the {ldelim}welcome.group{rdelim} mailing list has been activated.{/ts}','{ts}Welcome. Your subscription to the {ldelim}welcome.group{rdelim} mailing list has been activated.{/ts}',1,1),
    (@domain_id,'{ts}Unsubscribe Message{/ts}','Unsubscribe','{ts}Un-subscribe Confirmation{/ts}','{ts}You have been un-subscribed from the following groups: {ldelim}unsubscribe.group{rdelim}. You can re-subscribe by mailing {ldelim}action.resubscribe{rdelim} or clicking <a href="{ldelim}action.resubscribeUrl{rdelim}">here</a>.{/ts}','{ts}You have been un-subscribed from the following groups: {ldelim}unsubscribe.group{rdelim}. You can re-subscribe by mailing {ldelim}action.resubscribe{rdelim} or clicking {ldelim}action.resubscribeUrl{rdelim}{/ts}',1,1),
    (@domain_id,'{ts}Resubscribe Message{/ts}','Resubscribe','{ts}Re-subscribe Confirmation{/ts}','{ts}You have been re-subscribed to the following groups: {ldelim}resubscribe.group{rdelim}. You can un-subscribe by mailing {ldelim}action.unsubscribe{rdelim} or clicking <a href="{ldelim}action.unsubscribeUrl{rdelim}">here</a>.{/ts}','{ts}You have been re-subscribed to the following groups: {ldelim}resubscribe.group{rdelim}. You can un-subscribe by mailing {ldelim}action.unsubscribe{rdelim} or clicking {ldelim}action.unsubscribeUrl{rdelim}{/ts}',1,1),
    (@domain_id,'{ts}Opt-out Message{/ts}','OptOut','{ts}Opt-out Confirmation{/ts}','{ts}Your email address has been removed from {ldelim}domain.name{rdelim} mailing lists.{/ts}','{ts}Your email address has been removed from {ldelim}domain.name{rdelim} mailing lists.{/ts}',1,1),
    (@domain_id,'{ts}Auto-responder{/ts}','Reply','{ts}Please Send Inquiries to Our Contact Email Address{/ts}','{ts}This is an automated reply from an un-attended mailbox. Please send any inquiries to the contact email address listed on our web-site.{/ts}','{ts}This is an automated reply from an un-attended mailbox. Please send any inquiries to the contact email address listed on our web-site.{/ts}',1,1),
    (@domain_id,'{ts}Resubscribe Message{/ts}','Resubscribe','{ts}Re-subscribe Confirmation{/ts}','{ts}You have been re-subscribed to the {ldelim}resubscribe.group{rdelim} mailing list, as requested.{/ts}','{ts}You have been re-subscribed to the {ldelim}resubscribe.group{rdelim} mailing list, as requested.{/ts}',1,1);



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
   (@domain_id, 'contact_view_options'          , '{ts}Contact View Options{/ts}'               , 0, 1),
   (@domain_id, 'contact_edit_options'          , '{ts}Contact Edit Options{/ts}'               , 0, 1),
   (@domain_id, 'advanced_search_options'       , '{ts}Advanced Search Options{/ts}'            , 0, 1),
   (@domain_id, 'user_dashboard_options'        , '{ts}User Dashboard Options{/ts}'             , 0, 1),
   (@domain_id, 'address_options'               , '{ts}Addressing Options{/ts}'                 , 0, 1),
   (@domain_id, 'group_type'                    , '{ts}Group Type{/ts}'                         , 0, 1),
   (@domain_id, 'grant_status'                  , '{ts}Grant status{/ts}'                       , 0, 1),
   (@domain_id, 'grant_type'                    , '{ts}Grant Type{/ts}'                         , 0, 1),
   (@domain_id, 'honor_type'                    , '{ts}Honor Type{/ts}'                         , 0, 1);

   
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
SELECT @option_group_id_cvOpt          := max(id) from civicrm_option_group where name = 'contact_view_options';
SELECT @option_group_id_ceOpt          := max(id) from civicrm_option_group where name = 'contact_edit_options';
SELECT @option_group_id_asOpt          := max(id) from civicrm_option_group where name = 'advanced_search_options';
SELECT @option_group_id_udOpt          := max(id) from civicrm_option_group where name = 'user_dashboard_options';
SELECT @option_group_id_adOpt          := max(id) from civicrm_option_group where name = 'address_options';
SELECT @option_group_id_gType          := max(id) from civicrm_option_group where name = 'group_type';
SELECT @option_group_id_grantSt        := max(id) from civicrm_option_group where name = 'grant_status';
SELECT @option_group_id_grantTyp       := max(id) from civicrm_option_group where name = 'grant_type';
SELECT @option_group_id_honorTyp       := max(id) from civicrm_option_group where name = 'honor_type';

INSERT INTO 
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`) 
VALUES
   (@option_group_id_pcm, '{ts}Phone{/ts}', 1, NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1),
   (@option_group_id_pcm, '{ts}Email{/ts}', 2, NULL, NULL, 0, NULL, 2, NULL, 0, 0, 1),
   (@option_group_id_pcm, '{ts}Postal Mail{/ts}', 3, NULL, NULL, 0, NULL, 3, NULL, 0, 0, 1),
   (@option_group_id_pcm, '{ts}SMS{/ts}', 4, NULL, NULL, 0, NULL, 4, NULL, 0, 0, 1),
   (@option_group_id_pcm, '{ts}Fax{/ts}', 5, NULL, NULL, 0, NULL, 5, NULL, 0, 0, 1),
 
   (@option_group_id_act, '{ts}Meeting{/ts}',    1, 'Meeting',    NULL, 0, NULL, 1, '{ts}Schedule a meeting{/ts}',    0, 1, 1),
   (@option_group_id_act, '{ts}Phone Call{/ts}', 2, 'Phone Call', NULL, 0, NULL, 2, '{ts}Schedule a Phone Call{/ts}', 0, 1, 1),
   (@option_group_id_act, '{ts}Email{/ts}',      3, 'Email',      NULL, 0, NULL, 3, '{ts}Email Sent{/ts}',            0, 1, 1),
   (@option_group_id_act, '{ts}SMS{/ts}',        4, 'SMS',        NULL, 0, NULL, 4, '{ts}SMS{/ts}',                   0, 1, 1),
   (@option_group_id_act, '{ts}Event{/ts}',      5, 'Event',      NULL, 0, NULL, 5, '{ts}Event{/ts}',                 0, 0, 1),
  
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
  (@option_group_id_ps, '{ts}Pending{/ts}'  ,  5, 'Pending',    NULL, 0, NULL, 5, NULL, 0, 1, 1),

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

-- note that these are not ts'ed since they are used for logic in most cases and not display
-- they are used for display only in the prefernces field settings
  (@option_group_id_cvOpt, 'Activities'   ,   1, NULL, NULL, 0, NULL,  1, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, 'Relationships',   2, NULL, NULL, 0, NULL,  2, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, 'Groups'       ,   3, NULL, NULL, 0, NULL,  3, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, 'Notes'        ,   4, NULL, NULL, 0, NULL,  4, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, 'Tags'         ,   5, NULL, NULL, 0, NULL,  5, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, 'Change Log'   ,   6, NULL, NULL, 0, NULL,  6, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, 'Contributions',   7, NULL, NULL, 0, NULL,  7, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, 'Memberships'  ,   8, NULL, NULL, 0, NULL,  8, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, 'Events'       ,   9, NULL, NULL, 0, NULL,  9, NULL, 0, 0, 1 ),
  (@option_group_id_cvOpt, '{ts}Cases{/ts}'         ,  10, NULL, NULL, 0, NULL,  10,NULL, 0, 0, 1 ),

  (@option_group_id_ceOpt, 'Communication Preferences',   1, NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1 ),
  (@option_group_id_ceOpt, 'Demographics'             ,   2, NULL, NULL, 0, NULL, 2, NULL, 0, 0, 1 ),
  (@option_group_id_ceOpt, 'Tags and Groups'          ,   3, NULL, NULL, 0, NULL, 3, NULL, 0, 0, 1 ),
  (@option_group_id_ceOpt, 'Notes'                    ,   4, NULL, NULL, 0, NULL, 4, NULL, 0, 0, 1 ),

  (@option_group_id_asOpt, 'Address Fields'          ,   1, NULL, NULL, 0, NULL,  1, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Custom Fields'           ,   2, NULL, NULL, 0, NULL,  2, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Activity History'        ,   3, NULL, NULL, 0, NULL,  3, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Scheduled Activities'    ,   4, NULL, NULL, 0, NULL,  4, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Relationships'           ,   5, NULL, NULL, 0, NULL,  5, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Notes'                   ,   6, NULL, NULL, 0, NULL,  6, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Change Log'              ,   7, NULL, NULL, 0, NULL,  7, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Contributions'           ,   8, NULL, NULL, 0, NULL,  8, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Memberships'             ,   9, NULL, NULL, 0, NULL,  9, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, 'Events'                  ,  10, NULL, NULL, 0, NULL, 10, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Cases{/ts}'          ,  11, NULL, NULL, 0, NULL, 11, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Case Activities{/ts}',  12, NULL, NULL, 0, NULL, 12, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Kabissa{/ts}'        ,  13, NULL, NULL, 0, NULL, 13, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}Grants{/ts}'         ,  14, NULL, NULL, 0, NULL, 14, NULL, 0, 0, 1 ),
  (@option_group_id_asOpt, '{ts}User SQL{/ts}'       ,  15, NULL, NULL, 0, NULL, 15, NULL, 0, 0, 1 ),

  (@option_group_id_udOpt, 'Groups'       , 1, NULL, NULL, 0, NULL, 1, NULL, 0, 0, 1 ),
  (@option_group_id_udOpt, 'Contributions', 2, NULL, NULL, 0, NULL, 2, NULL, 0, 0, 1 ),
  (@option_group_id_udOpt, 'Memberships'  , 3, NULL, NULL, 0, NULL, 3, NULL, 0, 0, 1 ),
  (@option_group_id_udOpt, 'Events'       , 4, NULL, NULL, 0, NULL, 4, NULL, 0, 0, 1 ),

  (@option_group_id_adOpt, 'Street Address'    ,  1, NULL, NULL, 0, NULL,  1, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'Addt\'l Address 1' ,  2, NULL, NULL, 0, NULL,  2, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'Addt\'l Address 2' ,  3, NULL, NULL, 0, NULL,  3, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'City'              ,  4, NULL, NULL, 0, NULL,  4, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'Zip / Postal Code' ,  5, NULL, NULL, 0, NULL,  5, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'Postal Code Suffix',  6, NULL, NULL, 0, NULL,  6, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'County'            ,  7, NULL, NULL, 0, NULL,  7, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'State / Province'  ,  8, NULL, NULL, 0, NULL,  8, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'Country'           ,  9, NULL, NULL, 0, NULL,  9, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'Latitude'          , 10, NULL, NULL, 0, NULL, 10, NULL, 0, 0, 1 ),
  (@option_group_id_adOpt, 'Longitude'         , 11, NULL, NULL, 0, NULL, 11, NULL, 0, 0, 1 ),

  (@option_group_id_gType, 'Access Control'  , 1, NULL, NULL, 0, NULL, 1, NULL, 0, 1, 1 ),
  (@option_group_id_gType, 'Mailing List'    , 2, NULL, NULL, 0, NULL, 2, NULL, 0, 1, 1 ),

  (@option_group_id_grantSt, '{ts}Pending{/ts}',  1, 'Pending',  NULL, 0, 1,    1, NULL, 0, 0, 1),
  (@option_group_id_grantSt, '{ts}Granted{/ts}',  2, 'Granted',  NULL, 0, NULL, 2, NULL, 0, 0, 1),
  (@option_group_id_grantSt, '{ts}Rejected{/ts}', 3, 'Rejected', NULL, 0, NULL, 3, NULL, 0, 0, 1),
  (@option_group_id_grantTyp, '{ts}Emergency{/ts}'          , 1, 'Emergency'         , NULL, 0, 1,    1, NULL, 0, 0, 1),    
  (@option_group_id_grantTyp, '{ts}Family Support{/ts}'     , 2, 'Family Support'    , NULL, 0, NULL, 2, NULL, 0, 0, 1),
  (@option_group_id_grantTyp, '{ts}General Protection{/ts}' , 3, 'General Protection', NULL, 0, NULL, 3, NULL, 0, 0, 1),
  (@option_group_id_grantTyp, '{ts}Impunity{/ts}'           , 4, 'Impunity'          , NULL, 0, NULL, 4, NULL, 0, 0, 1),
  (@option_group_id_honorTyp, '{ts}In Honor of{/ts}'        , 1, 'In Honor of'       , NULL, 0, 1,    1, NULL, 0, 0, 1),
  (@option_group_id_honorTyp, '{ts}In Memory of{/ts}'       , 2, 'In Memory of'      , NULL, 0, NULL, 2, NULL, 0, 0, 1);


-- sample membership status entries
INSERT INTO
    civicrm_membership_status(domain_id, name, start_event, start_event_adjust_unit, start_event_adjust_interval, end_event, end_event_adjust_unit, end_event_adjust_interval, is_current_member, is_admin, weight, is_default, is_active)
VALUES
    (@domain_id,'{ts}New{/ts}', 'join_date', null, null,'join_date','month',3, 1, 0, 1, 0, 1),
    (@domain_id,'{ts}Current{/ts}', 'start_date', null, null,'end_date', null, null, 1, 0, 2, 1, 1),
    (@domain_id,'{ts}Grace{/ts}', 'end_date', null, null,'end_date','month', 1, 1, 0, 3, 0, 1),
    (@domain_id,'{ts}Expired{/ts}', 'end_date', 'month', 1, null, null, null, 0, 0, 4, 0, 1);

{literal}
-- Initial state of system preferences
INSERT INTO 
     civicrm_preferences(domain_id, contact_id, is_domain, location_count, contact_view_options, contact_edit_options, advanced_search_options, user_dashboard_options, address_options, address_format, mailing_format, individual_name_format, address_standardization_provider, address_standardization_userid, address_standardization_url )
VALUES 
     (@domain_id,NULL,1,1,'12345678910','1234','123456789101112131415','1234','123456891011','{street_address}\n{supplemental_address_1}\n{supplemental_address_2}\n{city}{, }{state_province}{ }{postal_code}\n{country}\n{world_region}','{street_address}\n{supplemental_address_1}\n{supplemental_address_2}\n{city}{, }{state_province}{ }{postal_code}\n{country}','{individual_prefix}{ } {first_name}{ }{middle_name}{ }{last_name}{ }{individual_suffix}',NULL,NULL,NULL);
{/literal}

INSERT INTO `civicrm_preferences_date`
  (domain_id, name, start, end, minute_increment, format)
VALUES
  ( @domain_id, 'birth'     , 100,  0,  0, null        ),
  ( @domain_id, 'creditCard',   0, 10,  0, 'M Y'       ),
  ( @domain_id, 'custom'    ,  20, 20, 15, 'Y M d H i' ),
  ( @domain_id, 'datetime'  ,  10,  3, 15, null        ),
  ( @domain_id, 'duration'  ,   0,  0, 15, 'H i'       ),
  ( @domain_id, 'fixed'     ,   0,  5,  0, null        ),
  ( @domain_id, 'mailing'   ,   0,  1, 15, 'Y M d H i' ),
  ( @domain_id, 'relative'  ,  20, 20,  0, null        ),
  ( @domain_id, 'manual'    ,  20, 20,  0, null        );


-- various processor options
--
-- Table structure for table `civicrm_payment_processor_type`
--

INSERT INTO `civicrm_payment_processor_type` 
 (domain_id, name, title, description, is_active, is_default, user_name_label, password_label, signature_label, subject_label, class_name, url_site_default, url_api_default, url_recur_default, url_button_default, url_site_test_default, url_api_test_default, url_recur_test_default, url_button_test_default, billing_mode, is_recur )
VALUES 
 (@domain_id,'Dummy','{ts}Dummy Payment Processor{/ts}',NULL,1,1,'{ts}User Name{/ts}',NULL,NULL,NULL,'Payment_Dummy',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL),
 (@domain_id,'PayPal_Standard','{ts}PayPal - Website Payments Standard{/ts}',NULL,1,0,'{ts}Merchant Account Email{/ts}',NULL,NULL,NULL,'Payment_PayPalImpl','https://www.paypal.com/',NULL,'https://www.paypal.com/',NULL,'https://www.sandbox.paypal.com/',NULL,'https://www.sandbox.paypal.com/',NULL,4,1),
 (@domain_id,'PayPal','{ts}PayPal - Website Payments Pro{/ts}',NULL,1,0,'{ts}User Name{/ts}','{ts}Password{/ts}','{ts}Signature{/ts}',NULL,'Payment_PayPalImpl','https://www.paypal.com/','https://api-3t.paypal.com/',NULL,'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif','https://www.sandbox.paypal.com/','https://api-3t.sandbox.paypal.com/',NULL,'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',3,NULL),
 (@domain_id,'PayPal_Express','{ts}PayPal - Express{/ts}',NULL,1,0,'{ts}User Name{/ts}','{ts}Password{/ts}','{ts}Signature{/ts}',NULL,'Payment_PayPalImpl','https://www.paypal.com/','https://api-3t.paypal.com/',NULL,'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif','https://www.sandbox.paypal.com/','https://api-3t.sandbox.paypal.com/',NULL,'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',3,NULL),
 (@domain_id,'Google_Checkout','{ts}Google Checkout{/ts}',NULL,1,0,'{ts}Merchant ID{/ts}','{ts}Key{/ts}',NULL,NULL,'Payment_Google','https://checkout.google.com/',NULL,NULL,'http://checkout.google.com/buttons/checkout.gif','https://sandbox.google.com/checkout',NULL,NULL,'http://sandbox.google.com/checkout/buttons/checkout.gif',4,NULL),
 (@domain_id,'Moneris','{ts}Moneris{/ts}',NULL,1,0,'{ts}User Name{/ts}','{ts}Password{/ts}','{ts}Store ID{/ts}',NULL,'Payment_Moneris','https://www3.moneris.com/',NULL,NULL,NULL,'https://esqa.moneris.com/',NULL,NULL,NULL,1,1),
 (@domain_id,'AuthNet_AIM','{ts}Authorize.Net - AIM{/ts}',NULL,1,0,'{ts}API Login{/ts}','{ts}Payment Key{/ts}','{ts}MD5 Hash{/ts}',NULL,'Payment_AuthorizeNet','https://secure.authorize.net/gateway/transact.dll',NULL,'https://api.authorize.net/xml/v1/request.api',NULL,'https://test.authorize.net/gateway/transact.dll',NULL,'https://apitest.authorize.net/xml/v1/request.api',NULL,1,1),
 (@domain_id,'PayJunction','{ts}PayJunction{/ts}',NULL,1,0,'User Name','Password',NULL,NULL,'Payment_PayJunction','https://payjunction.com/quick_link',NULL,NULL,NULL,'https://payjunction.com/quick_link',NULL,NULL,NULL,1,1);

-- the default dedupe rules
INSERT INTO civicrm_dedupe_rule_group (domain_id, contact_type, threshold) VALUES (@domain_id, 'Individual', 20);

SELECT @dedupe_rule_group_id := MAX(id) FROM civicrm_dedupe_rule_group;

INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES
  (@dedupe_rule_group_id, 'civicrm_individual', 'first_name', 5),
  (@dedupe_rule_group_id, 'civicrm_individual', 'last_name',  7),
  (@dedupe_rule_group_id, 'civicrm_email',      'email',     10);

INSERT INTO civicrm_dedupe_rule_group (domain_id, contact_type, threshold) VALUES (@domain_id, 'Organization', 10);

SELECT @dedupe_rule_group_id := MAX(id) FROM civicrm_dedupe_rule_group;

INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES
  (@dedupe_rule_group_id, 'civicrm_organization', 'organization_name', 5),
  (@dedupe_rule_group_id, 'civicrm_email',        'email',             5);

INSERT INTO civicrm_dedupe_rule_group (domain_id, contact_type, threshold) VALUES (@domain_id, 'Household', 10);

SELECT @dedupe_rule_group_id := MAX(id) FROM civicrm_dedupe_rule_group;

INSERT INTO civicrm_dedupe_rule (dedupe_rule_group_id, rule_table, rule_field, rule_weight)
VALUES
  (@dedupe_rule_group_id, 'civicrm_household', 'household_name', 5),
  (@dedupe_rule_group_id, 'civicrm_email',     'email',          5);

