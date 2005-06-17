{* this template is used for adding/editing calls  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Call{/ts}{else}{ts}Edit Call{/ts}{/if}</legend>
  <dl>
	
	<dt>{$form.phonecall_date.label}</dt><dd>{$form.phonecall_date.html}</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
	<dt>{$form.call_log.label}</dt><dd>{$form.call_log.html}</dd>
	<dt>{$form.priority.label}</dt><dd>{$form.priority.html}</dd>

	


	<dt>{$form.next_phonecall_datetime.label}</dt><dd>{$form.next_phonecall_datetime.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
