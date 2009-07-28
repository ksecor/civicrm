{* This file provides the plugin for the phone block *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller*}
{* @var blockId Contains the current block id, and assigned in the CRM/Contact/Form/Location.php file *}

{if !$addBlock}
<tr>
    <td>{ts}Phone{/ts}
         &nbsp;&nbsp;<a id='addPhone' href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'Phone', '{$className}');return false;">add</a>
    </td>
    {if $className eq 'CRM_Contact_Form_Contact'}
		<td colspan="2"></td>
		<td id="Phone-Primary" class="hiddenElement">{ts}Primary?{/ts}</td>
	{/if}
</tr>
{/if}
<tr id="Phone_Block_{$blockId}">
     <td>{$form.phone.$blockId.phone.html|crmReplace:class:twenty}&nbsp;{$form.phone.$blockId.location_type_id.html}</td>
     <td colspan="2">{$form.phone.$blockId.phone_type_id.html}</td>
	 <td align="center" id="Phone-Primary-html" {if $blockId eq 1}class="hiddenElement"{/if}>{$form.phone.$blockId.is_primary.1.html}</td>
  {if $blockId gt 1}
   <td><a href="#" title="{ts}Delete Phone Block{/ts}" onClick="removeBlock('Phone','{$blockId}'); return false;">{ts}delete{/ts}</a></td>
  {/if}
</tr>