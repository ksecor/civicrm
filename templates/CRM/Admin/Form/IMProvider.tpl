{* this template is used for adding/editing IM Provider  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}NewIM Provider{/ts}{else}{ts}Edit IM Provider{/ts}{/if}</legend>
    <dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
</form>
