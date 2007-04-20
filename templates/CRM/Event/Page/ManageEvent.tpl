{capture assign=newEventURL}{crmURL q="action=add&reset=1"}{/capture}
{capture assign=icalURL}{crmURL p='civicrm/event/ical' q="reset=1"}{/capture}
{capture assign=icalPage}{crmURL p='civicrm/event/ical' q="reset=1&page=1"}{/capture}
{capture assign=pastEventsURL}{crmURL q="action=browse&past=true&reset=1"}{/capture}
{capture assign=currentEventsURL}{crmURL q="reset=1"}{/capture}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}

{if $action eq 1 or $action eq 2 }
   {include file="CRM/Event/Page/ManageEventEdit.tpl"}
{/if}
<div id="help">
  {if $past and $action ne 1 and $action ne 2} 
    <p>{ts 1=$pastEventsURL 2=$docURLTitle 3="http://wiki.civicrm.org/confluence//x/4Cs"}This page lists current (in-progress) and upcoming events. Click a column header to sort by that column. <a href="%1">Click here</a> to browse completed (past) events (<a href="%3" target="_blank" title="%2">read more...</a>).{/ts}
  {else}
    <p>{ts 1=$currentEventsURL 2=$docURLTitle 3="http://wiki.civicrm.org/confluence//x/4Cs"}This page lists completed (past) events. Click a column header to sort by that column. <a href="%1">Click here</a> to browse current (in-progress) and upcoming events (<a href="%3" target="_blank" title="%2">read more...</a>).{/ts}
  {/if}
</div>
{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item" id=event_status_id>
        {strip}
        {ts 1=$newEventURL 2=$icalPage 3=$icalURL} <a href="%1" id="newManageEvent">&raquo; New Event</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="%2">&raquo; Browse iCalendar Listing</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="%3">&raquo; Download iCalendar File</a>{/ts} &nbsp; {help id='icalendar'}   
        <br />
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
        <thead>
         <tr class="columnheader">
            <th field="Event" dataType="String">{ts}Event{/ts}</th>
            <th field="City" dataType="String">{ts}City{/ts}</th>
            <th field="State" dataType="String">{ts}State/Province{/ts}</th>
            <th field="Public?" dataType="String">{ts}Public?{/ts}</th>
            <th field="Start Date" dataType="String">{ts}Starts{/ts}</th>
            <th field="End Date" dataType="String">{ts}Ends{/ts}</th>
	    <th field="Enabled"  dataType="String" >{ts}Active?{/ts}</th>
	    <th datatype="html"></th>
         </tr>
        </thead>
        <tbody> 
        {foreach from=$rows item=row}
          <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title} ({ts}ID:{/ts} {$row.id})</td> 
            <td>{$row.city}</td>  
            <td>{$row.state_province}</td>	
            <td>{if $row.is_public eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>    
	    <td>{$row.start_date|crmDate}</td>
   	    <td>{$row.end_date|crmDate}</td>
	    <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	    <td>{$row.action}</td>
          </tr>
        {/foreach}    
        </tbody>
        </table>
        {if $past and $action ne 1 and $action ne 2} 
           <div class="form-item">
             <a href="{$pastEventsURL}" id="pastEvents">&raquo; {ts}Show Past Events{/ts}</a>
           </div>
        {else}
           <div class="form-item">
             <a href="{$currentEventsURL}" id="currentEvents">&raquo; {ts}Show Current and Upcoming Events{/ts}</a>
           </div>
        {/if}
        {/strip}
      
    </div>
</div>
{else}
  {if $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$newEventURL}There are no events created yet. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
  {/if}
{/if}
