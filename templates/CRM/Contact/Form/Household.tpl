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
{if $form.mode.label eq 16}
{include file="CRM/Contact/Form/Household/AddMini.tpl"} 
{/if}
{if $form.mode.label eq 32}
{include file="CRM/Contact/Form/Household/SearchMini.tpl"} 
{/if}
{if $form.mode.label eq 64}
{include file="CRM/Contact/Form/Household/Search.tpl"} 
{/if}
