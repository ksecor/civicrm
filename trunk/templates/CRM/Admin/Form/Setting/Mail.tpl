<div id="help">
    {ts 1="http://wiki.civicrm.org/confluence/display/CRMDOC/CiviMail+Admin"}These settings are used to configure mailer properties for the optional CiviMail component. They are NOT used for the built-in 'Send Email to Contacts' feature. Refer to the <a href='%1' target='_blank' title='CiviMail Guide (opens in a new window)'>CiviMail Guide</a> for more information.{/ts}
</div>
<div class="form-item">
<fieldset><legend>{ts}CiviMail Configuration{/ts}</legend>
      <dl>
       
        <dt>{$form.mailerPeriod.label}</dt><dd>{$form.mailerPeriod.html}</dd>      
        <dt>&nbsp;</dt><dd class="description">{ts}Number of seconds between delivery attempts for new outgoing mailings.{/ts}</dd>
        <dt>{$form.mailerBatchLimit.label}</dt><dd>{$form.mailerBatchLimit.html}</dd>    
        <dt>&nbsp;</dt><dd class="description">{ts}Throttle email delivery by setting the maximum number of emails sent during each CiviMail run (0 = unlimited).{/ts}</dd>
        <dt>{$form.mailerSpoolLimit.label}</dt><dd>{$form.mailerSpoolLimit.html}</dd>    
        <dt>&nbsp;</dt><dd class="description">{ts}Set the limit of emails sent via smtp mailer, for more than limit send them in Spool table.{/ts}</dd>
        <dt>{$form.verpSeparator.label}</dt><dd>{$form.verpSeparator.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Separator character used when CiviMail generates VERP (variable envelope return path) Mail-From addresses.{/ts}</dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
       </dl>
<div class="spacer"></div>
</fieldset>
</div>
