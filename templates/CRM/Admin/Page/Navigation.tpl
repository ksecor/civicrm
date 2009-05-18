<div id="help">
    {capture assign=crmURL}{crmURL p='civicrm/admin/menu' q="action=add&reset=1"}{/capture}
    {ts 1=$crmURL}Create CiviCRM Menus. ( <a href='%1'>Add Menu</a> ){/ts}
</div>
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/Navigation.tpl"}
{/if}

