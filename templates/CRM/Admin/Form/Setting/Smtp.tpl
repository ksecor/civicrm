<div id="help">
    {ts}If you are sending emails to contacts using CiviCRM - then you need to enter settings for your SMTP server. If
    you're unsure of the correct values, check with your system administrator, ISP or hosting provider.{/ts}
</div>
<div class="form-item">
<fieldset><legend>{ts}SMTP Configuration{/ts}</legend>
        <dl>
            <dt>{$form.smtpServer.label}</dt><dd>{$form.smtpServer.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Enter the SMTP server (machine) name. EXAMPLE: smtp.example.com {/ts}</dd>
            <dt>{$form.smtpPort.label}</dt><dd>{$form.smtpPort.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}The standard STMP port is 25. You should only change that value if your SMTP server is running on a non-standard port.{/ts}</dd>
            <dt>{$form.smtpAuth.label}</dt><dd>{$form.smtpAuth.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Does your SMTP server require authentication (user name + password)?{/ts}</dd>    
            <dt>{$form.smtpUsername.label}</dt><dd>{$form.smtpUsername.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}If your SMTP server requires authentication, enter your Username here. You must also enter you SMTP password in the CiviCRM
            settings file (civicrm.settings.php).{/ts}</dd>
        </dl>
        <dl>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div>
