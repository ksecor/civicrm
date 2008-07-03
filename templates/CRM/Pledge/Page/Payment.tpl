<table class="selector">
  <tr class="columnheader">
    <th>{ts}Scheduled Amount{/ts}</th>
    <th>{ts}Scheduled Date{/ts}</th>
    <th>{ts}Paid Amount{/ts}</th>
    <th>{ts}Paid Date{/ts}</th>
    <th>{ts}Last Reminder{/ts}</th>
    <th>{ts}Reminders Sent{/ts}</th>
    <th>{ts}Status{/ts}</th>
    <th></th>
  </tr>

  {foreach from=$rows item=row}
   <tr class="{cycle values="odd-row,even-row"} {if $row.status eq 'Pending' or $row.status eq 'Overdue' } disabled{/if}">
    <td>{$row.scheduled_amount|crmMoney}</td>	
    <td>{$row.scheduled_date|truncate:10:''|crmDate}</td>
    <td>{$row.total_amount|crmMoney}</td>	
    <td>{$row.receive_date|truncate:10:''|crmDate}</td>
    <td>{$row.reminder_date|truncate:10:''|crmDate}</td>
    <td>{$row.reminder_count}</td>
    <td>{$row.status}</td>	
    <td>
      {if $permission EQ 'edit' and ($row.status eq 'Pending' or $row.status eq 'Overdue') }
        {capture assign=newContribURL}{crmURL p="civicrm/contact/view/contribution" q="reset=1&action=add&cid=`$contactId`&context=contribution&pledgeId=`$pledgeId`"}{/capture}
        {ts 1=$newContribURL}<a href='%1'>Record Payment (Check, Cash, EFT ...)</a>{/ts}
        {if $newCredit}
	   <br/>  
          {capture assign=newCreditURL}{crmURL p="civicrm/contact/view/contribution" q="reset=1&action=add&cid=`$contactId`&pledgeId=`$pledgeId`&context=contribution&mode=live"}{/capture}
          {ts 1=$newCreditURL}<a href='%1'>Submit Credit Card Payment</a>{/ts}
        {/if}
    {/if}
    </td>
   </tr>
  {/foreach}
</table>
