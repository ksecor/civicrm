{if $mode == 16}
{include file="CRM/Contact/Form/BasicSearch.tpl}
{elseif $mode == 32}
{include file="CRM/Contact/Form/AdvancedSearch.tpl}
{else}
  Please check the URL.
{/if}
