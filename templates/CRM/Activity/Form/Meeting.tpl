{* this template is used for adding/editing meeting  *}
<div class="form-item">
<fieldset>
   <legend>
    {if $action eq 1}
    {if $log}{ts}Log a Meeting{/ts}{else}{ts}Schedule a Meeting{/ts}{/if}
    {elseif $action eq 2}{ts}Edit Scheduled Meeting{/ts}
    {else}{ts}View Scheduled Meeting{/ts}{/if}
  </legend>
  <dl>
        {if $action eq 1}
          <dt>{ts}With Contact{/ts}</dt><dd>{$displayName}</dd>
        {else}
  	  <dt>{ts}With Contact{/ts}</dt><dd>{$targetName}</dd>
	  <dt>{ts}Created By{/ts}</dt><dd>{$sourceName}</dd>
        {/if}
	<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
    <dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
	<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
	<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} {ts}Hrs{/ts} &nbsp; {$form.duration_minutes.html} {ts}Min{/ts} &nbsp;</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
    {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Meeting will be moved to Activity History when status is 'Completed'.{/ts}</dd>
    {/if}
    <dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
