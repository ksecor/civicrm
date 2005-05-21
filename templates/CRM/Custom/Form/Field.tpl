<div class="form-item">
<fieldset><legend>{ts}Custom Data Field{/ts}</legend>
    {strip}
    <dt>{$form.label.label}</dt><dd> {$form.label.html}</dd>
    <dt>{$form.data_type.label}</dt><dd>{$form.data_type.html} &nbsp;  &nbsp; <span class=label>Field type</span></dd>
    <dt>{$form.mask.label}</dt><dd>{$form.mask.html}</dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>{$form.default_value.label}</dt><dd>{$form.default_value.html}</dd>
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
