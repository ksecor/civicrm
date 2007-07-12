<div class="form-item">
<fieldset>
<legend>{ts}Sent SMS Message{/ts}</legend>
<dl>
<dt>{ts}Date Sent{/ts}</dt><dd>{$sentDate|crmDate}</dd>
<dt>{ts}From{/ts}</dt><dd>{if $fromName}{$fromName}{else}{ts}(display name not available){/ts}{/if}</dd>
<dt>{ts}To{/ts}</dt><dd>{$toName}</dd>
<dt>{ts}Message{/ts}</dt><dd>{$message}</dd>
<dt>&nbsp;</dt><dd><input type="button" name="Done" value="Done" onClick="location.href='{crmURL p='civicrm/contact/view/activity' q="history=1&show=1"}';"></dd>
</dl>
</fieldset>
</div>
