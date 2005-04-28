<div id="name" class="data-group form-item">
 	<label>{$displayName}</label>
</div>

  {if $action eq 4}
  {if $relationship}
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
{/if}
{elseif $action eq 1 or $action eq 2}
{include file="CRM/Contact/Form/Relationship.tpl"}	
{/if}

{if $relationship}
<div id="relationships">
<div class="form-item">
    {strip}
	{if $relationship}
	<div>
    	 <a href="{crmURL p='civicrm/contact/view/rel' q="cid=`$contactId`&action=add"}">New Relationship</a>
       </div>
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
	  {assign var = "rtype" value = "" }
      {if $rel.contact_b > 0 }
	    {assign var = "rtype" value = "b_a" }
	  {else}
	    {assign var = "rtype" value = "a_b" }
	  {/if}
        <tr class="{cycle values="odd-row,even-row"}">
          	<td> {$rel.relation}</td>
	    	<td> <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$rel.cid`"}">{$rel.name}</a></td>
	 	<td>{$rel.email}</td>
	 	<td>{$rel.phone}</td>
	 	<td>{$rel.city}</td>
	 	<td>{$rel.state}</td>
         	<td><a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=view&rtype=$rtype"}">View</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=update&rtype=$rtype"}">Edit</a>
		</td>
	</tr>
       	{/foreach}
       	</table>
       	</p>
	{else}
	<div class="status">
        <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
        There are no Relationships entered for this contact.
	</div>
	<p>
        {if $action eq 16 or $action eq 4}
       	<div>
	<a href="{crmURL p='civicrm/contact/view/rel' q="cid=`$contactId`&action=add"}">New Relationship</a>
       	</div>
        {/if}
	</p>
	{/if}
	{/strip}	
       
  </div>
  </div>
{else}
   <div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
   There are no Relationships entered for this contact. You can <a href="{crmURL p='civicrm/contact/view/rel' q='action=add'}">add one</a>.
  </div>
{/if}
