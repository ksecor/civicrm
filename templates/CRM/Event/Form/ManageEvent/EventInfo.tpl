{* this template is used for adding event  *}
{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset><legend>{ts}Event Information{/ts}</legend>
      <dl>
     	<dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
        <dt>{$form.event_type.label}</dt><dd>{$form.event_type.html}</dd>
    	<dt>{$form.summary.label}</dt><dd>{$form.summary.html}</dd>
    	<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
    	<dt>{$form.is_public.label}</dt><dd>{$form.is_public.html}</dd>
        <dt>{$form.start_date.label}</dt><dd>{$form.start_date.html}</dd>
        <dt>&nbsp;</dt>
        <dd class="description">
               {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1}
        </dd>
        <dt>&nbsp;</dt>
        <dd class="description">
{include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=3 doTime=1 trigger=trigger_event_1}
        </dd>
        <dt>{$form.end_date.label}</dt><dd>{$form.end_date.html}</dd>  
        <dt>&nbsp;</dt>
        <dd class="description">
               {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1}
        </dd>
        <dt>&nbsp;</dt>
        <dd class="description">
{include file="CRM/common/calendar/body.tpl" dateVar=end_date offset=3 doTime=1 trigger=trigger_event_1}
        </dd>
        <dt>{$form.max_participants.label}</dt><dd>{$form.max_participants.html}</dd>
        <dt>{$form.event_full_text.label}</dt><dd>{$form.event_full_text.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
      </dl> 
    <dl>   
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
