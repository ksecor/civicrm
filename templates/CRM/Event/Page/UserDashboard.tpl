<div class="view-content">
{if $event_rows}
  {strip}
  <div class="description">
    {ts}Click on the event name for more information.{/ts}
  </div>
  <table class="selector">
    <tr class="columnheader">
      <th>{ts}Event{/ts}</th>
      <th>{ts}Event Date(s){/ts}</th>
      <th>{ts}Status{/ts}</th>
      <th></th>
    </tr>
     {counter start=0 skip=1 print=false}
     {foreach from=$event_rows item=row}
        <tr id='rowid{$row.participant_id}' class="{cycle values="odd-row,even-row"}{if $row.status eq Cancelled} disabled{/if}">
           <td><a href="{crmURL p='civicrm/event/info' q="reset=1&id=`$row.event_id`&context=dashboard"}">{$row.event_title}</a></td>
           <td>
                {$row.event_start_date|crmDate}
                {if $row.event_end_date}
                    &nbsp; - &nbsp;
                    {* Only show end time if end date = start date *}
                    {if $row.event_end_date|date_format:"%Y%m%d" == $row.event_start_date|date_format:"%Y%m%d"}
                        {$row.event_end_date|date_format:"%I:%M %p"}
                    {else}
                        {$row.event_end_date|crmDate}
                    {/if}
                {/if}
           </td>
           <td>{$row.participant_status_id}</td>
	   {if $row.showConfirmUrl}
           <td><a href="{crmURL p='civicrm/event/confirm' q="reset=1&participantId=`$row.participant_id`"}">{ts}Confirm Registration{/ts}</a><br/></td>
	   {/if}
        </tr>
      {/foreach}
  </table>
  {/strip}
{else}
   <div class="messages status">
       <dl>
         <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
           <dd>
               {ts}You are not registered for any current or upcoming Events.{/ts}
           </dd>
       </dl>
   </div>
{/if}
</div>
