<table class="selector">
  <tr class="columnheader">
    <th>{ts}Scheduled Amount{/ts}</th>
    <th>{ts}Scheduled Date{/ts}</th>
    <th>{ts}Paid Amount{/ts}</th>
    <th>{ts}Paid Date{/ts}</th>
    <th>{ts}Last Reminder{/ts}</th>
    <th>{ts}Reminders Sent{/ts}</th>
    <th>{ts}Status{/ts}</th>
  </tr>

  {foreach from=$rows item=row}
   <tr class="{cycle values="odd-row,even-row"}">
    <td>{$row.scheduled_amount|crmMoney}</td>	
    <td>{$row.scheduled_date|truncate:10:''|crmDate}</td>
    <td>{$row.total_amount|crmMoney}</td>	
    <td>{$row.receive_date|truncate:10:''|crmDate}</td>
    <td>{$row.reminder_date|truncate:10:''|crmDate}</td>
    <td>{$row.reminder_count}</td>
    <td>{$row.status}</td>	
   </tr>
  {/foreach}
</table>
