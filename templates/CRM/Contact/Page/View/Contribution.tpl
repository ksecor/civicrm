{capture assign=newContribURL}{crmURL p="civicrm/contribute/contribution" q="reset=1&action=add&cid=`$contactId`"}{/capture}
<div id="help">
<p>{ts 1=$newContribURL}This page lists all contributions received from {$display_name} since inception.
Click <a href="%1">New Contribution</a> to record a new offline contribution for this contact.{/ts}.
</div>
<table class="report form-layout-compressed">
    <tr>
    <td class="header-dark">{ts}Total Contributed{/ts}</td><td> &nbsp; {if $total_amount}{$total_amount}{else}n/a{/if}</td>
    <td class="header-dark"> &nbsp; {ts}# Contributions{/ts}</td><td> &nbsp; {$pager->_totalItems}</td>
    <td class="header-dark"> &nbsp; {ts}Avg Amount{/ts}</td><td> &nbsp; {$total_amount/$pager->_totalItems}</td>
    {if $cancel_amount}
        <th class="header-dark"> &nbsp; {ts}Cancelled{/ts}</th><th> &nbsp; {$cancel_amount}</th>
    {/if}
    </tr>
</table>

{include file="CRM/Contribute/Form/Selector.tpl"}

<div class="action-link">
<a href="{$newContribURL}">&raquo; {ts}New Contribution{/ts}</a>
<div>