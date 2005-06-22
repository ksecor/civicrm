{* this template is used for adding/editing calls  *}
<div class="form-item">
   <legend>
    {if $action eq 1}
        {if $log}{ts}Log a Phone Call{/ts}{else}{ts}Schedule A Phone Call{/ts}{/if}
    {elseif $action eq 2}{ts}Edit Scheduled Call{/ts}
    {else}{ts}View Scheduled Call{/ts}{/if}
  </legend>
  <dl>
	<dt>Name</dt><dd>{$displayName}</dd>
	<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
	<dt>{$form.phone_id.label}</dt><dd>{$form.phone_id.html}&nbsp;{$form.phone_number.label}&nbsp;{$form.phone_number.html}</dd>
	<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
	<dt>Duration</dt><dd>{$form.duration_hours.html}Hr &nbsp;{$form.duration_minutes.html}Min</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
    {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Call will be moved to Activity History when status is 'Completed'.{/ts}</dd>
    {/if}
	<dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}</dd>

    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>

  {if $status}
    <dt></dt>
     <dd>
         <a href="{crmURL p='civicrm/contact/view/call' q="action=add&pid=`$pid`"}">Follow Up Call </a>
     <dd>
  {/if}
  </dl>
</fieldset>
</div>
