<div id="name" class="data-group form-item">
 	<label>{$displayName}</label>
</div>

<div id="groupContact">
 <p>
    <div class="form-item">
      {if $groupCount > 0 }  	
        {if $groupIn }
        <table>
         <tr><td>Current Group Memberships <br>{$displayName} is currently a member of these groups:</td></tr>
         <tr class="columnheader"><th>Group</th><th>Category</th><th>Status</th><th>Date Added</th><th>&nbsp;</th></tr>
         {foreach from=$groupIn item=row}
           <tr class="{cycle values="odd-row,even-row"}">
            <td>{$row.name}</td>
	    <td>&nbsp;</td>	
	    <td>Added (by {$row.in_method})</td> 
            <td>{$row.in_date|date_format:"%B %e, %Y"}</td>
	    <td><a href="#">[ Remove ]</a></td>   
           </tr>
         {/foreach}
       </table>
	{/if}
	<br>
	{include file="CRM/GroupContact/Form/GroupContact.tpl"}	

	<br>
       {if $groupPending }
        <table>
         <tr><td>Pending  Memberships <br>Membership in these group(s) is pending confirmation by this contact:</td></tr>
         <tr class="columnheader"><th>Group</th><th>Category</th><th>Status</th><th>Date Pending</th><th>&nbsp;</th></tr>
         {foreach from=$groupPending item=row}
           <tr class="{cycle values="odd-row,even-row"}">
            <td> {$row.name}</td>
	    <td>&nbsp;</td>	
	    <td>Pending (by {$row.pending_method})</td> 
            <td>{$row.pending_date|date_format:"%B %e, %Y"}</td>
	    <td><a href="#">[ Remove ]</a></td>   
           </tr>
         {/foreach}
        </table>
       {/if}
       <br>
       <br>
       {if $groupOut }
       <table>
         <tr><td>Past Memberships <br>{$displayName} is no longer a member of these groups:</td></tr>
         <tr class="columnheader"><th>Group</th><th>Category</th><th>Status</th><th>Date Removed</th><th>&nbsp;</th></tr>
         {foreach from=$groupOut item=row}
           <tr class="{cycle values="odd-row,even-row"}">
            <td> {$row.name}</td>
	    <td>&nbsp;</td>	
	    <td>Removed (by {$row.out_method})</td> 
            <td>{$row.out_date|date_format:"%B %e, %Y"}</td>
	    <td><a href="#">[ Rejoin Group ]</a></td>   
           </tr>
         {/foreach}
       </table>
       {/if}

     {else}
       <div class="message status">	
         <img src="crm/i/Inform.gif" alt="status"> &nbsp; This contact does not belong to any groups.
       </div>	
     {/if}
    </div>
  </p>
</div>
