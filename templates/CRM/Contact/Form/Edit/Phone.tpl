{* tpl for building phone related fields*}
{if !$addBlock}
<tr>
        <td><strong>{ts}Phone{/ts}</strong>
             &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'Phone', countPhone(cj('#hidden_Phone_Count').val()), '{$contactType}');return false;">add</a>
        </td>
        <td colspan="2"></td>
        <td>{ts}Primary?{/ts}</td>
</tr>
{literal}
<script type="text/javascript">
function countPhone(count){
    var locationId = parseInt(count) + 1;
    cj('#hidden_Phone_Count').val(locationId);
    return locationId;
}
</script>
{/literal}
{/if}
<!-Add->
<tr id="Phone_Block_{$locationId}">
     <td>{$form.phone.$locationId.phone.html|crmReplace:class:twenty}
         &nbsp;{$form.phone.$locationId.location_type_id.html}</td>
     <td colspan="2">{$form.phone.$locationId.phone_type_id.html}</td>
     <td align="center">{$form.phone.$locationId.is_primary.html}</td>
  {if $locationId gt 1}
   <td><a href="#" title={ts}Remove{/ts} onClick='cj("tr#Phone_Block_{$locationId}").remove();return false;'>{ts}remove{/ts}</a></td>
  {/if}
</tr>
<!-Add->