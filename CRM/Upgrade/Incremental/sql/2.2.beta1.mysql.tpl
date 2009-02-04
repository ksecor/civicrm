{if $multilingual}
  {foreach from=$locales item=locale}
     DROP VIEW IF EXISTS civicrm_event_page_{$locale};
  {/foreach}
{/if}

ALTER TABLE `civicrm_domain` 
  MODIFY version varchar(32) COMMENT 'The civicrm version this instance is running';

{if $notifyAbsent}
-- CRM-3989
ALTER TABLE `civicrm_pcp_block`
  ADD notify_email varchar(255) DEFAULT NULL COMMENT 'If set, notification is automatically emailed to this email-address on create/update Personal Campaign Page';
{/if}

ALTER TABLE `civicrm_custom_group` CHANGE `min_multiple` `min_multiple` INT( 10 ) UNSIGNED NULL COMMENT 'minimum number of multiple records (typically 0?)',
CHANGE `max_multiple` `max_multiple` INT( 10 ) UNSIGNED NULL COMMENT 'maximum number of multiple records, if 0 - no max';