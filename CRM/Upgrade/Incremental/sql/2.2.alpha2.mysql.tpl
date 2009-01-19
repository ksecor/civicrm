-- CRM-3546 CRM-2105 CRM-3248 CRM-3869
 UPDATE civicrm_option_group SET is_reserved = 0, is_active = 1 WHERE name IN( 'mail_protocol', 'visibility', 'greeting_type', 'phone_type' );
 UPDATE civicrm_option_group SET is_active   = 1 WHERE name = 'encounter_medium';
 SELECT @option_group_id_mp := max(id) from civicrm_option_group where name = 'mail_protocol';

{if $multilingual}
  INSERT INTO civicrm_option_value
    (option_group_id,     {foreach from=$locales item=locale}label_{$locale},{/foreach} value, name,       weight) VALUES
    (@option_group_id_mp, {foreach from=$locales item=locale}'IMAP',{/foreach}          1,     'IMAP',     1),
    (@option_group_id_mp, {foreach from=$locales item=locale}'Maildir',{/foreach}       2,     'Maildir',  2),
    (@option_group_id_mp, {foreach from=$locales item=locale}'POP3',{/foreach}          3,     'POP3',     3);
{else}
  INSERT INTO civicrm_option_value
    (option_group_id,     label,       value, name,       weight) VALUES
    (@option_group_id_mp, 'IMAP' ,     1,     'IMAP',     1),
    (@option_group_id_mp, 'Maildir',   2,     'Maildir',  2),
    (@option_group_id_mp, 'POP3'   ,   3,     'POP3',     3);
{/if}

ALTER TABLE `civicrm_domain` 
  MODIFY version varchar(32) COMMENT 'The civicrm version this instance is running';

-- CRM-3989
ALTER TABLE `civicrm_pcp_block`
  ADD notify_email varchar(255) DEFAULT NULL COMMENT 'If set, notification is automatically emailed to this email-address on create/update Personal Campaign Page';
