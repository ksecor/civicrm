{* CiviPledge DashBoard (launch page) *}

<h3>{ts}Pledge Summary{/ts}</h3>
<table class="report">
<tr class="columnheader-dark">
    <th>&nbsp;</th>
    <th scope="col" colspan="2" class="right" style="padding-right: 10px;">{$previousMonthYear}</th>
    <th scope="col" colspan="2" class="right" style="padding-right: 10px;">{$currentMonthYear}<br /><span class="extra">{ts}(current month){/ts}</span></th>
    <th scope="col" colspan="2" class="right" style="padding-right: 10px;">{$curYear}<br /><span class="extra">{ts}(current year){/ts}</span></th>
    <th scope="col" colspan="2" class="right" style="padding-right: 10px;">{ts}Cumulative{/ts}<br /><span class="extra">{ts}(since inception){/ts}</span></th>
</tr>
<tr>
    <td scope="row"><strong>{ts}Total Pledges{/ts}</strong></td>
    {* prior month *}
        <td class="right"><a href="{$previousToDate.Completed.purl}">{$previousToDate.Completed.pledge_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $previousToDate.Completed.pledge_count }<a href="{$previousToDate.Completed.purl}">{$previousToDate.Completed.pledge_amount|crmMoney}</a>{/if}</td>
    {* current month *}
        <td class="right"><a href="{$monthToDate.Completed.purl}">{$monthToDate.Completed.pledge_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $monthToDate.Completed.pledge_count }<a href="{$monthToDate.Completed.purl}">{$monthToDate.Completed.pledge_amount|crmMoney}</a>{/if}</td>
    {* current year *}
        <td class="right"><a href="{$yearToDate.Completed.purl}">{$yearToDate.Completed.pledge_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $yearToDate.Completed.pledge_count }<a href="{$yearToDate.Completed.purl}">{$yearToDate.Completed.pledge_amount|crmMoney}</a>{/if}</td>
    {* cumulative *}
        <td class="right"><a href="{$startToDate.Completed.purl}">{$startToDate.Completed.pledge_count}</a></td><td class="right">{if $startToDate.Completed.pledge_count }<a href="{$startToDate.Completed.purl}">{$startToDate.Completed.pledge_amount|crmMoney}</a>{/if}</td>
</tr>
<tr>
    <td scope="row"><strong>{ts}Payments Received{/ts}</strong></td>
    {* prior month *}
     <td class="right"><a href="{$previousToDate.Completed.url}">{$previousToDate.Completed.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $previousToDate.Completed.received_count }<a href="{$previousToDate.Completed.url}">{$previousToDate.Completed.received_amount|crmMoney}</a>{/if}</td>
    {* current month *}
        <td class="right"><a href="{$monthToDate.Completed.url}">{$monthToDate.Completed.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $monthToDate.Completed.received_count}<a href="{$monthToDate.Completed.url}">{$monthToDate.Completed.received_amount|crmMoney}</a>{/if}</td>
    {* current year *}
        <td class="right"><a href="{$yearToDate.Completed.url}">{$yearToDate.Completed.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $yearToDate.Completed.received_count }<a href="{$yearToDate.Completed.url}">{$yearToDate.Completed.received_amount|crmMoney}</a>{/if}</td>
    {* cumulative *}
        <td class="right"><a href="{$startToDate.Completed.url}">{$startToDate.Completed.received_count}</a></td><td class="right">{if $startToDate.Completed.received_count }<a href="{$startToDate.Completed.url}">{$startToDate.Completed.received_amount|crmMoney}</a>{/if}</td>  
</tr>
<tr>
    <td scope="row"><strong>{ts}Balance Due{/ts}</strong></td>
    {* prior month *}
        <td class="right"><a href="{$previousToDate.Pending.url}">{$previousToDate.Pending.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $previousToDate.Pending.received_count }<a href="{$previousToDate.Pending.url}">{$previousToDate.Pending.received_amount|crmMoney}</a>{/if}</td>
    {* current month *}
        <td class="right"><a href="{$monthToDate.Pending.url}">{$monthToDate.Pending.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $monthToDate.Pending.received_count }<a href="{$monthToDate.Pending.url}">{$monthToDate.Pending.received_amount|crmMoney}</a>{/if}</td>
    {* current year *}
        <td class="right"><a href="{$yearToDate.Pending.url}">{$yearToDate.Pending.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $yearToDate.Pending.received_count }<a href="{$yearToDate.Pending.url}">{$yearToDate.Pending.received_amount|crmMoney}</a>{/if}</td>
    {* cumulative *}
        <td class="right"><a href="{$startToDate.Pending.url}">{$startToDate.Pending.received_count}</a></td><td class="right">{if $startToDate.Pending.received_count }<a href="{$startToDate.Pending.url}">{$startToDate.Pending.received_amount|crmMoney}</a>{/if}</td>
</tr>
<tr>
    <td scope="row"><strong>{ts}Past Due{/ts}</strong></td>
    {* prior month *}
        <td class="right"><a href="{$previousToDate.Overdue.url}">{$previousToDate.Overdue.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $previousToDate.Overdue.received_count }<a href="{$previousToDate.Overdue.url}">{$previousToDate.Overdue.received_amount|crmMoney}</a>{/if}</td>
    {* current month *}
        <td class="right"><a href="{$monthToDate.Overdue.url}">{$monthToDate.Overdue.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $monthToDate.Overdue.received_count }<a href="{$monthToDate.Overdue.url}">{$monthToDate.Overdue.received_amount|crmMoney}</a>{/if}</td>
    {* current year *}
        <td class="right"><a href="{$yearToDate.Overdue.url}">{$yearToDate.Overdue.received_count}</a></td><td class="right" style="border-right: 5px double #92B6EC;">{if $yearToDate.Overdue.received_count }<a href="{$yearToDate.Overdue.url}">{$yearToDate.Overdue.received_amount|crmMoney}</a>{/if}</td>
    {* cumulative *}
        <td class="right"><a href="{$startToDate.Overdue.url}">{$startToDate.Overdue.received_count}</a></td><td class="right">{if $startToDate.Overdue.received_count }<a href="{$startToDate.Overdue.url}">{$startToDate.Overdue.received_amount|crmMoney}</a>{/if}</td>
</tr>
</table>

<div class="spacer"></div>

{if $pager->_totalItems}
    <h3>{ts}Recent Pledges{/ts}</h3>
    <div>
        {include file="CRM/Pledge/Form/Selector.tpl" context="dashboard"}
    </div>
{/if}
