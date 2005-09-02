{if ! empty( $fields )}
<p>If you think people may be worried about you (and believe me, they
/ we are), tell us where you're from and where you're hiding out so
that we can know you're all right.</p> 
<p>Please be aware that all information collected here will be
accessible to the world.</p> 
    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}
        {assign var=n value=$field.name}
        <tr><td class="label">{$form.edit.$n.label}</td><td>{$form.edit.$n.html}</td></tr>
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <tr><td>&nbsp;</td><td class="description">{ts}{$field.help_post}{/ts}</td></tr>
        {/if}
    {/foreach}
    <tr><td></td><td>{$form.buttons.html}</td></tr>
    </table>
{/if} {* fields array is not empty *}