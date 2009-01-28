{if $multilingual}
  {foreach from=$locales item=locale}
     DROP VIEW IF EXISTS civicrm_event_page_{$locale};
  {/foreach}
{/if}
