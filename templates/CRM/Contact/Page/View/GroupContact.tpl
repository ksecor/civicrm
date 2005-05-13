<div id="groupContact">
 {if $groupCount eq 0 }  		
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
      <dd>This contact does not currently belong to any groups.</dd>
    </dl>
  </div>	
 {/if}

  	{if $groupIn }
        
	<div class="form-item">
	<div><label>Current Group Memberships</label></div>
	
	<div>
	{strip}
	<table>
        <tr class="columnheader">
		<th>Group</th>
		<th>Status</th>
		<th>Date Added</th>
		<th></th>
	</tr>
       	{foreach from=$groupIn item=row}
        <tr class="{cycle values="odd-row,even-row"}">
        	<td class="label">{$row.title}</td>
	    	<td>Added (by {$row.in_method})</td> 
            <td>{$row.in_date|date_format:"%B %e, %Y"}</td>
	        <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&action=delete&st=o"}" onclick ="return confirm('Are you sure you want to remove {$displayName} from {$row.title}?');">[ Remove ]</a></td> 
        </tr>
     	{/foreach}
        </table>
	{/strip}
	</div>
	</div>
	{/if}
	
	{include file="CRM/Contact/Form/GroupContact.tpl"}	

       	{if $groupPending }
	<div class="form-item">
        <div class="label">Pending  Memberships </div> 
        <div class="description">Membership in these group(s) is pending confirmation by this contact.</div>
		
	<div>
	{strip}
	<table>
	<tr class="columnheader">
		<th>Group</th>
		<th>Status</th>
		<th>Date Pending</th>
		<th></th>
	</tr>
   	{foreach from=$groupPending item=row}
        <tr class="{cycle values="odd-row,even-row"}">
            <td class="label">{$row.title}</td>
            <td>Pending (by {$row.pending_method})</td> 
            <td>{$row.pending_date|date_format:"%B %e, %Y"}</td>
            <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&action=delete&st=o"}" onclick ="return confirm('Are you sure you want to remove {$displayName} from {$row.title}?');">[ Remove ]</a></td> 
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
	<div class="label font-red">Past Memberships</div>
    <div class="description">{$displayName} is no longer a member of these group(s).</div>
	
	<div>
        {strip}
	<table>
	<tr class="columnheader">
		<th>Group</th>
		<th>Status</th>
        <th>Date Added</th>
		<th>Date Removed</th>
		<th></th>
	</tr>
        {foreach from=$groupOut item=row}
        <tr class="{cycle values="odd-row,even-row"}">
            <td class="label">{$row.title}</td>
	    	<td>Removed (by {$row.out_method})</td> 
            <td>{$row.in_date|date_format:"%B %e, %Y"}</td>
            <td>{$row.out_date|date_format:"%B %e, %Y"}</td>
	        <td><a href="{crmURL p='civicrm/contact/view/group' q="gcid=`$row.id`&action=delete&st=i"}" onclick ="return confirm('Are you sure you want to add {$displayName} back into {$row.title}?');">[ Rejoin Group ]</a></td>
    	</tr>
     	{/foreach}
   	</table>
	{/strip}
	</div>
	</div>
	{/if}
</div>
