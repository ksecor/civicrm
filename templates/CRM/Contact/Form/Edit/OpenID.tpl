{* tpl for building IM related fields *}
<tr>
    <td><strong>{ts}Open ID{/ts}</strong>
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="addRemoveOpenIdBlock();">add</a>
    </td>
    <td colspan="2"></td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{section name=loop start=1 loop=`$openidCount+1`} 
{assign var=key value=$smarty.section.loop.index}
<tr id="openId-{$key}">
     <td>{$form.openid.$key.openid.html|crmReplace:class:twenty}</td>
     <td colspan="2"></td>
     <td align="center">{$form.openid.$key.is_primary.html}</td>
</tr>
{/section}
{literal}
<script type="text/javascript">
cj('#openId-2').hide();
function addRemoveOpenIdBlock( ) {
    cj('#openId-2').toggle();
}
</script>
{/literal}