{* this template is used for adding event  *}
{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset><legend>{ts}Event Information{/ts}</legend>
      <dl>
 	<dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    	<dt>{$form.summary.label}</dt><dd>{$form.summary.html}</dd>
    	<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
    	<dt>{$form.is_public.label}</dt><dd>{$form.is_public.html}</dd>
        <dt>{$form.start_date.label}</dt><dd>{$form.start_date.html}</dd>
        {include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=1905 endDate=currentYear trigger=trigger_event_1 }
        <dt>{$form.end_date.label}</dt><dd>{$form.end_date.html}</dd>  
        {include file="CRM/common/calendar/body.tpl" dateVar=end_date startDate=1905 endDate=currentYear trigger=trigger_event_1 }
        <dt>{$form.max_participant.label}</dt><dd>{$form.max_participant.html}</dd>
        <dt>{$form.event_full_text.label}</dt><dd>{$form.event_full_text.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
      </dl> 
    <dl>   
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
