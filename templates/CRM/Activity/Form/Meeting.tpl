{* this template is used for adding/editing meeting  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Meeting{/ts}{else}{ts}Edit Meeting{/ts}{/if}</legend>
  <dl>
	<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
	<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
	<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} &nbsp; {$form.duration_minutes.label} &nbsp; {$form.duration_minutes.html}</dd>
        <dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
        <dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
