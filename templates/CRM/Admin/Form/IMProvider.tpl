{* this template is used for adding/editing IM Provider  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New IM Service Provider{/ts}{else}{ts}Edit IM Service Provider{/ts}{/if}</legend>
    <dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
