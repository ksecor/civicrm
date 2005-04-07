<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
    </p>
</div>

{if $op eq 'view'}
    <p>
    <fieldset><legend>View Relationship</legend>
    <div class="form-item">
    <b>Relationship : {$relationship_name}<br>
    Contact : {$relationship_contact_name}</b>
    </div>
    </fieldset>
    </p>
{elseif $op eq 'add' or $op eq 'edit'}
    {include file="CRM/Relationship/Form/Relationship.tpl"}	
{/if}

<div id="relationships">
 <p>
    <div class="form-item">
       <table>
	<tr class="columnheader"><td>Relationship</td><td>Contact</td><td>Email</td><td>Phone</td><td>City</td><td>State/Prov</td><td>&nbsp;</td></tr>
       {foreach from=$relationship item=rel}
        <tr>
          <td> {$rel.relation}</td>
	 <td>{$rel.name}</td>
	 <td>{$rel.email}</td>
	 <td>{$rel.phone}</td>
	 <td>{$rel.city}</td>
	 <td>{$rel.state}</td>
         <td><a href="{$config->httpBase}contact/view/rel&cid={$contactId}&rid={$rel.id}&op=view">View</a> | <a href="{$config->httpBase}contact/view/rel&cid={$contactId}&rid={$rel.id}&op=edit">Edit</a></td>
         </tr>
       {/foreach}
       </table>
       <br />
       <div class="action-link">
         <a href="{$config->httpBase}contact/view/rel&cid={$contactId}&op=add">Create Relationship</a>
       </div>
    </div>
 </p>
</div>
