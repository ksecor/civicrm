<div id="help">
    {ts}Use this screen to configure formats for date display and date input fields. Defaults are provided for standard United States formats. Settings use standard POSIX specifiers.{/ts} {help id='date-format'}
</div>
<div class="form-item">
<fieldset><legend>{ts}Date Display{/ts}</legend>
     <dl>
      <dt>{$form.dateformatDatetime.label}</dt><dd>{$form.dateformatDatetime.html}</dd>
      <dt>{$form.dateformatFull.label}</dt><dd>{$form.dateformatFull.html}</dd>
      <dt>{$form.dateformatPartial.label}</dt><dd>{$form.dateformatPartial.html}</dd>
      <dt>{$form.dateformatYear.label}</dt><dd>{$form.dateformatYear.html}</dd>
      <dt>{$form.dateformatTime.label}</dt><dd>{$form.dateformatTime.html}</dd>
    </dl>
</fieldset>
<fieldset><legend>{ts}Date Input Fields{/ts}</legend>
     <dl>
      <dt>{$form.dateformatQfDate.label}</dt><dd>{$form.dateformatQfDate.html}</dd>
      <dt>{$form.dateformatQfDatetime.label}</dt><dd>{$form.dateformatQfDatetime.html}</dd>
    </dl>
    <div class="action-link">
    	<a href="{crmURL p="civicrm/admin/setting/preferences/date" q="reset=1"}" id="advDateSetting" title="{ts}Manage available date ranges and input formats for different types of date fields.{/ts}">&raquo; {ts}Advanced Date Input Settings{/ts}</a>
    </div>
</fieldset>
<fieldset>
<dt>{$form.fiscalYearStart.label}</dt><dd>{$form.fiscalYearStart.html}</dd>
</fieldset>
     <dl>
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
<div class="spacer"></div>
</div>
