{capture assign=newEventURL}{crmURL q="action=add&reset=1"}{/capture}
{capture assign=icalFile}{crmURL p='civicrm/event/ical' q="reset=1"}{/capture}
{capture assign=icalFeed}{crmURL p='civicrm/event/ical' q="reset=1&page=1"}{/capture}
{capture assign=rssFeed}{crmURL p='civicrm/event/ical' q="reset=1&page=1&rss=1"}{/capture}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}


{if $action eq 1 or $action eq 2 }
    {include file="CRM/Event/Page/ManageEventEdit.tpl"}
{/if}

{include file="CRM/Event/Form/SearchEvent.tpl"}

 <a accesskey="N" href="{$newEventURL}" id="newManageEvent">&raquo; {ts}New Event{/ts}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="{$icalFile}">&raquo; {ts}Download iCalendar File{/ts}</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$icalFeed}" title="{ts}iCalendar Feed{/ts}"><img src="{$config->resourceBase}i/ical_feed.gif" alt="{ts}iCalendar Feed{/ts}" /></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$rssFeed}" title="{ts}RSS 2.0 Feed{/ts}"><img src="{$config->resourceBase}i/rss2.png" alt="{ts}RSS 2.0 Feed{/ts}" /></a>&nbsp;{help id='icalendar'} 

{if $rows}

<div id="ltype">
<p></p>
    <div class="form-item" id=event_status_id>
        {strip}
        {include file="CRM/common/pager.tpl" location="top"}
        {include file="CRM/common/pagerAToZ.tpl}    
        <table cellpadding="0" cellspacing="0" border="0">
         <tr class="columnheader">
            <th>{ts}Event{/ts}</th>
            <th>{ts}City{/ts}</th>
            <th>{ts}State/Province{/ts}</th>
            <th>{ts}Public?{/ts}</th>
            <th>{ts}Starts{/ts}</th>
            <th>{ts}Ends{/ts}</th>
	        <th>{ts}Active?{/ts}</th>
	        <th></th>
         </tr>
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
        </table>
        {include file="CRM/common/pager.tpl" location="bottom"}
        {/strip}
      
    </div>
</div>
{else}
   {if $isSearch eq 1}
    <div class="status messages">
        <dl>
            <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
            {capture assign=browseURL}{crmURL p='civicrm/event/manage' q="reset=1"}{/capture}
            <dd>
                {ts}No available Events match your search criteria. Suggestions:{/ts}
                <div class="spacer"></div>
                <ul>
                <li>{ts}Check your spelling.{/ts}</li>
                <li>{ts}Try a different spelling or use fewer letters.{/ts}</li>
                <li>{ts}Make sure you have enough privileges in the access control system.{/ts}</li>
                </ul>
                {ts 1=$browseURL}Or you can <a href='%1'>browse all available Current Events</a>.{/ts}
            </dd>
        </dl>
    </div>
   {else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>{ts 1=$newEventURL}There are no events created yet. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
   {/if}
{/if}
