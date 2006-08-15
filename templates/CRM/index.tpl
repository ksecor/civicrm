{if $config->debug}
{include file="CRM/common/debug.tpl"}
{/if}
{include file="CRM/common/calendar/js.tpl}

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>

{include file="CRM/common/$metaTpl.tpl"}

</div> {* end crm-container div *}
