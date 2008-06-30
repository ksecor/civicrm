{* PledgeBank DashBoard (launch page) *}
<h3>{ts}Pledge Summary{/ts}</h3>
{if $pledgeSummary.total_pledges}
<table class="report">
<tr class="columnheader-dark">
    <th scope="col">{ts}Title{/ts}</th>
    <th scope="col">{ts}ID{/ts}</th>
    <th scope="col">{ts}Signers limit{/ts}</th>
    <th scope="col">{ts}Deadline{/ts}</th>
    <th scope="col">{ts}Creator{/ts}</th>
    <th scope="col">{ts}Status{/ts}</th>
    <th scope="col">{ts}Active?{/ts}</th>
{if $pledgeAdmin}
    <th></th>
{/if}
</tr>
{foreach from=$pledgeSummary.pledges item=values key=id}
<tr>
    <td><a href="{crmURL p="civicrm/pledge/info" q="reset=1&id=`$id`"}" title="{ts}View pledge info page"{/ts}>{$values.title}</a></td>
    <td>{$id}</td>
    <td class="right">{$values.signersLimit}</td>
    <td>{$values.deadline}</td>
    <td>{$values.displayName}</td>
    <td>{$values.status}</td>
    <td class="right">{$values.isActive}</td>
{if $pledgeAdmin}
    <td> <a href="{$values.configure}">{ts}Configure{/ts}</a>
{/if}
    </td>
</tr>
{/foreach}
{if $pledgeSummary.total_pledges GT 10}
<tr>
    <td colspan="7"><a href="{crmURL p='civicrm/admin/pledge' q='reset=1'}">&raquo; {ts}Browse more pledges{/ts}...</a></td>
</tr>
{/if}
</table>
{/if}
{if $pager->_totalItems}
    <h3>{ts}Recent Pledge Signers{/ts}</h3>
    <div class="form-item">
        {include file="CRM/PledgeBank/Form/Selector.tpl" context="pledge_dashboard"}
    </div>
{/if}
