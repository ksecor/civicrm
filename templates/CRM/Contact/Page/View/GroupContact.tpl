<div id="name" class="data-group form-item">
	<label>{$displayName}</label>
</div>

<div id="groupContact">

  	{if $groupIn }
        
	<div class="form-item">
        <div class="font-size12pt label">Current Group Memberships</div> 
	<div><label>{$displayName} is currently a member of these groups:</label></div>
	
	<div>
	{strip}
	<table>
        <tr class="columnheader">
		<th>Group</th>
		<th>Category</th>
		<th>Status</th>
		<th>Date Added</th>
		<th></th>
	</tr>
       	{foreach from=$groupIn item=row}
        <tr class="{cycle values="odd-row,even-row"}">
        	<td>{$row.name}</td>
	    	<td></td>	
	    	<td>Added (by {$row.in_method})</td> 
            	<td>{$row.in_date|date_format:"%B %e, %Y"}</td>
	        <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&op=del&st=o"}" onclick ="return confirm('Are you sure you want to remove {$displayName} from {$row.name}?');">[ Remove ]</a></td> 
	</tr>
     	{/foreach}
        </table>
	{/strip}
	</div>
	</div>
	{/if}
	
	{include file="CRM/GroupContact/Form/GroupContact.tpl"}	

       	{if $groupPending }
	<div class="form-item">
        <div class="font-size12pt label">Pending  Memberships </div> 
	<div><label>Membership in these group(s) is pending confirmation by this contact:</label></div>
		
	<div>
	{strip}
	<table>
	<tr class="columnheader">
		<th>Group</th>
		<th>Category</th>
		<th>Status</th>
		<th>Date Pending</th>
		<th></th>
	</tr>
   	{foreach from=$groupPending item=row}
        <tr class="{cycle values="odd-row,even-row"}">
 		<td> {$row.name}</td>
	    	<td></td>	
	    	<td>Pending (by {$row.pending_method})</td> 
            	<td>{$row.pending_date|date_format:"%B %e, %Y"}</td>
	        <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&op=del&st=o"}" onclick ="return confirm('Are you sure you want to remove {$displayName} from {$row.name}?');">[ Remove ]</a></td> 

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
        <div class="font-size12pt label font-red">Past Memberships</div> 
	<div class="label font-red">{$displayName} is no longer a member of these groups:</div>
	
	<div>
        {strip}
	<table>
	<tr class="columnheader">
		<th>Group</th>
		<th>Category</th>
		<th>Status</th>
		<th>Date Removed</th>
		<th></th>
	</tr>
        {foreach from=$groupOut item=row}
        <tr class="{cycle values="odd-row,even-row"}">
            	<td> {$row.name}</td>
	    	<td></td>	
	    	<td>Removed (by {$row.out_method})</td> 
            	<td>{$row.out_date|date_format:"%B %e, %Y"}</td>
	        <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&op=del&st=i"}" onclick ="return confirm('Are you sure you want to add {$displayName} back into {$row.name}?');">[ Rejoin Group ]</a></td>

    	</tr>
     	{/foreach}
   	</table>
	{/strip}
	</div>
	</div>
	{/if}
 {if $groupCount eq 0 }  		
  <div class="form-item message status">	
  <img src="crm/i/Inform.gif" alt="status"> &nbsp; No current group membership.
  </div>	
  {/if}
  
</div>
