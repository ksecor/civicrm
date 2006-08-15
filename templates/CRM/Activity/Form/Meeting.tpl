{* this template is used for adding/editing meeting  *}
<div class="form-item">
<fieldset>
   <legend>
    {if $action eq 1}
    {if $log}{ts}Log a Meeting{/ts}{else}{ts}Schedule a Meeting{/ts}{/if}
    {elseif $action eq 2}{ts}Edit Scheduled Meeting{/ts}
    {elseif $action eq 8}{ts}Delete Meeting{/ts}
    {else}{ts}View Scheduled Meeting{/ts}{/if}
  </legend>
  <dl>
     {if $action eq 1 or $action eq 2  or $action eq 4}
        {if $action eq 1}
          <dt>{ts}With Contact{/ts}</dt><dd>{$displayName}&nbsp;</dd>
        {else}
  	  <dt>{ts}With Contact{/ts}</dt><dd>{$targetName}&nbsp;</dd>
	  <dt>{ts}Created By{/ts}</dt><dd>{$sourceName}&nbsp;</dd>
        {/if}
	<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
    <dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
    {if $action eq 4}
        <dt>{$form.scheduled_date_time.label}</dt><dd>{$scheduled_date_time|crmDate}</dd>
    {else}
        <dt>{$form.scheduled_date_time.label}</dt>
        <dd>{$form.scheduled_date_time.html}</dd>
        <dt>&nbsp;</dt>
        <dd class="description">
               {include file="CRM/common/calendar/desc.tpl" trigger=trigger_meeting_1}
        </dd>
        <dt>&nbsp;</dt>
        <dd class="description">
{include file="CRM/common/calendar/body.tpl" dateVar=scheduled_date_time startDate=currentYear endDate=endYear offset=3 doTime=1 trigger=trigger_meeting_1}
        </dd>
    {/if}
	<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} {ts}Hrs{/ts} &nbsp; {$form.duration_minutes.html} {ts}Min{/ts} &nbsp;</dd>
	<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
    {edit}      {*if $action neq 4*} {* Commented for crm-914*}
        <dt>&nbsp;</dt><dd class="description">{ts}Meeting will be moved to Activity History when status is 'Completed'.{/ts}</dd>
    {/edit}     {*/if*}

    <dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}&nbsp;</dd>

     <dt></dt><dd class="description">
     {if $action eq 4} 
      {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
     {else}
      {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
    {/if}
     </dd>
   {/if}
   
    {if $action eq 8 }
    <div class="status">{ts 1=$delName}Are you sure you want to delete "%1"?{/ts}</div>
    {/if}
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
