{* Step 1 of New Event Wizard, and Edit Event Info form.  *}
{include file="CRM/common/WizardHeader.tpl"}
{capture assign=mapURL}{crmURL p='civicrm/admin/setting/mapping' q="reset=1"}{/capture}

<div class="form-item">
<fieldset><legend>{ts}Event Information{/ts}</legend>
    <dl class="html-adjust">
        <dt>{$form.event_type_id.label}</dt><dd>{$form.event_type_id.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}After selecting an Event Type, this page will display any custom event fields for that type.{/ts}</dd>
     	<dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    	<dt>{$form.summary.label}</dt><dd>{$form.summary.html}</dd>
    	<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.start_date.label}</dt><dd>{$form.start_date.html}</dd>
        <dt></dt>
        <dd>{include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=3 doTime=1 trigger=trigger_event_1}
        </dd>
        <dt>{$form.end_date.label}</dt><dd>{$form.end_date.html}</dd>
        <dt></dt>
        <dd>{include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=end_date offset=3 doTime=1 trigger=trigger_event_1}</dd>
        <dt>{$form.max_participants.label}</dt><dd>{$form.max_participants.html|crmReplace:class:four}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Optionally set a maximum number of participants for this event. The registration link is hidden, and the text below is displayed when the maximum number of registrations is reached.{/ts}</dd>
        <dt>{$form.event_full_text.label}</dt><dd>{$form.event_full_text.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Text displayed on the Event Information page when the maximum number of registrations is reached. If online registration is enabled, this message will also be displayed if users attempt to register.{/ts}</dd>
        <dt>&nbsp;</dt><dd>{$form.is_map.html} {$form.is_map.label}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts 1=$mapURL}Include a link to map the event location? (A map provider must be configured under <a href="%1">Global Settings &raquo; Mapping</a>{/ts}</dd>
    	<dt>&nbsp;</dt><dd>{$form.is_public.html} {$form.is_public.label}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Include this event in iCalendar feeds?{/ts}</dd>
        <dt>&nbsp;</dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
        {if $id}
            <dt>&nbsp;</dt><dd class="description">{ts}When this Event is active, create links to the Event Information page by copying and pasting the following URL:{/ts}<br />
            <strong>{crmURL p='civicrm/event/info' q="reset=1&id=`$id`"}</strong></dd>
        {/if}
        </dl>
        <div class="spacer"></div>
	    {if $action eq 4}
         {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
        {else}
          {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
        {/if}
        </fieldset>
        <dl>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>

</div>

<script type="text/javascript">
{literal}
function reload(refresh)
{
    var eventId = document.getElementById("event_type_id");
    var url = "{/literal}{$refreshURL}{literal}"
    var post = url + "&etype=" + eventId.value;
    if( refresh ) {
        window.location= post; 
    }
}

function defaultDate(data)
{

// if end date is not given then it is equal to start date 
if( !document.getElementsByName("end_date[M]")[0].value) {
    document.getElementsByName("end_date[M]")[0].value =  document.getElementsByName("start_date[M]")[0].value;
    document.getElementsByName("end_date[d]")[0].value =  document.getElementsByName("start_date[d]")[0].value;
    document.getElementsByName("end_date[Y]")[0].value =  document.getElementsByName("start_date[Y]")[0].value;
    document.getElementsByName("end_date[h]")[0].value =  document.getElementsByName("start_date[h]")[0].value;
    document.getElementsByName("end_date[i]")[0].value =  document.getElementsByName("start_date[i]")[0].value;
    document.getElementsByName("end_date[A]")[0].value =  document.getElementsByName("start_date[A]")[0].value;
 }
}

{/literal}
</script>
