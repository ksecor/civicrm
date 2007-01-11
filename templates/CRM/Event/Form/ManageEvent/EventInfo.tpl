{* this template is used for adding event  *}
{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset><legend>{ts}Event Information{/ts}</legend>
    <dl class="html-adjust">
     	<dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
        <dt>{$form.event_type_id.label}</dt><dd>{$form.event_type_id.html}</dd>
    	<dt>{$form.summary.label}</dt><dd>{$form.summary.html}</dd>
    	<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
    	<dt>{$form.is_public.label}</dt><dd>{$form.is_public.html}</dd>
        <dt>{$form.start_date.label}</dt><dd>{$form.start_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=3 doTime=1 trigger=trigger_event_1}</dd>
              <dd class="description html-adjust"></dd>
        <dt>{$form.end_date.label}</dt><dd>{$form.end_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=end_date offset=3 doTime=1 trigger=trigger_event_1}</dd>
              <dd class="description html-adjust"></dd>
        <dt>{$form.max_participants.label}</dt><dd>{$form.max_participants.html|crmReplace:class:four}</dd>
       <dt>{$form.event_full_text.label}</dt><dd>{$form.event_full_text.html}</dd>
        <dt>{$form.is_map.label}</dt><dd>{$form.is_map.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
        <dt></dt>
        <dd class="description">
	    {if $action eq 4}
         {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
        {else}
          {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
        {/if}
        </dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>

<script type="text/javascript">
{literal}
function reload(refresh)
{
    var eventId = document.getElementById("event_type_id");
    var url = "{/literal}{$refreshURL}{literal}"
    var post = url + "&subType=" + eventId.value;
    if( refresh ) {
        window.location= post; 
    }
}
{/literal}
</script>
