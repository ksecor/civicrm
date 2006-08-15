<div class="form-item">
<fieldset>
<legend>{ts}Sent Email Message{/ts}</legend>
<dl>
<dt>{ts}Date Sent{/ts}</dt><dd>{$sentDate|crmDate}</dd>
<dt>{ts}From{/ts}</dt><dd>{if $fromName}{$fromName|escape}{else}{ts}(display name not available){/ts}{/if}</dd>
<dt>{ts}To{/ts}</dt><dd>{$toName|escape}</dd>
<dt>{ts}Subject{/ts}</dt><dd>{$subject}</dd>
<dt>{ts}Message{/ts}</dt><dd>{$message}</dd>
<dt>&nbsp;</dt><dd><input type="button" name="Done" value="Done" onclick="location.href='{crmURL p='civicrm/contact/view/activity' q="history=1&show=1"}';"></dd>
</dl>
</fieldset>
</div>
