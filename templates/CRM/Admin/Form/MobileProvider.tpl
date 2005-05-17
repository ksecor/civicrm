{* this template is used for adding/editing Mobile Provider  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Mobile Provider{/ts}{else}{ts}Edit Mobile Provider{/ts}{/if}</legend>
    <dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
</form>
