{include file="CRM/common/WizardHeader.tpl"}
{include file="CRM/Mailing/Form/Count.tpl"}
<div id="help">
    {ts}You can schedule this mailing to be sent starting at a specific date and time, OR you can request that it be sent as soon as possible by checking &quot;Send Immediately&quot;.{/ts} {help id="sending"}
</div>

<div class="form-item">
<fieldset>
  <dl>
    <dt class="label">{$form.now.label}</dt><dd>{$form.now.html}</dd>
    <dt class="label">{ts}OR{/ts}</dt><dd>&nbsp;</dd>
    <dt class="label extra-long-fourty">{$form.start_date.label}</dt>
        <dd>{$form.start_date.html}<br />
            <span class="description">{ts}Set a date and time when you want CiviMail to start sending this mailing.{/ts}</span>
        </dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
