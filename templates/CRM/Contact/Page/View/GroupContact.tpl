<div id="name" class="data-group form-item">
 	<label>{$displayName}</label>
</div>
{if $op eq 'view'}
<div class="form-item">
	<fieldset><legend>View Relationship</legend>

    	<div>
	<span class="horizontal-position">
	<span class="labels"><label>Relationship :</label></span> 
	<span class="fields">{$relationship_name}</span>
	</span>
	</div>
	<div>
    	<span class="horizontal-position">
	<span class="labels"><label>Contact :</label></span> 
	<span class="fields">{$relationship_contact_name}</span>
	</span>
	</div>
	<div class="spacer"></div>
	</fieldset>
</div>    
{elseif $op eq 'add' or $op eq 'edit'}
{include file="CRM/GroupContact/Form/GroupContact.tpl"}	
{/if}


<div id="groupContact">
 <p>
    <div class="form-item">
    {if $groupCount > 0 }  	
       <table>
       <tr class="columnheader"><th>Group Listings</th><th>In Date</th><th>Out Date</th><th></th></tr>
       {foreach from=$groupContact item=row}
         <tr class="{cycle values="odd-row,even-row"}">
            <td> {$row.name}</td>
            <td>{$row.in_date|date_format:"%B %e, %Y"}</td>
            <td>{$row.out_date|date_format:"%B %e, %Y"}</td>
	    <td><a href="#">View</a></td>   
         </tr>
       {/foreach}
       </table>
     {else}
     <div class="message status">	
     <img src="crm/i/Inform.gif" alt="status"> &nbsp;
      This contact does not belong to any groups.
     </div>	
     {/if}
    </div>
 </p>
  <span class="float-right">
    <a href="{$config->httpBase}contact/view/group&cid={$contactId}&op=add">Manage Groups</a>
  </span>

</div>
