{* CiviGrant DashBoard (launch page) *}
<div id="help" class="solid-border-bottom">
    {capture assign=findContactURL}{crmURL p="civicrm/contact/search/basic" q="reset=1"}{/capture}
    <p>CiviGrant allows you to register small amounts of money passed to your constituents and track further workflow.
    </p>{ts 1=$findContactURL }You can also input and track grants. To enter grants manually for individual contacts, use <a href="%1">Find Contacts</a> to locate the contact. Then click <strong>View</strong> to go to their summary page, select the grants tab and click on the <strong>New grant</strong> link.{/ts}</p>
</div>

<h3>{ts}Grants Summary{/ts}</h3>
<div class="description">
    {capture assign=findGrantsURL}{crmURL p="civicrm/grant/search" q="reset=1"}{/capture}
    <p>{ts 1=$findGrantsURL}This table provides a summary of <strong>Grant Totals</strong>, and includes shortcuts to view the grant details for these commonly used search periods. Click the Grant Status to see a list of Contacts for that grant status. To run your own customized searches - click <a href="%1">Find grants</a>. You can search by Contact Name, Amount, grant type and a variety of other criteria.{/ts}
    
    </p>
</div>

{if $grantSummary.total_grants}
You have {$grantSummary.total_grants} grant(s) registered in your database.
<table class="report">
<tr class="columnheader-dark">
    <th scope="col">{ts}Grant status{/ts}</th>
    <th scope="col">{ts}Number of grants{/ts}</th>
</tr>

{foreach from=$grantSummary.per_status item=status key=id}
<tr>
    <td><a href="{crmURL p="civicrm/grant/search" q="reset=1&status=`$id`&force=1"}">{$status.label}</a></td>
    <td><a href="{crmURL p="civicrm/grant/search" q="reset=1&status=`$id`&force=1"}">{$status.total}</a></td>
</tr>
{/foreach}
<tr class="columnfooter">
    <td>TOTAL:</td>
    <td>{$grantSummary.total_grants}</td>
</tr>
</table>
{else}
You have no Grants registered in your Database

{/if}


{if $pager->_totalItems}
    
    <h3>{ts}Recent Grants{/ts}</h3>
    <div class="form-item">
        {include file="CRM/Grant/Form/Selector.tpl" context="DashBoard"}
    </div>
{/if}
