{* tpl for building IM related fields*}
{if !$addBlock}
<tr>
    <td>{ts}Instant Messenger{/ts}
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'IM', '{$className}');return false;">add</a>
    </td>
    <td colspan="3"></td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{/if}
<!-Add->
<tr id="IM_Block_{$blockId}">
    <td>{$form.im.$blockId.name.html|crmReplace:class:twenty}&nbsp;{$form.im.$blockId.location_type_id.html}</td>
    <td colspan="3">{$form.im.$blockId.provider_id.html}</td>
    <td align="center">{$form.im.$blockId.is_primary.html}</td>
    {if $blockId gt 1}
     <td><a href="#" title={ts}Remove{/ts} onClick="removeBlock('IM','{$blockId}'); return false;">{ts}remove{/ts}</a></td>
    {/if}
    </td>
</tr>
<!-Add->