{if $countryID}
{foreach from=$countryID key=countryIndex item=dontCare}
{include file="CRM/common/stateCountry.tpl" index=$countryIndex}
{/foreach}
{/if}

{include file="CRM/Profile/Form/Dynamic.tpl"}