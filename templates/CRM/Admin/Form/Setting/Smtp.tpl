<div id="help">
    {ts}If you are sending emails to contacts using CiviCRM - then you need to enter settings for your SMTP/Sendmail server. You can send a test email to check your SMTP/Sendmail settings by clicking "Save and Send Test Email". If you're unsure of the correct values, check with your system administrator, ISP or hosting provider.
    If you do not want users to send outbound mail from CiviCRM, select "Disable Outbound Email". NOTE: If you disable outbound email, and you are using Online Contribution pages or online Event Registration - you will need to disable automated receipts and registration confirmations.{/ts}
</div>
<div class="form-item">

<fieldset>
<legend>{ts}Settings - Outbound Email{/ts}</legend>
<dl>
        <dt>{$form.outBound_option.label}</dt><dd>{$form.outBound_option.html}</dd>
</dl>
<div id="bySMTP">
<fieldset><legend>{ts}SMTP Configuration{/ts}</legend>
        <dl>
            <dt>{$form.smtpServer.label}</dt><dd>{$form.smtpServer.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Enter the SMTP server (machine) name. EXAMPLE: smtp.example.com{/ts}</dd>
            <dt>{$form.smtpPort.label}</dt><dd>{$form.smtpPort.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}The standard SMTP port is 25. You should only change that value if your SMTP server is running on a non-standard port.{/ts}</dd>
            <dt>{$form.smtpAuth.label}</dt><dd>{$form.smtpAuth.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Does your SMTP server require authentication (user name + password)?{/ts}</dd>    
            <dt>{$form.smtpUsername.label}</dt><dd>{$form.smtpUsername.html}</dd>
            <dt>{$form.smtpPassword.label}</dt><dd>{$form.smtpPassword.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}If your SMTP server requires authentication, enter your Username and Password here.{/ts}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div>
<div id="bySendmail">
<fieldset><legend>{ts}Sendmail Configuration{/ts}</legend>
        <dl>
            <dt>{$form.sendmail_path.label}</dt><dd>{$form.sendmail_path.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Enter the Sendmail Path. EXAMPLE: /usr/sbin/sendmail{/ts}</dd>
            <dt>{$form.sendmail_args.label}</dt><dd>{$form.sendmail_args.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div>
    <dl>
        <dt></dt><dd>{$form.buttons.html}<span id="idSendTestMail">&nbsp;&nbsp;&nbsp;&nbsp;{$form.sendTestEmail.html}</span></dd>
    </dl>
</div>
</fieldset>

{literal}
<script type="text/javascript">
window.onload = function() {
showHideMailOptions();
}
function showHideMailOptions()
{
    if (document.getElementsByName("outBound_option")[0].checked) {
        show("bySMTP");
        hide("bySendmail");
    } else if (document.getElementsByName("outBound_option")[1].checked) {
        hide("bySMTP");
        show("bySendmail");
    } else {
        hide("bySMTP");
        hide("bySendmail");
        hide("idSendTestMail");
    }
}
</script>
{/literal}
