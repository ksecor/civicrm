{if $action eq 1 or $action eq 2 or $action eq 4}
<form {$form.attributes}>
<div class="form-item">
<fieldset>
    {strip}
    <dt>{$form.label.label}</dt><dd> {$form.label.html}</dd>
    <dt>{$form.data_type.label}</dt><dd>{$form.data_type.html}</dd>
    <dt>{$form.html_type.label}</dt><dd>{$form.html_type.html}</dd>
    <dt>{$form.mask.label}</dt><dd>{$form.mask.html}</dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>{$form.default_value.label}</dt><dd>{$form.default_value.html}</dd>
    <dt>{$form.javascript.label}</dt><dd>{$form.javascript.html}</dd>
    <dt>{$form.fattributes.label}</dt><dd>{$form.fattributes.html}</dd>
    <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>{$form.is_required.label}</dt><dd>{$form.is_required.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    
    {if $action ne 4}
    <div id="crm-submit-buttons">
    <dt></dt><dd>{$form.buttons.html}</dd>
    </div>
    {else}
    <dt></dt><dd>{$form.done.html}</dd>
    {/if} {* $action ne view *}
    {/strip}
</div>
</fieldset>
</form>
{/if}
