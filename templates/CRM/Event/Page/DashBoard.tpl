{* CiviEvent DashBoard (launch page) *}
<div id="help" class="solid-border-bottom">
    {capture assign=findContactURL}{crmURL p="civicrm/contact/search/basic" q="reset=1"}{/capture}
    {capture assign=importURL}{crmURL p="civicrm/event/import" q="reset=1"}{/capture}
    {capture assign=configPagesURL}{crmURL p="civicrm/admin/event" q="reset=1"}{/capture}
    <p>{ts 1=$configPagesURL}CiviEvent allows you to create customized page(s) for creating and registering online events. Administrators can create or modify your Online Events Pages from <a href="%1">here</a>.{/ts}</p>
    <p>{ts 1=$findContactURL 2=$importURL}You can also input and track offline Events. To enter events manually for individual contacts, use <a href="%1">Find Contacts</a> to locate the contact. Then click <strong>View</strong> to go to their summary page and click on the <strong>New Event</strong> link. You can also <a href="%2">import batches of offline participants</a> from other sources.{/ts}</p>
</div>

<h3>{ts}Event Summary{/ts}</h3>
<div class="description">
    {capture assign=findEventsURL}{crmURL p="civicrm/event/search/basic" q="reset=1"}{/capture}
    <p>{ts 1=$findEventsURL}This table provides a summary of <strong>Events</strong>, and includes shortcuts to view the Events details.{/ts}</p>
</div>

{*  *}
{if $eventSummary.total_events}
<table class="report">
<tr class="columnheader-dark">
    <th scope="col">{ts}Event{/ts}</th>
    <th scope="col">{ts}Event Type{/ts}</th>
    <th scope="col">{ts}Is Public{/ts}</th>
    <th scope="col">{ts}Max Participants{/ts}</th>
    <th scope="col">{ts}Rigistered Partcipants{/ts}</th>
    <th scope="col">{ts}Start and End Date{/ts}</th>
    <th></th>
</tr>
{foreach from=$eventSummary.events item=values key=id}
<tr>
    <td>{$values.eventTitle}</td>
    <td>{$values.eventType}</td>
    <td>{$values.isPublic}</td>
    <td>{$values.maxParticipants}</td>
    <td>{if $values.participant_url}<a href="{$values.participant_url}">{$values.participants}</a>{else}{$values.participants}{/if}</td>
    <td>{$values.startDate}&nbsp;{if $values.endDate}to{/if}&nbsp;{$values.endDate}</td>
    <td>{if $values.isMap}<a href="{$values.isMap}">Map</a>&nbsp;|&nbsp;{/if}<a href="{$values.configure}">Configure</a></td>
</tr>
{/foreach}

{if $eventSummary.total_events GT 10}
<tr>
    <td colspan="7"><a href="{crmURL p='civicrm/admin/event' q='reset=1'}">&raquo; {ts}List more events{/ts}...</a></td>
</tr>
{/if}
</table>
{/if}

{if $pager->_totalItems}
    <h3>{ts}Recent Event Participations{/ts}</h3>
    <div class="form-item">
        {include file="CRM/Event/Form/Selector.tpl" context="DashBoard"}
    </div>
{/if}