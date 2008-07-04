{* CiviPledge DashBoard (launch page) *}
{if $pledgeAdmin}
    {capture assign=newPageURL}{crmURL p="civicrm/admin/pledge" q="action=add&reset=1"}{/capture}
    {capture assign=configPagesURL}{crmURL p="civicrm/admin/pldedge" q="reset=1"}{/capture}
{/if}

{* Sample layout with hard-coded values. dgg *}
<h3>{ts}Pledge Summary{/ts}</h3>
<table class="report">
<tr class="columnheader-dark">
    <th>&nbsp;</th>
    <th scope="col" colspan="2" class="right" style="padding-right: 10px;">June 2008</th>
    <th scope="col" colspan="2" class="right" style="padding-right: 10px;">July 2008<br /><span class="extra">{ts}(month-to-date){/ts}</span></th>
    <th scope="col" colspan="2" class="right" style="padding-right: 10px;">2008<br /><span class="extra">{ts}(year-to-date){/ts}</span></th>
    <th scope="col" colspan="2" class="right" style="padding-right: 10px;">{ts}Cumulative{/ts}<br /><span class="extra">{ts}(since inception){/ts}</span></th>
</tr>
<tr>
    <td><strong>{ts}Total Pledges{/ts}</strong></td>
    {* prior month *}
        <td class="right"><a href="{$priorMonth.Valid.url}">8</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$priorMonth.Valid.url}">$ 4,000.00</a></td>
    {* current month *}
        <td class="right"><a href="{$currentMonth.Valid.url}">3</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$currentMonth.Valid.url}">$ 2,100.00</a></td>
    {* current year *}
        <td class="right"><a href="{$currentYear.Valid.url}">63</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$currentYear.Valid.url}">$ 15,600.00</a></td>
    {* cumulative *}
        <td class="right"><a href="{$cumulative.Valid.url}">344</a></td><td class="right"><a href="{$cumulative.Valid.url}">$ 120,100.00</a></td>
</tr>
<tr>
    <td><strong>{ts}Payments Received{/ts}</strong></td>
    {* prior month *}
        <td class="right"><a href="{$priorMonth.Valid.url}">8</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$priorMonth.Valid.url}">$ 4,000.00</a></td>
    {* current month *}
        <td class="right"><a href="{$currentMonth.Valid.url}">3</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$currentMonth.Valid.url}">$ 2,100.00</a></td>
    {* current year *}
        <td class="right"><a href="{$currentYear.Valid.url}">63</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$currentYear.Valid.url}">$ 15,600.00</a></td>
    {* cumulative *}
        <td class="right"><a href="{$cumulative.Valid.url}">344</a></td><td class="right"><a href="{$cumulative.Valid.url}">$ 120,100.00</a></td>
</tr>
<tr>
    <td><strong>{ts}Balance Due{/ts}</strong></td>
    {* prior month *}
        <td class="right"><a href="{$priorMonth.Valid.url}">8</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$priorMonth.Valid.url}">$ 4,000.00</a></td>
    {* current month *}
        <td class="right"><a href="{$currentMonth.Valid.url}">3</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$currentMonth.Valid.url}">$ 2,100.00</a></td>
    {* current year *}
        <td class="right"><a href="{$currentYear.Valid.url}">63</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$currentYear.Valid.url}">$ 15,600.00</a></td>
    {* cumulative *}
        <td class="right"><a href="{$cumulative.Valid.url}">344</a></td><td class="right"><a href="{$cumulative.Valid.url}">$ 120,100.00</a></td>
</tr>
<tr>
    <td><strong>{ts}Past Due{/ts}</strong></td>
    {* prior month *}
        <td class="right"><a href="{$priorMonth.Valid.url}">8</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$priorMonth.Valid.url}">$ 4,000.00</a></td>
    {* current month *}
        <td class="right"><a href="{$currentMonth.Valid.url}">3</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$currentMonth.Valid.url}">$ 2,100.00</a></td>
    {* current year *}
        <td class="right"><a href="{$currentYear.Valid.url}">63</a></td><td class="right" style="border-right: 5px double #92B6EC;"><a href="{$currentYear.Valid.url}">$ 15,600.00</a></td>
    {* cumulative *}
        <td class="right"><a href="{$cumulative.Valid.url}">344</a></td><td class="right"><a href="{$cumulative.Valid.url}">$ 120,100.00</a></td>
</tr>
</table>
{*
<table class="report">
<tr class="columnheader-dark">
    <th scope="col">{ts}Period{/ts}</th>
    <th scope="col">{ts}Total Amount{/ts}</th>
    <th scope="col" title="Pledge Count"><strong>#</strong></th><th></th></tr>
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
*}

<div class="spacer"></div>

{if $pager->_totalItems}
    <h3>{ts}Recent Pledges{/ts}</h3>
    <div class="form-item">
        {include file="CRM/Pledge/Form/Selector.tpl" context="dashboard"}
    </div>
{/if}
