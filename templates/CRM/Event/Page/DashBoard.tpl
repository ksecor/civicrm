{* CiviEvent DashBoard (launch page) *}
<div id="help" class="solid-border-bottom">
    {capture assign=findContactURL}{crmURL p="civicrm/contact/search/basic" q="reset=1"}{/capture}
    {capture assign=importURL}{crmURL p="civicrm/event/import" q="reset=1"}{/capture}
    {capture assign=configPagesURL}{crmURL p="civicrm/admin/event" q="reset=1"}{/capture}
    <p>{ts 1=$configPagesURL}CiviEvent allows you to create customized page(s) for creating and registering online events. Administrators can create or modify your Online Events Pages from <a href="%1">here</a>.{/ts}</p>
    <p>{ts 1=$findContactURL 2=$importURL}You can also input and track offline Events. To enter events manually for individual contacts, use <a href="%1">Find Contacts</a> to locate the contact. Then click <strong>View</strong> to go to their summary page and click on the <strong>New Event</strong> link. You can also <a href="%2">import batches of offline participants</a> from other sources.{/ts}</p>
</div>

<h3>{ts}Event Participation Summary{/ts}</h3>
<div class="description">
    {capture assign=findEventsURL}{crmURL p="civicrm/event/search/basic" q="reset=1"}{/capture}
    <p>{ts 1=$findEventsURL}This table provides a summary of <strong>Participant Totals</strong>, and includes shortcuts to view the participation details for these commonly used search periods. To run your own customized searches - click <a href="%1">Find Participants</a>. You can search by Event Name, Event Status and a variety of other criteria.{/ts}</p>
</div>


{* This needs to be fixed for CiviEvent *}

{*
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
*}
{if $pager->_totalItems}
    <h3>{ts}Recent Event Participations{/ts}</h3>
    <div class="form-item">
        {include file="CRM/Event/Form/Selector.tpl" context="DashBoard"}
    </div>
{/if}