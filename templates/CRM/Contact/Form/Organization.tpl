{*$form.mode.label*}
{if $form.mode.label eq 1}
{include file="CRM/Contact/Form/Organization/Add.tpl"} 
{/if}
{if $form.mode.label eq 2}
{include file="CRM/Contact/Form/Organization/View.tpl"} 
{/if}
{if $form.mode.label eq 4}
{include file="CRM/Contact/Form/Organization/Update.tpl"} 
{/if}
{if $form.mode.label eq 8}
{include file="CRM/Contact/Form/Organization/Delete.tpl"} 
{/if}
{if $form.mode.label eq 16}
{include file="CRM/Contact/Form/Organization/AddMini.tpl"} 
{/if}
{if $form.mode.label eq 32}
{include file="CRM/Contact/Form/Organization/SearchMini.tpl"} 
{/if}
{if $form.mode.label eq 64}
{include file="CRM/Contact/Form/Organization/Search.tpl"} 
{/if}




