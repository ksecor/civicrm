{* this template is used for adding/editing calls  *}
<div class="form-item">
  <fieldset>
   <legend>
    {if $action eq 1}
        {if $log}{ts}Log a Phone Call{/ts}{else}{ts}Schedule a Phone Call{/ts}{/if}
    {elseif $action eq 2}{ts}Edit Scheduled Call{/ts}
    {else}{ts}View Scheduled Call{/ts}{/if}
  </legend>
  <dl>
        {if $action eq 1}
          <dt>{ts}With Contact{/ts}</dt><dd>{$displayName}</dd>
        {else}
  	  <dt>{ts}With Contact{/ts}</dt><dd>{$targetName}</dd>
	  <dt>{ts}Created By{/ts}</dt><dd>{$sourceName}</dd>
        {/if}
	<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
	<dt>{$form.phone_id.label}</dt><dd>{$form.phone_id.html}{if $action neq 4}&nbsp;{$form.phone_number.label}&nbsp;{/if}{$form.phone_number.html}</dd>
	<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
	<dt>{ts}Duration{/ts}</dt><dd>{$form.duration_hours.html} {ts}Hrs{/ts} &nbsp; {$form.duration_minutes.html} {ts}Min{/ts} &nbsp;</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
    {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Call will be moved to Activity History when status is 'Completed'.{/ts}</dd>
    {/if}
	<dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}</dd>

    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>

  </dl>
</fieldset>
</div>
