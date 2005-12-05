{capture assign=newContribURL}{crmURL p="civicrm/contribute/contribution" q="reset=1&action=add&cid=`$contactId`"}{/capture}
<div id="help">
<p>{ts 1=$newContribURL}This page lists all contributions received from {$display_name} since inception.
Click <a href="%1">New Contribution</a> to record a new offline contribution for this contact.{/ts}.
</div>

{include file="CRM/Contribute/Page/ContributionTotals.tpl"}
<p>
{include file="CRM/Contribute/Form/Selector.tpl"}
</p>

<div class="action-link">
<a href="{$newContribURL}">&raquo; {ts}New Contribution{/ts}</a>
<div>