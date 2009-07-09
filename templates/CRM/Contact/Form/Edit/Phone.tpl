{* tpl for building phone related fields*}
{if !$addBlock}
<tr>
    <td>{ts}Phone{/ts}
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'Phone', '{$contactType}');return false;">add</a>
    </td>
    <td colspan="2"></td>
    {if !$defaultLocation}
		<td>{ts}Primary?{/ts}</td>
	{/if}
</tr>
{/if}
<!-Add->
<tr id="Phone_Block_{$blockId}">
     <td>{$form.phone.$blockId.phone.html|crmReplace:class:twenty}
       {if !$defaultLocation}
	     &nbsp;{$form.phone.$blockId.location_type_id.html}
		{/if} 
	  </td>
     <td colspan="2">{$form.phone.$blockId.phone_type_id.html}</td>
	 {if !$defaultLocation}
		<td align="center">{$form.phone.$blockId.is_primary.html}</td>
	 {/if}
  {if $blockId gt 1}
   <td><a href="#" title={ts}Remove{/ts} onClick="removeBlock('Phone','{$blockId}'); return false;">{ts}remove{/ts}</a></td>
  {/if}
</tr>
<!-Add->
