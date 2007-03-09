<div class="view-content">
{if $event_rows}
  {strip}
  <div><label>{ts}Registered Events{/ts}</label></div>
  <table class="selector">
    <tr class="columnheader">
      <th>{ts}Event{/ts}</th>
      <th>{ts}Event Date{/ts}</th>
      <th>{ts}Status{/ts}</th>
      <th></th>
    </tr>
     {counter start=0 skip=1 print=false}
     {foreach from=$event_rows item=row}
        <tr id='rowid{$row.participant_id}' class="{cycle values="odd-row,even-row"}{if $row.status eq Cancelled} disabled{/if}">
           <td><a href="{crmURL p='civicrm/event/info' q="reset=1&id=`$row.event_id`"}">{$row.event_title}</a></td>
           <td>{$row.start_date|crmDate}<br/> {$row.end_date|crmDate}</td>
           <td>{$row.status}<br/>{$row.modified_date|crmDate}</td>
           <td>{$row.action}</td>
        </tr>
      {/foreach}
  </table>
  {/strip}
{else}
   <div class="messages status">
       <dl>
         <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
           <dd>
               {ts}You are currently not registered for any current or upcoming Events.{/ts}
           </dd>
       </dl>
   </div>
{/if}
</div>
