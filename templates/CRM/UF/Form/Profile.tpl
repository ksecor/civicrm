{if ! empty( $fields )}
<div id="crm-container"> {* wrap in crm-container div so crm styles are used *}

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
</div> {* end crm-container div *}
{/if} {* fields array is not empty *}