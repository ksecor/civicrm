{* tpl file for building email block*}
<tr>
    <td><strong>{ts}Email{/ts}</strong>
          &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="addRemoveEmailBlock();">add</a>
     </td> 
    <td>{ts}On Hold?{/ts} {help id="id-hold"}</td>
    <td>{ts}Bulk Mailings?{/ts} {help id="id-bulk"}</td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{section name=loop start=1 loop=`$emailCount+1`} 
{assign var=key value=$smarty.section.loop.index}
<tr id="email-{$key}">
    <td>{$form.email.$key.email.html|crmReplace:class:twenty}&nbsp;{$form.email.$key.location_id.html}</td>
    <td align="center">{$form.email.$key.on_hold.html}</td>
    <td align="center">{$form.email.$key.is_bulkmail.html}</td>
    <td align="center">{$form.email.$key.is_primary.html}</td>
</tr>
{/section}
{literal}
<script type="text/javascript">
cj('#email-2').hide();
function addRemoveEmailBlock( ) {
    cj('#email-2').toggle();
}
</script>
{/literal}