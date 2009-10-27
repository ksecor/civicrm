{include file="CRM/common/WizardHeader.tpl"}
{include file="CRM/Mailing/Form/Count.tpl"}
<div id="help">
    {ts}You can schedule this mailing to be sent starting at a specific date and time, OR you can request that it be sent as soon as possible by checking &quot;Send Immediately&quot;.{/ts} {help id="sending"}
</div>

<fieldset>
 <table class="form-layout">
  <tbody>
    <tr>
        <td>{$form.now.label}</td>
        <td>{$form.now.html}</td>
    </tr>
    <tr>
        <td>{ts}OR{/ts}</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>{$form.start_date.label}</td>
        <td>{include file="CRM/common/jcalendar.tpl" elementName=start_date}
            <span class="description">{ts}Set a date and time when you want CiviMail to start sending this mailing.{/ts}</span>
        </td>
    </tr>
    <tr><td colspan="2">{$form.buttons.html}</td></tr>
  </tbody>
</table>
</fieldset>

{* include jscript to warn if unsaved form field changes *}
{include file="CRM/common/formNavigate.tpl"}


