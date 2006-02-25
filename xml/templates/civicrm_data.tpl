-- This file provides template to civicrm_data.mysql. Inserts all base data needed for a new CiviCRM DB

-- All domain-keyed values handled by this included file
{include file="civicrm_add_domain.tpl" context="baseData"}

-- Sample counties (state-province and country lists defined in a separate tpl files)
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

