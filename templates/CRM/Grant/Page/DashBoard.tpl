{* CiviGrant DashBoard (launch page) *}
<div id="help" class="solid-border-bottom">
    <p>
     CiviGrant allows you to register small amounts of money passed to your constituents and track further workflow.
    </p>
</div>

<h3>{ts}Grants Summary{/ts}</h3>
<div class="description">
    <p>
     Description here.
    </p>
</div>

{if $grantSummary.total_grants}
You have {$grantSummary.total_grants} grant(s) registered in your database.
<table class="report">
<tr class="columnheader-dark">
    <th scope="col">{ts}Grant status{/ts}</th>
    <th scope="col">{ts}Number of grants{/ts}</th>
</tr>
</tr>
{foreach from=$grantSummary.per_status item=status key=id}
<tr>
    <td><a href="{crmURL p="civicrm/grant/search" q="reset=1&status=`$id`"}">{$status.label}</a></td>
    <td>{$status.total}</td>
</tr>
{/foreach}
<tr class="columnheader-dark">
    <td>TOTAL:</td>
    <th>{$grantSummary.total_grants}</th>
</tr>
</table>

{/if}


{if $pager->_totalItems}
    <h3>{ts}Recent Registrations{/ts}</h3>
    <div class="form-item">
        {include file="CRM/Event/Form/Selector.tpl" context="DashBoard"}
    </div>
{/if}
