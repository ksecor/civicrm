{* tpl for building IM related fields *}
{assign var="locationId" value=$blockCount}
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
<tr id="OpenID_Block_{$locationId}">
     <td>{$form.openid.$locationId.openid.html|crmReplace:class:twenty}</td>
     <td colspan="2"></td>
     <td align="center">{$form.openid.$locationId.is_primary.html}</td>
   {if $locationId gt 1}
    <td><a href="#" title={ts}Remove{/ts} onClick='cj("tr#OpenID_Block_{$locationId}").remove();return false;'>{ts}remove{/ts}</a></td>
   {/if}
</tr>
<!-Add->