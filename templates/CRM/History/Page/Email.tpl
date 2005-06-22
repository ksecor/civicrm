<div class="form-item">
<fieldset>
<legend>{ts}Sent Email Message{/ts}</legend>
<dl>
<dt>Date Sent</dt><dd>{$sentDate|crmDate}</dd>
<dt>From</dt><dd>{if $fromName}{$fromName}{else}(display name not available){/if}</dd>
<dt>To</dt><dd>{$toName}</dd>
<dt>Subject</dt><dd>{$subject}</dd>
<dt>Message</dt><dd>{$message}</dd>
<dt>&nbsp;</dt><dd><input type="button" name="Done" value="Done" onClick="location.href='{crmURL p='civicrm/contact/view/activity' q="history=1"}';"></dd>
</fieldset>
</div>
