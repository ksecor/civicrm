{* CiviEvent DashBoard (launch page) *}
{capture assign=newEventURL}{crmURL p="civicrm/event/manage" q="action=add&reset=1"}{/capture}
{capture assign=configPagesURL}{crmURL p="civicrm/event/manage" q="reset=1"}{/capture}
{if $eventAdmin}
<div class="float-right">
<table class="form-layout-compressed">
<tr>
    <td><a href="{$configPagesURL}" class="button"><span>&raquo; Manage Events</span></a></td>
    <td><a href="{$newEventURL}" class="button"><span>&raquo; New Event</span></a></td>
</tr>
</table>
</div>
{/if}

<h3>{ts}Event Summary{/ts} {help id="id-event-intro"}</h3>
{if $eventSummary.total_events}
<table class="report">
<tr class="columnheader-dark">
    <th scope="col">{ts}Event{/ts}</th>
    <th scope="col">{ts}ID{/ts}</th>
    <th scope="col">{ts}Type{/ts}</th>
    <th scope="col">{ts}Public{/ts}</th>
    <th scope="col">{ts}Date(s){/ts}</th>
    <th scope="col">{ts}Participants{/ts}</th>
{if $eventAdmin or $eventMap}
    <th></th>
{/if}
</tr>
{foreach from=$eventSummary.events item=values key=id}
<tr>
    <td><a href="{crmURL p="civicrm/event/info" q="reset=1&id=`$id`"}" title="{ts}View event info page"{/ts}>{$values.eventTitle}</a></td>
    <td>{$id}</td>
    <td>{$values.eventType}</td>
    <td>{$values.isPublic}</td>
    <td class="nowrap">{$values.startDate}&nbsp;{if $values.endDate}to{/if}&nbsp;{$values.endDate}</td>
    <td class="right">
       {$eventSummary.statusDisplay}:&nbsp;<strong>{if $values.participants_url and $values.participants}<a href="{$values.participants_url}" title="{ts 1=$eventSummary.statusDisplay}View %1 participants{/ts}">{$values.participants}</a>{else}{$values.participants}{/if}</strong><br />
       {$eventSummary.statusDisplayPending}:&nbsp;<strong>{if $values.pending_url and $values.pending}<a href="{$values.pending_url}" title="{ts 1=$eventSummary.statusDisplayPending}View %1 participants{/ts}">{$values.pending}</a>{else}{$values.pending}{/if}</strong>
       {if $values.maxParticipants}<br />{ts 1=$values.maxParticipants}(max %1){/ts}{/if}
    </td>
{if $eventAdmin or $eventMap}
    <td>
{if $values.isMap}
  <a href="{$values.isMap}">{ts}Map{/ts}</a>&nbsp;|&nbsp;
{/if}
{if $eventAdmin}
  <a href="{$values.configure}">{ts}Configure{/ts}</a>
{/if}
{/if}
    </td>
</tr>
{/foreach}

{if $eventSummary.total_events GT 10}
<tr>
    <td colspan="7"><a href="{crmURL p='civicrm/admin/event' q='reset=1'}">&raquo; {ts}Browse more events{/ts}...</a></td>
</tr>
{/if}
</table>
{/if}

{if $pager->_totalItems}
    <h3>{ts}Recent Registrations{/ts}</h3>
    <div class="form-item">
        {include file="CRM/Event/Form/Selector.tpl" context="event_dashboard"}
    </div>
{/if}
