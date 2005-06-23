<fieldset><legend>{ts}Selection Options{/ts}</legend>

    <div class="form-item">
        <dl>
        <dt>{$form.label.label}</dt><dd>&nbsp;{$form.label.html}</dd>
        <dt>{$form.value.label}</dt><dd>&nbsp;{$form.value.html}</dd>
        <dt>{$form.weight.label}</dt><dd>&nbsp;{$form.weight.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>&nbsp;{$form.is_active.html}</dd>
	<dt>{$form.default_value.label}</dt><dd>&nbsp;{$form.default_value.html}</dd>
	<dt>&nbsp;</dt><dd class="description">{ts}Click here to set the default value{/ts}</dd>
	</dl>
    </div>
    
    <div id="crm-submit-buttons" class="form-item">
    <dl>
    {if $action ne 4}
        <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
    {else}
        <dt>&nbsp;</dt><dd>{$form.done.html}</dd>
    {/if} {* $action ne view *}
    <dl>
    </div>

</fieldset>
