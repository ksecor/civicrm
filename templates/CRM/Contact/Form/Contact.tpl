{* Contact.tpl is the first template file to be invoked by the controller when any form associated with Contact is displayed *}
{* This template file includes the appropriate file depending on the mode value set in the label field of the mode text box *} 

{*$form.mode.label*}
{if $form.mode.label eq 1}
{include file="CRM/Contact/Form/Contact/Add.tpl"} 
{/if}
{if $form.mode.label eq 2}
{include file="CRM/Contact/Form/Contact/View.tpl"} 
{/if}
{if $form.mode.label eq 4}
{include file="CRM/Contact/Form/Contact/Update.tpl"} 
{/if}
{if $form.mode.label eq 8}
{include file="CRM/Contact/Form/Contact/Delete.tpl"} 
{/if}
{if $form.mode.label eq 16}
{include file="CRM/Contact/Form/Contact/AddMini.tpl"} 
{/if}
{if $form.mode.label eq 32}
{include file="CRM/Contact/Form/Contact/SearchMini.tpl"} 
{/if}
{if $form.mode.label eq 64}
{include file="CRM/Contact/Form/Contact/Search.tpl"} 
{/if}
