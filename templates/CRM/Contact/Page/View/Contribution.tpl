<div id="search-status"> 
    {ts count=$pager->_totalItems plural='Found %count contributions.'}Found %count contribution.{/ts} 
    {ts}Total Amount:{/ts} {$total_amount} 
    {if $cancel_amount}&nbsp; &nbsp; {ts}Cancelled Contribution Amount:{/ts} {$cancel_amount}{/if} 
</div>

{include file="CRM/Contribute/Form/Selector.tpl"}

<p>

<a href="{crmURL p="civicrm/contribute/contribution" q="reset=1&action=add&cid=`$contactId`"}">{ts}Record New Contribution{/ts}</a>
<p>