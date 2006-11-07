<div class="form-item">
<fieldset><legend>{ts}Mailer Setting{/ts}</legend>
      <dl>
       
        <dt>{$form.mailerPeriod.label}</dt><dd>{$form.mailerPeriod.html}</dd>      
        <dt>&nbsp</dt><dd class="description">{ts}Number of seconds between delivery attempts for new outgoing mailings.{/ts}</dd>
        <dt>{$form.verpSeparator.label}</dt><dd>{$form.verpSeparator.html}</dd>
        <dt>&nbsp</dt><dd class="description">{ts} Separator character used when CiviMail generates VERP (variable envelope return path) Mail-From addresses.{/ts}</dd>
        <dt>{$form.mailerBatchLimit.label}</dt><dd>{$form.mailerBatchLimit.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}Number of emails sent every CiviMail run (0 - no limit)..{/ts}</dd>
 
         <dt></dt><dd>{$form.buttons.html}</dd>
       </dl>
<div class="spacer"></div>
</fieldset>
</div>
