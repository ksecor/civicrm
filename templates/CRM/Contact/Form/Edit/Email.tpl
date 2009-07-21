{* tpl file for building email block*}
{if !$addBlock}
<tr>
    <td>{ts}Email{/ts}
      &nbsp;&nbsp;<a id='addEmail' href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'Email', '{$className}');return false;">{ts}add{/ts}</a>
     </td> 
	 {if $className eq 'CRM_Contact_Form_Contact'}
		<td>{ts}On Hold?{/ts} {help id="id-onhold" file="CRM/Contact/Form/Contact.hlp"}</td>
		<td>{ts}Bulk Mailings?{/ts} {help id="id-bulkmail" file="CRM/Contact/Form/Contact.hlp"}</td>
		<td id="Email-Primary" class="hiddenElement">{ts}Primary?{/ts}</td>
	{/if}
</tr>
{/if}
<!-Add->
<tr id="Email_Block_{$blockId}">
    <td>{$form.email.$blockId.email.html|crmReplace:class:twenty}&nbsp;{$form.email.$blockId.location_type_id.html}</td>
	<td align="center">{$form.email.$blockId.on_hold.html}</td>
	<td align="center" id="Email-Bulkmail-html">{$form.email.$blockId.is_bulkmail.html}</td>
	<td align="center" id="Email-Primary-html" {if $blockId eq 1}class="hiddenElement"{/if}>{$form.email.$blockId.is_primary.html}</td>
  {if $blockId gt 1}
    <td><a href="#" title="{ts}Delete Email Block{/ts}" onClick="removeBlock( 'Email', '{$blockId}' ); return false;">{ts}delete{/ts}</a></td>
  {/if}
</tr>
<!-Add->
