{* CiviContribute Dashboard (launch page) *}
<div id="help">
    {capture assign=findContactURL}{crmURL p="civicrm/contact/search/basic" q="reset=1"}{/capture}
    {capture assign=importURL}{crmURL p="civicrm/contribute/import" q="reset=1"}{/capture}
    {capture assign=configPagesURL}{crmURL p="civicrm/admin/contribute" q="reset=1"}{/capture}
    {ts 1=$findContactURL 2=$importURL 3=$configPagesURL}
        <p>CiviContribute allows you to create customized page(s) for collecting online contributions.
        Administrators can create or modify your Online Contribution Pages from <a href="%3">here</a>.</p>
 
        <p>You can also input and track offline contributions. To enter contributions manually for individual
        contacts, use <a href="%1">Find Contacts</a> to locate the contact. Then click <strong>View</strong>
        to go to their summary page and click on the </strong>New Contribution</strong> link.
        You can also <a href="%2">import batches of offline contributions</a> from other sources.</p>
    {/ts}
</div>
<hr size="1" noshade/>
<h3>{ts}Contributions Summary{/ts}</h3>
<div class="description">
    {capture assign=findContribsURL}{crmURL p="civicrm/contact/search/basic" q="reset=1"}{/capture}
    <p>{ts 1=$findContribsURL}This table provides a summary of <strong>Contribution Totals</strong>,
    and includes shortcuts to view the contribution details for these commonly used search periods.
    To run your own customized searches - click <a href="%1">Find Contributions</a>.
    You can search by Contributor Name, Amount Range, and a variety of other
    criteria.{/ts}</p>
</div>
<table class="report form-layout-compressed">
<tr class="columnheader-dark">
    <th>Period</th>
    <th>{ts}Total Amount{/ts}</th>
    <th><strong>#</strong></th><th></th></tr>
<tr>
    <td><strong>{ts}Current Month-To-Date{/ts}</strong></td>
    <td class="label">{if NOT $monthToDate.Valid.amount}(n/a){else}{$monthToDate.Valid.amount}{/if}</td>
    <td class="label">{$monthToDate.Valid.count}</td>
    <td><a href="{$monthToDate.Valid.url}">view details...</td>
</tr>
<tr>
    <td><strong>{ts}Current Year-To-Date{/ts}</strong></td>
    <td class="label">{if NOT $yearToDate.Valid.amount}(n/a){else}{$yearToDate.Valid.amount}{/if}</td>
    <td class="label">{$yearToDate.Valid.count}</td>
    <td><a href="{$yearToDate.Valid.url}">view details...</td>
</tr>
<tr>
    <td><strong>{ts}Cumulative</strong><br />(since inception){/ts}</td>
    <td class="label">{if NOT $startToDate.Valid.amount}(n/a){else}{$startToDate.Valid.amount}{/if}</td>
    <td class="label">{$startToDate.Valid.count}</td>
    <td><a href="{$startToDate.Valid.url}">view details...</td>
</tr>
</table>
<hr size="1" noshade/>
<h3>{ts}Recent Contributions{/ts}</h3>
<div class="form-item">
{include file="CRM/Contribute/Form/Selector.tpl"}
</div>
<div class="action-link">
<a href="{crmURL p='civicrm/contribute/search' q='reset=1&force=1'}">view more recent contributions...</a>
</div>