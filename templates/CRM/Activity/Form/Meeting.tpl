{* this template is used for adding/editing meeting  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Meeting{/ts}{elseif $action eq 2}{ts}Edit Meeting{/ts}{else}{ts}View Meeting{/ts}{/if}</legend>
  <dl>
	<dt>Name</dt><dd>{$displayName}</dd>
	<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
        <dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
	<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
	<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} Hrs &nbsp; {$form.duration_minutes.html} Min &nbsp;</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
    <dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}</dd>
    {if $status}
        <dt></dt>
        <dd><a href="{crmURL p='civicrm/contact/view/meeting' q="action=add&pid=`$pid`"}">&raquo; Schedule Follow Up Meeting </a><dd>
    {/if}
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
