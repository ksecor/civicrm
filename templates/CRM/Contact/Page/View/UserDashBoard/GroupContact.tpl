<div id="groupContact">
<div class="view-content"> 
{if $groupCount eq 0 }
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>{ts}You are not currently subscribed to any Groups.{/ts}</dd>
    </dl>
  </div>
 {/if}

  	{if $groupIn }
        
	<div class="form-item">
	<div>
	{strip}
	<table class="selector">
        <tr class="columnheader">
		<th>{ts}Group{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th>{ts}Date Added{/ts}</th>
		<th></th>
	</tr>
       	{foreach from=$groupIn item=row}
        <tr class="{cycle values="odd-row,even-row"}">
        	<td><strong><a href="{crmURL p='civicrm/group/search' q="reset=1&force=1&context=smog&gid=`$row.group_id`"}">{$row.title}</a></strong></td>
	    	<td>{ts 1=$row.in_method}Added (by %1){/ts}</td> 
            <td>{$row.in_date|crmDate}</td>
            <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&action=delete&st=o"}" onclick ="return confirm('{ts 1=$row.title}Are you sure you want to unsubscribe from %1?{/ts}');">[ {ts}Unsubscribe{/ts} ]</a></td> 
	    </tr>
     	{/foreach}
        </table>
	{/strip}
	</div>
	</div>
	{/if}
    
	{* Include 'Join a Group' form *}
       
    {include file="CRM/Contact/Form/GroupContact.tpl"}	
   
    
    {if $groupPending }
	<div class="form-item">
        <div class="label status-pending">{ts}Pending Subscriptions{/ts}</div> 
        <div class="description">{ts}Your subscription to these group(s) is pending confirmation.{/ts}</div>
		
	<div>
	{strip}
	<table class="selector">
	<tr class="columnheader">
		<th>{ts}Group{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th>{ts}Date Pending{/ts}</th>
		<th></th>
	</tr>
   	{foreach from=$groupPending item=row}
        <tr class="{cycle values="odd-row,even-row"}">
            <td><strong>{$row.title}</strong></td>
            <td>{ts 1=$row.pending_method}Pending (by %1){/ts}</td> 
            <td>{$row.pending_date|crmDate}</td>
            <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&action=delete&st=o"}" onclick ="return confirm('{ts 1=$row.title}Are you sure you want to remove from %1?{/ts}');">[ {ts}Confirm{/ts} ]</a></td> 
    	</tr>
     	{/foreach}
	</table>
	{/strip}

	</div>
	</div>
	{/if}

{*the following <div> is for temporary purpose...for formatting purpose....need to be fixed*}
	<div class="form-item">
     	</div>
       	
	{if $groupOut }
	<div class="form-item">
	<div class="label status-removed">{ts}Unsubscribed Groups{/ts}</div>
    <div class="description">{ts}You are no longer subscribed to these group(s). Click Rejoin Group if you want to re-subscribe.{/ts}</div>
	
	<div>
        {strip}
	<table class="selector">
	<tr class="columnheader">
		<th>{ts}Group{/ts}</th>
		<th>{ts}Status{/ts}</th>
        <th>{ts}Date Added{/ts}</th>
		<th>{ts}Date Removed{/ts}</th>
		<th></th>
	</tr>
        {foreach from=$groupOut item=row}
        <tr class="{cycle values="odd-row,even-row"}">
            <td><strong>{$row.title}</strong></td>
	    	<td class="status-removed">{ts 1=$row.out_method}Removed (by %1){/ts}</td> 
            <td>{$row.date_added|crmDate}</td>
            <td>{$row.out_date|crmDate}</td>
	        <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&action=delete&st=i"}" onclick ="return confirm('{ts 1=$row.title}Are you sure you want to add back into %1?{/ts}');">[ {ts}Rejoin Group{/ts} ]</a></td>
    	</tr>
     	{/foreach}
   	</table>
	{/strip}
	</div>
	</div>
	{/if}
</div>
</div>
