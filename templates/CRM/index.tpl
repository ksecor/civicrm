{if $config->debug}
{include file="CRM/common/debug.tpl"}
{/if}
{include file="CRM/common/calendar/js.tpl}

<div id="crm-container">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>

{if $config->userFramework eq 'Mambo'}
    {include file="CRM/common/mambo.tpl"}
{else}
    {include file="CRM/common/drupal.tpl"}
{/if}
</div> {* end crm-container div *}
