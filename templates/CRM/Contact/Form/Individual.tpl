{*$form.mode.label*}
{if $form.mode.label eq 1}
{include file="CRM/Contact/Form/Individual/Create.tpl"} 
{/if}
{if $form.mode.label eq 2}
{include file="CRM/Contact/Form/Individual/View.tpl"} 
{/if}
{if $form.mode.label eq 4}
{include file="CRM/Contact/Form/Individual/Update.tpl"} 
{/if}
{if $form.mode.label eq 8}
{include file="CRM/Contact/Form/Individual/Delete.tpl"} 
{/if}
{if $form.mode.label eq 16}
{include file="CRM/Contact/Form/Individual/CreateMini.tpl"} 
{/if}
{if $form.mode.label eq 32}
{include file="CRM/Contact/Form/Individual/SearchMini.tpl"} 
{/if}
{if $form.mode.label eq 64}
{include file="CRM/Contact/Form/Individual/Search.tpl"} 
{/if}




