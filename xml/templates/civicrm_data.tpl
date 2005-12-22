
# insert some data for domain, reserved location_types, and reserved relationship_types
#
INSERT INTO civicrm_domain( name, contact_name, email_domain ) 
    VALUES ( 'CRM Test Domain', 'Mr System Administrator', 'FIXME.ORG' );

INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( {$civicrmDomainId}, '{ts}Home{/ts}', 'HOME', '{ts}Place of residence{/ts}', 0, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active, is_default ) VALUES( {$civicrmDomainId}, '{ts}Work{/ts}', 'WORK', '{ts}Work location{/ts}', 0, 1, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( {$civicrmDomainId}, '{ts}Main{/ts}', NULL, '{ts}Main office location{/ts}', 0, 1 );
INSERT INTO civicrm_location_type( domain_id, name, vcard_name, description, is_reserved, is_active ) VALUES( {$civicrmDomainId}, '{ts}Other{/ts}', NULL, '{ts}Another location{/ts}', 0, 1 );

INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( {$civicrmDomainId}, '{ts}Child of{/ts}', '{ts}Parent of{/ts}', '{ts}Parent/child relationship.{/ts}', 'Individual', 'Individual', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( {$civicrmDomainId}, '{ts}Spouse of{/ts}', '{ts}Spouse of{/ts}', '{ts}Spousal relationship.{/ts}', 'Individual', 'Individual', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( {$civicrmDomainId}, '{ts}Sibling of{/ts}','{ts}Sibling of{/ts}', '{ts}Sibling relationship.{/ts}','Individual','Individual', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( {$civicrmDomainId}, '{ts}Employee of{/ts}', '{ts}Employer of{/ts}', '{ts}Employment relationship.{/ts}','Individual','Organization', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( {$civicrmDomainId}, '{ts}Volunteer for{/ts}', '{ts}Volunteer is{/ts}', '{ts}Volunteer relationship.{/ts}','Individual','Organization', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( {$civicrmDomainId}, '{ts}Head of Household for{/ts}', '{ts}Head of Household is{/ts}', '{ts}Head of household.{/ts}','Individual','Household', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( {$civicrmDomainId}, '{ts}Household Member of{/ts}', '{ts}Household Member is{/ts}', '{ts}Household membership.{/ts}','Individual','Household', 0 );

-- Sample Tags
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( {$civicrmDomainId}, '{ts}Non-profit{/ts}', '{ts}Any not-for-profit organization.{/ts}', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( {$civicrmDomainId}, '{ts}Company{/ts}', '{ts}For-profit organization.{/ts}', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( {$civicrmDomainId}, '{ts}Government Entity{/ts}', '{ts}Any governmental entity.{/ts}', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( {$civicrmDomainId}, '{ts}Major Donor{/ts}', '{ts}High-value supporter of our organization.{/ts}', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( {$civicrmDomainId}, '{ts}Volunteer{/ts}', '{ts}Active volunteers.{/ts}', NULL );


INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Yahoo', {$civicrmDomainId}, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('MSN', {$civicrmDomainId}, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('AIM', {$civicrmDomainId}, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('GTalk', {$civicrmDomainId}, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Jabber', {$civicrmDomainId}, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Skype', {$civicrmDomainId}, 0, 1);

INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Sprint', {$civicrmDomainId}, 0, 1);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Verizon', {$civicrmDomainId}, 0, 1);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Cingular', {$civicrmDomainId}, 0, 1);

INSERT INTO civicrm_county (name, state_province_id) VALUES ('Alameda', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('Contra Costa', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('Marin', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('San Francisco', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('San Mateo', 1004);
INSERT INTO civicrm_county (name, state_province_id) VALUES ('Santa Clara', 1004);

INSERT INTO civicrm_geo_coord (id, coord_type, coord_units, coord_ogc_wkt_string) VALUES (1, 'LatLong', 'Degree', 31);

-- Bounce classification patterns
INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('AOL', '{ts}AOL Terms of Service complaint{/ts}', 1);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (1, 'Client TOS Notification');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Away', '{ts}Recipient is on vacation{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (2, '(be|am)? (out of|away from) (the|my)? (office|computer|town)'),
    (2, 'i am on vacation');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Dns', '{ts}Unable to resolve recipient domain{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (3, 'name(server entry| lookup failure)'),
    (3, 'no (mail server|matches to nameserver query|dns entries)'),
    (3, 'reverse dns entry');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Host', '{ts}Unable to deliver to destintation mail server{/ts}', 3);
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
        VALUES ('Inactive', '{ts}User account is no longer active{/ts}', 1);
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
        VALUES ('Invalid', '{ts}Email address is not valid{/ts}', 1);
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
        VALUES ('Loop', '{ts}Mail routing error{/ts}', 3);
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
        VALUES ('Quota', '{ts}User inbox is full{/ts}', 3);
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
        VALUES ('Relay', '{ts}Unable to reach destination mail server{/ts}', 3);
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
        VALUES ('Spam', '{ts}Message caught by a content filter{/ts}', 1);
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
        VALUES ('Syntax', '{ts}Error in SMTP transaction{/ts}', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (11, 'nonstandard smtp line terminator'),
    (11, 'syntax error in from address'),
    (11, 'unknown smtp code');

INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( {$civicrmDomainId}, '{ts}Meeting{/ts}', '{ts}Schedule a Meeting{/ts}', 1, 1);
INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( {$civicrmDomainId}, '{ts}Phone Call{/ts}', '{ts}Schedule a Phone Call{/ts}', 1, 1);
INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( {$civicrmDomainId}, '{ts}Email{/ts}', '{ts}Email Sent{/ts}', 1, 1);
INSERT INTO civicrm_activity_type (domain_id, name, description, is_active, is_reserved) VALUES ( {$civicrmDomainId}, '{ts}Event{/ts}', '{ts}Event{/ts}', 1, 0);

INSERT INTO civicrm_mailing_component
    (domain_id,name,component_type,subject,body_html,body_text,is_default,is_active)
VALUES
    ({$civicrmDomainId},'Mailing Header','Header','This is the Header','HTML Body of Header','Text Body of Header',1,1),
    ({$civicrmDomainId},'Mailing Footer','Footer','This is the Footer','HTML Body of Footer','Text Body of Footer',1,1),
    ({$civicrmDomainId},'Subscribe Message','Subscribe','Subscription confirmation request','You have a pending subscription to {ldelim}subscribe.group{rdelim}.  To confirm this subscription, reply to this email.','You have a pending subscription to {ldelim}subscribe.group{rdelim}.  To confirm this subscription, reply to this email.',1,1),
    ({$civicrmDomainId},'Welcome Message','Welcome','Welcome','Welcome to {ldelim}welcome.group{rdelim}!','Welcome to {ldelim}welcome.group{rdelim}!',1,1),
    ({$civicrmDomainId},'Unsubscribe Message','Unsubscribe','Unsubscribe results','You have been unsubscribed from {ldelim}unsubscribe.group{rdelim}.','You have been unsubscribed from {ldelim}unsubscribe.group{rdelim}.',1,1),
    ({$civicrmDomainId},'Opt-out Message','OptOut','Goodbye','You have been removed from {ldelim}domain.name{rdelim}.  Goodbye.','You have been removed from {ldelim}domain.name{rdelim}.  Goodbye.',1,1),
    ({$civicrmDomainId},'Auto-responder','Reply','Automated response','Thank you for your reply.','Thank you for your reply.',1,1);


{if $locale == 'en_US'}
INSERT INTO civicrm_individual_prefix (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, 'Mrs', 1, 1);
{/if}
INSERT INTO civicrm_individual_prefix (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}Ms{/ts}', 2, 1);
INSERT INTO civicrm_individual_prefix (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}Mr{/ts}', 3, 1);
INSERT INTO civicrm_individual_prefix (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}Dr{/ts}', 4, 1);

INSERT INTO civicrm_individual_suffix (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}Jr{/ts}', 1, 1);
INSERT INTO civicrm_individual_suffix (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}Sr{/ts}', 2, 1);
INSERT INTO civicrm_individual_suffix (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}II{/ts}', 3, 1);

INSERT INTO civicrm_gender (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}Female{/ts}', 1, 1);
INSERT INTO civicrm_gender (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}Male{/ts}', 2, 1);
INSERT INTO civicrm_gender (domain_id, name, weight, is_active) VALUES ( {$civicrmDomainId}, '{ts}Transgender{/ts}', 3, 1);

INSERT INTO civicrm_dupe_match (domain_id, entity_table , rule) VALUES ( {$civicrmDomainId},'contact_individual','first_name AND last_name AND email');

-- contribution types
INSERT INTO
   civicrm_contribution_type(name, domain_id, is_reserved, is_active, is_deductible)
VALUES
  ( '{ts}Donation{/ts}'             , {$civicrmDomainId}, 0, 1, 1 ),
  ( '{ts}Member Dues{/ts}'          , {$civicrmDomainId}, 0, 1, 1 ), 
  ( '{ts}Campaign Contribution{/ts}', {$civicrmDomainId}, 0, 1, 0 );

-- payment instrument
INSERT INTO  
   civicrm_payment_instrument(name, domain_id, is_reserved, is_active)  
VALUES  
  ( '{ts}Credit Card{/ts}', {$civicrmDomainId}, 1, 1 ),   
  ( '{ts}Debit Card{/ts}' , {$civicrmDomainId}, 1, 1 ),    
  ( '{ts}Cash{/ts}'       , {$civicrmDomainId}, 1, 1 ),     
  ( '{ts}Check{/ts}'      , {$civicrmDomainId}, 1, 1 ),      
  ( '{ts}EFT{/ts}'        , {$civicrmDomainId}, 1, 1 );
  
-- accepted credit cards
INSERT INTO
    civicrm_accept_credit_card(name, title, domain_id, is_reserved, is_active)
VALUES
  ( 'Visa', '{ts}Visa{/ts}', {$civicrmDomainId}, 0, 1 ),
  ( 'MasterCard', '{ts}MasterCard{/ts}', {$civicrmDomainId}, 0, 1 ),
  ( 'Amex', '{ts}American Express{/ts}', {$civicrmDomainId}, 0, 1 ),   
  ( 'Discover', '{ts}Discover{/ts}', {$civicrmDomainId}, 0, 1 );
