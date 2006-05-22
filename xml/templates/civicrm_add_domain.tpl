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

-- Sample Instant Msg service providers
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Yahoo', @domain_id, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('MSN', @domain_id, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('AIM', @domain_id, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('GTalk', @domain_id, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Jabber', @domain_id, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Skype', @domain_id, 0, 1);

-- Sample mobile phone service providers
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Sprint', @domain_id, 0, 1);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Verizon', @domain_id, 0, 1);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Cingular', @domain_id, 0, 1);

-- Activity types
INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( @domain_id, '{ts}Meeting{/ts}', '{ts}Schedule a Meeting{/ts}', 1, 1);
INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( @domain_id, '{ts}Phone Call{/ts}', '{ts}Schedule a Phone Call{/ts}', 1, 1);
INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( @domain_id, '{ts}Email{/ts}', '{ts}Email Sent{/ts}', 1, 1);
INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( @domain_id, '{ts}SMS{/ts}', '{ts}SMS{/ts}', 1, 0);
INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( @domain_id, '{ts}Event{/ts}', '{ts}Event{/ts}', 1, 0);

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

-- individual prefix and suffix enums 
{if $locale == 'en_US'}
INSERT INTO civicrm_individual_prefix (domain_id, name, weight, is_active) VALUES ( @domain_id, 'Mrs', 1, 1);
{/if}
INSERT INTO civicrm_individual_prefix (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}Ms{/ts}', 2, 1);
INSERT INTO civicrm_individual_prefix (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}Mr{/ts}', 3, 1);
INSERT INTO civicrm_individual_prefix (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}Dr{/ts}', 4, 1);

INSERT INTO civicrm_individual_suffix (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}Jr{/ts}', 1, 1);
INSERT INTO civicrm_individual_suffix (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}Sr{/ts}', 2, 1);
INSERT INTO civicrm_individual_suffix (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}II{/ts}', 3, 1);

INSERT INTO civicrm_gender (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}Female{/ts}', 1, 1);
INSERT INTO civicrm_gender (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}Male{/ts}', 2, 1);
INSERT INTO civicrm_gender (domain_id, name, weight, is_active) VALUES ( @domain_id, '{ts}Transgender{/ts}', 3, 1);

INSERT INTO civicrm_dupe_match (domain_id, entity_table , rule) VALUES ( @domain_id,'contact_individual','first_name AND last_name AND email');

-- contribution types
INSERT INTO
   civicrm_contribution_type(name, domain_id, is_reserved, is_active, is_deductible)
VALUES
  ( '{ts}Donation{/ts}'             , @domain_id, 0, 1, 1 ),
  ( '{ts}Member Dues{/ts}'          , @domain_id, 0, 1, 1 ), 
  ( '{ts}Campaign Contribution{/ts}', @domain_id, 0, 1, 0 );

-- payment instrument
INSERT INTO  
   civicrm_payment_instrument(name, domain_id, is_reserved, is_active)  
VALUES  
  ( '{ts}Credit Card{/ts}', @domain_id, 1, 1 ),   
  ( '{ts}Debit Card{/ts}' , @domain_id, 1, 1 ),    
  ( '{ts}Cash{/ts}'       , @domain_id, 1, 1 ),     
  ( '{ts}Check{/ts}'      , @domain_id, 1, 1 ),      
  ( '{ts}EFT{/ts}'        , @domain_id, 1, 1 );
  
-- accepted credit cards
INSERT INTO
    civicrm_accept_credit_card(name, title, domain_id, is_reserved, is_active)
VALUES
  ( 'Visa', '{ts}Visa{/ts}', @domain_id, 0, 1 ),
  ( 'MasterCard', '{ts}MasterCard{/ts}', @domain_id, 0, 1 ),
  ( 'Amex', '{ts}American Express{/ts}', @domain_id, 0, 1 ),   
  ( 'Discover', '{ts}Discover{/ts}', @domain_id, 0, 1 );
