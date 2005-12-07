#
# insert some data for domain, reserved location_types, and reserved relationship_types
#
INSERT INTO civicrm_domain( name, contact_name, email_domain ) 
    VALUES ( 'CRM Test Domain', 'Mr System Administrator', 'FIXME.ORG' );

INSERT INTO civicrm_location_type( domain_id, name, description, is_reserved, is_active ) VALUES( 1, 'Home', 'Place of residence', 1, 1 );
INSERT INTO civicrm_location_type( domain_id, name, description, is_reserved, is_active, is_default ) VALUES( 1, 'Work', 'Work location', 1, 1, 1 );
INSERT INTO civicrm_location_type( domain_id, name, description, is_reserved, is_active ) VALUES( 1, 'Main', 'Main office location', 0, 1 );
INSERT INTO civicrm_location_type( domain_id, name, description, is_reserved, is_active ) VALUES( 1, 'Other', 'Another location', 0, 1 );

INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 1, 'Child of', 'Parent of', 'Parent/child relationship.', 'Individual', 'Individual', 1 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 1, 'Spouse of', 'Spouse of', 'Spousal relationship.', 'Individual', 'Individual', 1 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 1, 'Sibling of','Sibling of', 'Sibling relationship.','Individual','Individual', 1 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 1, 'Employee of', 'Employer of', 'Employment relationship.','Individual','Organization', 1 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 1, 'Volunteer for', 'Volunteer is', 'Volunteer relationship.','Individual','Organization', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 1, 'Head of Household for', 'Head of Household is', 'Head of household.','Individual','Household', 0 );
INSERT INTO civicrm_relationship_type( domain_id, name_a_b, name_b_a, description, contact_type_a, contact_type_b, is_reserved )
    VALUES( 1, 'Household Member of', 'Household Member is', 'Household membership.','Individual','Household', 0 );

-- Sample Tags
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( 1, 'Non-profit', 'Any not-for-profit organization.', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( 1, 'Company', 'For-profit organization.', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( 1, 'Government Entity', 'Any governmental entity.', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( 1, 'Major Donor', 'High-value supporter of our organization.', NULL );
INSERT INTO civicrm_tag( domain_id, name, description, parent_id )
    VALUES( 1, 'Volunteer', 'Active volunteers.', NULL );


INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Yahoo', 1, 1, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('MSN', 1, 1, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('AIM', 1, 1, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Jabber', 1, 0, 1);
INSERT INTO civicrm_im_provider(name, domain_id, is_reserved, is_active) VALUES('Indiatimes', 1, 0, 0);

INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Sprint', 1, 1, 1);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Verizon', 1, 1, 1);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Cingular', 1, 0, 1);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Reliance Infocom', 1, 1, 1);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('BPL Mobile', 1, 0, 0);
INSERT INTO civicrm_mobile_provider (name, domain_id, is_reserved, is_active) VALUES ('Airtel', 1, 0, 1);

INSERT INTO civicrm_geo_coord (id, coord_type, coord_units, coord_ogc_wkt_string) VALUES (1, 'LatLong', 'Degree', 31);

-- Sample Extended Property Group and Fields
INSERT INTO civicrm_custom_group
    (domain_id, name, title, extends, style, collapse_display, help_pre, weight, is_active)
VALUES
    (1, 'voter_info', 'Voter Info', 'Individual', 'Tab', 0, 'Please complete the voter information fields as data becomes available for this contact.', 1, 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active)
VALUES
    (1, 'registered_voter', 'Registered Voter?', 'Boolean', 'Radio', 1, 1, '', 1);
    
INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active, is_searchable)
VALUES
    (1, 'party_registration', 'Party Registration', 'String', 'Text', 0, 2, 'If contact is registered, enter party name here.', 1, 1);
    
INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active)
VALUES
    (1, 'date_last_voted', 'Date Last Voted', 'Date', 'Select Date', 0, 3, '', 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active, is_searchable)
VALUES
    (1, 'voting_precinct', 'Voting Precinct', 'Int', 'Text', 0, 4, 'Precinct number - if available.', 1, 1);


