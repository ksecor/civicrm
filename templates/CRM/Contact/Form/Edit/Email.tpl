{* tpl file for building email block*}
{if !$addBlock}
<tr>
    <td><strong>{ts}Email{/ts}</strong>
      &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'Email', countEmail(cj('#emailBlockCount').val()), '{$contactType}');return false;">{ts}add{/ts}</a>
     </td> 
    <td>{ts}On Hold?{/ts} {help id="id-hold"}</td>
    <td>{ts}Bulk Mailings?{/ts} {help id="id-bulk"}</td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{literal}
<script type="text/javascript">
function countEmail(count){
    var locationId = parseInt(count) + 1;
    cj('#emailBlockCount').val(locationId);
    return locationId;
}
</script>
{/literal}
{/if}
<tr id="Email_Block_{$locationId}">
    <td>{$form.email.$locationId.email.html|crmReplace:class:twenty}&nbsp;{$form.email.$locationId.location_id.html}</td>
    <td align="center">{$form.email.$locationId.on_hold.html}</td>
    <td align="center">{$form.email.$locationId.is_bulkmail.html}</td>
    <td align="center">{$form.email.$locationId.is_primary.html}
  {if $locationId gt 1}
    &nbsp;&nbsp;<a href="#" title={ts}Remove{/ts} onClick='cj("tr#Email_Block_{$locationId}").remove();return false;'>{ts}remove{/ts}</a>
  {/if}
    </td>
</tr>

