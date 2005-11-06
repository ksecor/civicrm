<link rel="stylesheet" type="text/css" media="all" href="{$config->resourceBase}css/skins/aqua/theme.css" title="Aqua" /> 
<script type="text/javascript" src="{$config->resourceBase}js/calendar.js"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/lang/calendar-lang.php"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/calendar-setup.js"></script> 

<div class="form-item">
<fieldset>
<legend>
{ts}Record an Activity{/ts}
</legend>
<dl>
<dt>{ts}With Contact{/ts}</dt><dd>{$displayName}&nbsp;</dd>
<dt>{$form.activity_type_id.label} <dd>{$form.activity_type_id.html}{$form.description.html|crmReplace:class:texttolabel}</dd></dt>
<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
<dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
<dt>&nbsp;</dt>
<dd class="description">
  <img src="{$config->resourceBase}i/cal.gif" id="trigger" alt="{ts}Calender{/ts}"/>
  {ts}Click to select date/time from calendar.{/ts}
</dd>
            {literal}
            <script type="text/javascript">
              var obj = new Date();
              var currentYear = obj.getFullYear();
              var endYear     = currentYear + 3 ;
              Calendar.setup(
                {
                  dateField   : "scheduled_date_time[d]",
                  monthField  : "scheduled_date_time[M]",
                  yearField   : "scheduled_date_time[Y]",
                  hourField   : "scheduled_date_time[h]",
                  minuteField : "scheduled_date_time[i]",
                  ampmField   : "scheduled_date_time[A]",
                  button      : "trigger",
                  range       : [currentYear, endYear],
                  showsTime   : true,
                  timeFormat  : "12"
                }
              );
            </script>
            {/literal}
<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} {ts}Hrs{/ts} &nbsp; {$form.duration_minutes.html} {ts}Min{/ts} &nbsp;</dd>
<dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
<dt>&nbsp;</dt><dd class="description">{ts}Activity will be moved to Activity History when status is 'Completed'.{/ts}</dd>
<dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}&nbsp;</dd>
<dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
<dt></dt><dd>{$form.buttons.html}</dd>
</fieldset>
</div>
