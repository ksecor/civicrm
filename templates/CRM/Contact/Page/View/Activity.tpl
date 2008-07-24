{* Include links to enter Activities if session has 'edit' permission *}
{if ($permission EQ 'edit') and !$addAssigneeContact }
    {include file="CRM/Activity/Form/ActivityLinks.tpl"}
{/if}

{if $action eq 1 or $action eq 2 or $action eq 8 or $action eq 4 or $action eq 32768} {* add, edit, delete or view or detach*}
    {include file="CRM/Activity/Form/Activity.tpl"}
{else}
    {include file="CRM/Activity/Selector/Activity.tpl"}
{/if}