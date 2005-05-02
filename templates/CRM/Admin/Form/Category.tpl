{* this template is used for adding/editing category  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $action eq 1}New{else}Edit{/if} Category</legend>
	<dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
	<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
    <div class="spacer"></div>
    </dl>
</fieldset>
</div>
</form>
