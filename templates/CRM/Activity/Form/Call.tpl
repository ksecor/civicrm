{* this template is used for adding/editing calls  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Call{/ts}{else}{ts}Edit Call{/ts}{/if}</legend>
  <dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
	<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
