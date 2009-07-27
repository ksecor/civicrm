{* Include links to enter Activities if session has 'edit' permission *}
{if $action EQ 16 and $permission EQ 'edit' and !$addAssigneeContact and !$addTargetContact}
    <div class="buttons" style="text-align: left">{include file="CRM/Activity/Form/ActivityLinks.tpl"}</div>
{/if}

{if $action eq 1 or $action eq 2 or $action eq 8 or $action eq 4 or $action eq 32768} {* add, edit, delete or view or detach*}
    {include file="CRM/Activity/Form/Activity.tpl"}
{else}
    {include file="CRM/Activity/Selector/Activity.tpl"}
{/if}