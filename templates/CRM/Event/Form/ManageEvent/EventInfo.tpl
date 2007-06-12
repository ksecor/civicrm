{* Step 1 of New Event Wizard, and Edit Event Info form.  *}
{include file="CRM/common/WizardHeader.tpl"}
{capture assign=mapURL}{crmURL p='civicrm/admin/setting/mapping' q="reset=1"}{/capture}

<fieldset><legend>{ts}Event Information{/ts}</legend>
<table class="form-layout-compressed">
         <tr><td class="label">{$form.event_type_id.label}</td><td>{$form.event_type_id.html}</td></tr>
         <tr><td>&nbsp</td><td class="description">{ts}After selecting an Event Type, this page will display any custom event fields for that type.{/ts}</td></tr>
         <tr><td class="label">{$form.title.label}</td><td>{$form.title.html}</td></tr>
         <tr><td class="label">{$form.summary.label}</td><td>{$form.summary.html}</td></tr>
         <tr><td class="label">{$form.description.label}</td><td>{$form.description.html}</td></tr>
         <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}</td></tr>
         <tr><td>&nbsp;</td><td>{include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1}
         {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=3 doTime=1 trigger=trigger_event_1}</td></tr>
         <tr><td class="label">{$form.end_date.label}</td><td>{$form.end_date.html}</td></tr>
         <tr><td>&nbsp;</td><td>{include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_2}
         {include file="CRM/common/calendar/body.tpl" dateVar=end_date offset=3 doTime=1 trigger=trigger_event_2}</td></tr>
         <tr><td class="label">{$form.max_participants.label}</td><td>{$form.max_participants.html|crmReplace:class:four}</td></tr> 
         <tr><td>&nbsp;</td><td class="description">{ts}Optionally set a maximum number of participants for this event. The registration link is hidden, and the text below is displayed when the maximum number of registrations is reached.{/ts}</td></tr>
         <tr><td class="label">{$form.event_full_text.label}</td><td>{$form.event_full_text.html}</td></tr>
         <tr><td>&nbsp;</td><td class="description">{ts}Text displayed on the Event Information page when the maximum number of registrations is reached. If online registration is enabled, this message will also be displayed if users attempt to register.{/ts}</td></tr>
         <tr><td>&nbsp;</td><td>{$form.is_map.html} {$form.is_map.label}</td></tr>
         <tr><td>&nbsp;</td><td class="description">{ts 1=$mapURL}Include a link to map the event location? (A map provider must be configured under <a href="%1">Global Settings &raquo; Mapping</a>{/ts}</td></tr>
         <tr><td>&nbsp;</td><td>{$form.is_public.html} {$form.is_public.label}</td></tr>
         <tr><td>&nbsp</td><td class="description">{ts}Include this event in iCalendar feeds?{/ts}</td></tr>
         <tr><td>&nbsp</td><td>{$form.is_active.html} {$form.is_active.label}</td></tr> 

        {if $id}
         <tr><td>&nbsp;</td>
            <td class="description">
            {if $config->userFramework EQ 'Drupal'}
                {ts}When this Event is active, create links to the Event Information page by copying and pasting the following URL:{/ts}<br />
                <strong>{crmURL p='civicrm/event/info' q="reset=1&id=`$id`"}</strong>
            {elseif $config->userFramework EQ 'Joomla'}
                {ts 1=$id}When this Event is active, create front-end links to the Event Information page using the Menu Manager. Select <strong>Event Info Page</strong>
                and enter <strong>%1</strong> for the Event ID.{/ts}
            {/if}
            </td>
         </tr>
        {/if}
        </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
	    {if $action eq 4}
            {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
        {else}
            {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
        {/if}
        <tr><td>&nbsp;</td><td>{$form.buttons.html}</td></tr>        
</table>
</fieldset>     

<script type="text/javascript">
{literal}
    function reload(refresh) {
        var eventId = document.getElementById("event_type_id");
        var url = "{/literal}{$refreshURL}{literal}"
        var post = url + "&etype=" + eventId.value;
        if( refresh ) {
        window.location= post; 
        }
    }

    function defaultDate(data) {
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
