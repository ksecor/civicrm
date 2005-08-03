{* this template is used for adding/editing meeting  *}
<div class="form-item">
<fieldset>
   <legend>
    {if $action eq 1}
    {ts}Schedule a Activities{/ts}
    {elseif $action eq 2}{ts}Edit Scheduled Acticity{/ts}
    {elseif $action eq 8}{ts}Delete Activity{/ts}
    {else}{ts}View Scheduled Activity{/ts}{/if}
  </legend>
  <dl>
     {if $action eq 1 or $action eq 2  or $action eq 4}
        {if $action eq 1}
          <dt>{ts}With Contact{/ts}</dt><dd>{$displayName}&nbsp;</dd>
        {else}
  	  <dt>{ts}With Contact{/ts}</dt><dd>{$targetName}&nbsp;</dd>
	  <dt>{ts}Created By{/ts}</dt><dd>{$sourceName}&nbsp;</dd>
        {/if}
	<dt>{$form.activity_type.label} <dd>{$form.activity_type.html}{$form.description.html|crmReplace:class:small}</dd></dt>
	<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
    <dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
    {if $action eq 4}
        <dt>{$form.scheduled_date_time.label}</dt><dd>{$scheduled_date_time|crmDate}</dd>
    {else}
        <dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
    {/if}
	<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} {ts}Hrs{/ts} &nbsp; {$form.duration_minutes.html} {ts}Min{/ts} &nbsp;</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
    {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Activity will be moved to Activity History when status is 'Completed'.{/ts}</dd>
    {/if}

    <dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}</dd>
   {/if}
    {if $action eq 8 }
    <div class="status">{ts 1=$delName}Are you sure you want to delete "%1"?{/ts}</div>
    {/if}
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
