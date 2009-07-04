{* tpl file for building email block*}
{if !$addBlock}
<tr>
    <td><strong>{ts}Email{/ts}</strong>
      &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'Email', '{$contactType}');return false;">{ts}add{/ts}</a>
     </td> 
    <td>{ts}On Hold?{/ts} {help id="id-hold"}</td>
    <td>{ts}Bulk Mailings?{/ts} {help id="id-bulk"}</td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{/if}
<!-Add->
<tr id="Email_Block_{$blockId}">
    <td>{$form.email.$blockId.email.html|crmReplace:class:twenty}&nbsp;{$form.email.$blockId.location_type_id.html}</td>
    <td align="center">{$form.email.$blockId.on_hold.html}</td>
    <td align="center">{$form.email.$blockId.is_bulkmail.html}</td>
    <td align="center">{$form.email.$blockId.is_primary.$blockId.html}</td>
  {if $blockId gt 1}
    <td><a href="#" title={ts}Remove{/ts} onClick="removeBlock( 'Email', '{$blockId}' ); return false;">{ts}remove{/ts}</a></td>
  {/if}
</tr>
<!-Add->

