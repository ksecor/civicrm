{* Organization.tpl is the first template file to be invoked by the controller when any form associated with Organization is displayed *}
{* This template file polls the appropriate file depending on the mode value set in the label field of the mode text box *} 

{if $mode eq 1}
{include file="CRM/Contact/Form/Organization/Add.tpl"} 
{/if}
{if $mode eq 2}
{include file="CRM/Contact/Form/Organization/View.tpl"} 
{/if}
{if $mode eq 4}
{include file="CRM/Contact/Form/Organization/Update.tpl"} 
{/if}
{if $mode eq 8}
{include file="CRM/Contact/Form/Organization/Delete.tpl"} 
{/if}




