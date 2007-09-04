{* Include links to enter Activities if session has 'edit' permission *}
{if $permission EQ 'edit'}
    {include file="CRM/Contact/Page/View/ActivityLinks.tpl"}
{/if}

{if $action eq 1 or $action eq 2 or $action eq 8} {* add, edit or delete*}
    {include file="CRM/Activity/Form/Activity.tpl"}
{else}
    {include file="CRM/Activity/Selector/Activity.tpl"}
{/if}