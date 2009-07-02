{* tpl for building IM related fields *}
{if !$addBlock}
<tr>
    <td><strong>{ts}Open ID{/ts}</strong>
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'OpenID', '{$contactType}');return false;">add</a>
    </td>
    <td colspan="2"></td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{/if}
<!-Add->
<tr id="OpenID_Block_{$blockId}">
     <td>{$form.openid.$blockId.openid.html|crmReplace:class:twenty}&nbsp;{$form.openid.$blockId.location_type_id.html}</td>
     <td colspan="2"></td>
     <td align="center">{$form.openid.$blockId.is_primary.$blockId.html}</td>
   {if $blockId gt 1}
    <td><a href="#" title={ts}Remove{/ts} onClick='cj("tr#OpenID_Block_{$blockId}").remove();return false;'>{ts}remove{/ts}</a></td>
   {/if}
</tr>
<!-Add->