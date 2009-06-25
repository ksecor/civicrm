{* tpl for building IM related fields*}
<tr>
    <td><strong>{ts}Instant Messenger{/ts}</strong>
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="addRemoveIMBlock();">add</a>
    </td>
    <td colspan="2"></td>
    <td>{ts}Primary?{/ts}</td>
</tr>
{section name=loop start=1 loop=`$imCount+1`} 
{assign var=key value=$smarty.section.loop.index}
<tr id="im-{$key}">
     <td>{$form.im.$key.name.html|crmReplace:class:twenty}
            &nbsp;{$form.im.$key.location_id.html}</td>
    <td colspan="2">{$form.im.$key.provider_id.html}</td>
    <td align="center">{$form.im.$key.is_primary.html}</td>
</tr>
{/section}
{literal}
<script type="text/javascript">
cj('#im-2').hide();
function addRemoveIMBlock( ) {
    cj('#im-2').toggle();
}
</script>
{/literal}