{* CiviContribute DashBoard (launch page) *}
{if $isAdmin}
    {capture assign=newPageURL}{crmURL p="civicrm/admin/contribute" q="action=add&reset=1"}{/capture}
    {capture assign=configPagesURL}{crmURL p="civicrm/admin/contribute" q="reset=1"}{/capture}
    <div class="action-link float-right"><a href="{$newPageURL}">&raquo; New Contribution Page</a><br /><a href="{$configPagesURL}">&raquo; Manage Contribution Pages</a></div>
{/if}

<h3>{ts}Contributions Summary{/ts} {help id="id-contribute-intro"}</h3>
<table class="report">
<tr class="columnheader-dark">
    <th scope="col">{ts}Period{/ts}</th>
    <th scope="col">{ts}Total Amount{/ts}</th>
    <th scope="col" title="Contribution Count"><strong>#</strong></th><th></th></tr>
<tr>
    <td><strong>{ts}Current Month-To-Date{/ts}</strong></td>
    <td class="label">{if NOT $monthToDate.Valid.amount}{ts}(n/a){/ts}{else}{$monthToDate.Valid.amount|crmMoney}{/if}</td>
    <td class="label">{$monthToDate.Valid.count}</td>
    <td><a href="{$monthToDate.Valid.url}">{ts}view details{/ts}...</a></td>
</tr>
<tr>
    <td><strong>{ts}Current Year-To-Date{/ts}</strong></td>
    <td class="label">{if NOT $yearToDate.Valid.amount}{ts}(n/a){/ts}{else}{$yearToDate.Valid.amount|crmMoney}{/if}</td>
    <td class="label">{$yearToDate.Valid.count}</td>
    <td><a href="{$yearToDate.Valid.url}">{ts}view details{/ts}...</a></td>
</tr>
<tr>
    <td><strong>{ts}Cumulative{/ts}</strong><br />{ts}(since inception){/ts}</td>
    <td class="label">{if NOT $startToDate.Valid.amount}{ts}(n/a){/ts}{else}{$startToDate.Valid.amount|crmMoney}{/if}</td>
    <td class="label">{$startToDate.Valid.count}</td>
    <td><a href="{$startToDate.Valid.url}">{ts}view details{/ts}...</a></td>
</tr>
</table>

{if $pager->_totalItems}
    <h3>{ts}Recent Contributions{/ts}</h3>
    <div class="form-item">
        {include file="CRM/Contribute/Form/Selector.tpl" context="dashboard"}
    </div>
{/if}
