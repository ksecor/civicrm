{* tpl for building IM related fields*}
{if !$addBlock}
<tr>
    <td><strong>{ts}Instant Messenger{/ts}</strong>
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'IM', countIM(cj('#hidden_IM_Count').val()), '{$contactType}');return false;">add</a>
    </td>
    <td colspan="2"></td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{literal}
<script type="text/javascript">
function countIM(count){
    var locationId = parseInt(count) + 1;
    cj('#hidden_IM_Count').val(locationId);
    return locationId;
}
</script>
{/literal}
{/if}
<!-Add->
<tr id="IM_Block_{$locationId}">
     <td>{$form.im.$locationId.name.html|crmReplace:class:twenty}
            &nbsp;{$form.im.$locationId.location_type_id.html}</td>
    <td colspan="2">{$form.im.$locationId.provider_id.html}</td>
    <td align="center">{$form.im.$locationId.is_primary.html}</td>
    {if $locationId gt 1}
     <td><a href="#" title={ts}Remove{/ts} onClick='cj("tr#IM_Block_{$locationId}").remove();return false;'>{ts}remove{/ts}</a></td>
    {/if}
    </td>
</tr>
<!-Add->