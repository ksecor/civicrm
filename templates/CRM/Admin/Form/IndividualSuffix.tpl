{* this template is used for adding/editing individual suffix  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Individual Suffix{/ts}{else}{ts}Edit Individual Suffix{/ts}{/if}</legend>
  <dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
	<dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