-- Custom data for educational qualifications
INSERT INTO civicrm_custom_group
    (domain_id, name, title, extends, style, collapse_display, help_pre, weight, is_active)
VALUES
    (1, 'education', 'Education Qualification', 'Individual', 'Tab', 0, 'Please furnish educational history starting from high school', 2, 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active)
VALUES
    (2, 'degree', 'Degree Obtained', 'String', 'Text', 1, 2, '', 1),
    (2, 'school_college', 'School / College', 'String', 'Text', 0, 1, '', 1),
    (2, 'marks', 'Marks Obtained', 'String', 'Text', 0, 3, '', 1),
    (2, 'date_of_degree', 'Degree date', 'Date', 'Select Date', 0, 4, '', 1);

INSERT INTO civicrm_uf_group
    (domain_id, is_active, form_type, title)
VALUES
    (1,1,'CiviCRM Profile','CiviCRM Name and Address');

INSERT INTO civicrm_uf_field
    (uf_group_id, field_name, weight, is_active, is_view, is_required, visibility, help_post)
VALUES
    (1,'first_name',1,1,0,1,'Public User Pages',''),
    (1,'last_name',2,1,0,1,'Public User Pages','First and last name will be shared with other visitors to the site.'),
    (1,'street_address',3,1,0,0,'User and User Admin Only','Your street address will not be shared with visitors to the site.'),
    (1,'city',4,1,0,0,'Public User Pages','Your postal code and city of residence will be shared with others so folks can find others in their community.'),
    (1,'postal_code',5,1,0,0,'Public User Pages',''),
    (1,'state_province',6,1,0,0,'Public User Pages','Your state/province and country of residence will be shared with others so folks can find others in their community.'),
    (1,'country',7,1,0,0,'Public User Pages',''),
    (1,'email',8,1,1,0,'Public User Pages','');

INSERT INTO civicrm_mailing_component
    (domain_id,name,component_type,subject,body_html,body_text,is_default,is_active)
VALUES
    (1,'Mailing Header','Header','This is the Header','HTML Body of Header','Text Body of Header',1,1),
    (1,'Mailing Footer','Footer','This is the Footer','HTML Body of Footer','Text Body of Footer',1,1);

-- Bounce classification patterns
INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('AOL', 'AOL Terms of Service complaint', 1);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (1, 'Client TOS Notification');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Away', 'Recipient is on vacation', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (2, '(be|am)? (out of|away from) (the|my)? (office|computer|town)'),
    (2, 'i am on vacation');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Dns', 'Unable to resolve recipient domain', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (3, 'name(server entry| lookup failure)'),
    (3, 'no (mail server|matches to nameserver query|dns entries)'),
    (3, 'reverse dns entry');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Host', 'Unable to deliver to desintation mail server', 3);
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
        VALUES ('Inactive', 'User account is no longer active', 1);
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
        VALUES ('Invalid', 'Email address is not valid', 1);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (6, 'Empty group|Group name did not validate'),
    (6, 'Validation failed'),
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
        VALUES ('Loop', 'Mail routing error', 3);
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
        VALUES ('Quota', 'User inbox is full', 3);
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
        VALUES ('Relay', 'Unable to reach destination mail server', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (9, 'cannot find your hostname'),
    (9, 'ip name lookup'),
    (9, 'not configured to relay mail'),
    (9, 'relay (not permitted|access denied)'),
    (9, 'relayed mail to .+? not allowed'),
    (9, 'sender ip must resolve'),
    (9, 'unsupported mail destination'),
    (9, 'unable to relay');

INSERT INTO civicrm_mailing_bounce_type 
        (name, description, hold_threshold) 
        VALUES ('Spam', 'Message caught by a content filter', 1);
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
        VALUES ('Syntax', 'Error in SMTP transaction', 3);
INSERT INTO civicrm_mailing_bounce_pattern 
        (bounce_type_id, pattern) 
        VALUES
    (11, 'nonstandard smtp line terminator'),
    (11, 'syntax error in from address'),
    (11, 'unknown smtp code');

