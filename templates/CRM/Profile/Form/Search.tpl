{if ! empty( $fields )}
<p>
    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}
        {assign var=n value=$field.name}
        <tr>
            <td class="label">{$form.$n.label}</td>
            <td class="description">{$form.$n.html}</td>
        </tr>
    {/foreach}
    <tr><td></td><td>{$form.buttons.html}</td></tr>
    </table>
</p>
{/if}
