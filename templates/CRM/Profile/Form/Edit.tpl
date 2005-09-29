
{if ! empty( $fields )}
    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}
        {assign var=n value=$field.name}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <tr><td> </td><td
class="description">{ts}{$field.help_post}{/ts}</td></tr>
        {/if}
    {/foreach}
    <tr><td></td><td>{$form.buttons.html}</td></tr>
    </table>
{/if} {* fields array is not empty *}
