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
{include file="CRM/Relationship/Form/Relationship.tpl"}	
{/if}


<div id="relationships">
<div class="form-item">
	<div>
        <a href="{$config->httpBase}contact/view/rel&cid={$contactId}&op=add">Create Relationship</a>
       	</div>
		
        {strip}
	{if $relationship}
	<p>
	<table>
	<tr class="columnheader">
		<th>Relationship</th>
		<th>Contact</th>
		<th>Email</th>
		<th>Phone</th>
		<th>City</th>
		<th>State/Prov</th>
		<th>&nbsp;</th>
	</tr>
       	{foreach from=$relationship item=rel}
        <tr class="{cycle values="odd-row,even-row"}">
          	<td> {$rel.relation}</td>
	    	<td><a href="{$config->httpBase}contact/view&reset=1&cid={$rel.cid}">{$rel.name}</a></td>
	 	<td>{$rel.email}</td>
	 	<td>{$rel.phone}</td>
	 	<td>{$rel.city}</td>
	 	<td>{$rel.state}</td>
         	<td><a href="{$config->httpBase}contact/view/rel&cntid={$rel.cid}&rid={$rel.id}&op=view">View</a> | <a href="{$config->httpBase}contact/view/rel&cntid={$rel.cid}&rid={$rel.id}&op=edit">Edit</a></td>
	</tr>
       	{/foreach}
       	</table>
       	</p>
	{else}
	<div class="status">
	<img src="crm/i/Inform.gif" alt="status"> &nbsp;
	There are no Relationships entered for this contact.
	</div>
	{/if}
	{/strip}	
       
	<p>
       	<div>
        <a href="{$config->httpBase}contact/view/rel&cid={$contactId}&op=add">Create Relationship</a>
       	</div>
	</p>

</div>
</div>
