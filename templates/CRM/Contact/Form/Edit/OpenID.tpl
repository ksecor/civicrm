{* tpl for building IM related fields *}
{if !$addBlock}
<tr>
    <td><strong>{ts}Open ID{/ts}</strong>
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'OpenId', countOpenId(cj('#openidBlockCount').val()), '{$contactType}');return false;">add</a>
    </td>
    <td colspan="2"></td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{literal}
<script type="text/javascript">
function countOpenId(count){
    var locationId = parseInt(count) + 1;
    cj('#openidBlockCount').val(locationId);
    return locationId;
}
</script>
{/literal}
{/if}
<tr id="OpenId_Block_{$locationId}">
     <td>{$form.openid.$locationId.openid.html|crmReplace:class:twenty}</td>
     <td colspan="2"></td>
     <td align="center">{$form.openid.$locationId.is_primary.html}
   {if $locationId gt 1}
   &nbsp;&nbsp;<a href="#" title={ts}Remove{/ts} onClick='cj("tr#OpenId_Block_{$locationId}").remove();return false;'>{ts}remove{/ts}</a>
   {/if}
</td>
</tr>