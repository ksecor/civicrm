{* tpl for building phone related fields*}
<tr>
        <td><strong>{ts}Phone{/ts}</strong>
             &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="addRemovePhoneBlock();">add</a>
        </td>
        <td colspan="2"></td>
        <td>{ts}Primary?{/ts}</td>
</tr>
{section name=loop start=1 loop=`$phoneCount+1`} 
{assign var=key value=$smarty.section.loop.index}
<tr id="phone-{$key}">
     <td>{$form.phone.$key.phone.html|crmReplace:class:twenty}
         &nbsp;{$form.phone.$key.location_id.html}</td>
     <td colspan="2">{$form.phone.$key.phone_type_id.html}</td>
     <td align="center">{$form.phone.$key.is_primary.html}</td>
</tr>
{/section}
{literal}
<script type="text/javascript">
cj('#phone-2').hide();
function addRemovePhoneBlock( ) {
    cj('#phone-2').toggle();
}
</script>
{/literal}