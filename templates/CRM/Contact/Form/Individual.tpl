{* Individual.tpl is the first template file to be invoked by the controller when any form associated with Individual is displayed *}
{* This template file polls the appropriate file depending on the mode value set in the label field of the mode text box *} 

{if $mode eq 1}
{include file="CRM/Contact/Form/Individual/Add.tpl"} 
{elseif $mode eq 2}
{include file="CRM/Contact/Form/Individual/View.tpl"} 
{elseif $mode eq 4}
{include file="CRM/Contact/Form/Individual/Add.tpl"} 
{/if}



