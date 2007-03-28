<table>
        <tr> 
            <td class="label">{$form.sort_name.label}</td>
            <td class="nowrap">{$form.sort_name.html}{help id='sort_name'}</td>
        </tr>
        <tr> 
            <td class="label">{$form.state_province.label}</td> 
            <td class="nowrap">{$form.state_province.html}{help id='state_province'}</td>
        </tr>
        <tr> 
            <td class="label">{$form.country.label}</td> 
            <td>{$form.country.html}{help id='country'}</td>
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

