
<div class="form-item">
<fieldset>
<legend>
{ts}Record an Activity{/ts}
</legend>
<dl>
<dt>{ts}Created By{/ts}</dt><dd>{$displayName}&nbsp;</dd>
<dt>{$form.activity_type_id.label}</dt> <dd>{$form.activity_type_id.html}{$form.description.html|crmReplace:class:texttolabel}</dd>
<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
<dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
<dt>&nbsp;</dt>
<dd class="description">
   {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity doTime=1}
   {include file="CRM/common/calendar/body.tpl" dateVar=scheduled_date_time startDate=currentYear endDate=endYear offset=10 trigger=trigger_activity doTime=1 ampm=1}
</dd>
<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} {ts}Hrs{/ts} &nbsp; {$form.duration_minutes.html} {ts}Min{/ts} &nbsp;</dd>
<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
<dt>&nbsp;</dt><dd class="description">{ts}Activity will be moved to Activity History when status is 'Completed'.{/ts}</dd>
<dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}&nbsp;</dd>
<dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
 <dt></dt><dd class="description">
{include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
 </dd>
<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>

<script type="text/javascript" >
{literal}

    function reload(refresh) {
        var activityType = document.getElementById("activity_type_id");
        var url = {/literal}"{$refreshURL}"{literal}
        var post = url + "&subType=" + activityType.value;
        if( refresh ) {
            window.location= post; 
        }
    }
{/literal}
 </script>
