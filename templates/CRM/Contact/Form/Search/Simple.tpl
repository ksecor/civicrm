{include file="CRM/common/dojo.tpl"}
<table>
        <tr> 
            <td class="label" id="tt1">{$form.sort_name.label}</td>
<span dojoType="tooltip" connectId="tt1" href="{$config->resourceBase}extern/help.php?q=help" style="width: 300px;"</span>
            <td>{$form.sort_name.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.state_province.label}</td> 
            <td>{$form.state_province.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.country.label}</td> 
            <td>{$form.country.html}</td>
        </tr>
        <tr> 
            <td colspan=2>{$form.buttons.html}</td>
        </tr>

</table>

{if $rows}
<table>
<tr><th>Contact ID</th><th>Sort Name</th></tr>
{foreach from=$rows key=id item=name}
<tr><td>{$id}</td><td>{$name}</td></tr>
{/foreach}
</table>
{/if}

