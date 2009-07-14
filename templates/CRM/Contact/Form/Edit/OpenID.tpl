{* tpl for building IM related fields *}
{if !$addBlock}
<tr>
    <td>{ts}Open ID{/ts}
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'OpenID', '{$className}');return false;">add</a>
    </td>
    <td colspan="3"></td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{/if}
<!-Add->
<tr id="OpenID_Block_{$blockId}">
     <td>{$form.openid.$blockId.openid.html|crmReplace:class:twenty}&nbsp;{$form.openid.$blockId.location_type_id.html}</td>
     <td colspan="3"></td>
     <td align="center">{$form.openid.$blockId.is_primary.html}</td>
   {if $blockId gt 1}
    <td><a href="#" title={ts}Remove{/ts} onClick="removeBlock('OpenID','{$blockId}'); return false;">{ts}remove{/ts}</a></td>
   {/if}
</tr>
<!-Add->