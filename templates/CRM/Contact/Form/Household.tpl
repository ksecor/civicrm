{* Household.tpl is the first template file to be invoked by the controller when any form associated with Household is displayed *}
{* This template file polls the appropriate file depending on the mode value set in the label field of the mode text box *} 

{*$form.mode.label*}
{if $form.mode.label eq 1}
{include file="CRM/Contact/Form/Household/Add.tpl"} 
{/if}
{if $form.mode.label eq 2}
{include file="CRM/Contact/Form/Household/View.tpl"} 
{/if}
{if $form.mode.label eq 4}
{include file="CRM/Contact/Form/Household/Update.tpl"} 
{/if}
{if $form.mode.label eq 8}
{include file="CRM/Contact/Form/Household/Delete.tpl"} 
{/if}

