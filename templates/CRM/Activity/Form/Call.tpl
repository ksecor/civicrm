{* this template is used for adding/editing calls  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Call{/ts}{else}{ts}Edit Call{/ts}{/if}</legend>
  <dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
	<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
	<dt>{$form.phone_id.label}</dt><dd>{$form.phone_id.html}&nbsp;{$form.phone_number.label}&nbsp;{$form.phone_number.html}</dd>
	<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
	<dt>Duration</dt><dd>{$form.duration_hours.html}Hr &nbsp;{$form.duration_minutes.html}Min</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
	<dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}</dd>

    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
