{include file="CRM/common/WizardHeader.tpl"}
<div id="help">
<p>{ts}You must create two files with the actual message you are sending in this mailing - an HTML formatted file, and a simple text file. Save these files somewhere on your local computer - and locate them using the <strong>Browse...</strong> buttons below.{/ts}</p>

<p>{ts}CiviMail email messages must include an unsubscribe link, an opt-out link, and the postal address of your organization. These elements help reduce the chances of your email being categorized as SPAM.{/ts} <a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></p>
</div>

<div class="form-item">
<fieldset>
  <legend>{ts}Settings and Content{/ts}</legend>
  <dl>
    <dt class="label">{$form.from_name.label}</dt><dd>{$form.from_name.html}</dd>
    <dt class="label">{$form.from_email.label}</dt><dd>{$form.from_email.html}</dd>
    <dt class="label">{$form.forward_reply.label}</dt><dd>{$form.forward_reply.html}</dd>
    <dt class="label">{$form.track_urls.label}</dt><dd>{$form.track_urls.html}</dd>
    <dt class="label">{$form.track_opens.label}</dt><dd>{$form.track_opens.html}</dd>
    <dt class="label">{$form.auto_responder.label}</dt><dd>{$form.auto_responder.html}</dd>
    <dt class="label">{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
    <dt class="label">{$form.header_id.label}</dt><dd>{$form.header_id.html}</dd>
    <dt class="label">{$form.footer_id.label}</dt><dd>{$form.footer_id.html}</dd>
    <dt class="label">{$form.reply_id.label}</dt><dd>{$form.reply_id.html}</dd>
    <dt class="label">{$form.unsubscribe_id.label}</dt><dd>{$form.unsubscribe_id.html}</dd>
    <dt class="label">{$form.optout_id.label}</dt><dd>{$form.optout_id.html}</dd>
    <dt class="label">{$form.textFile.label}</dt><dd>{$form.textFile.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Browse to the <strong>TEXT</strong> message file you have prepared for this mailing.{/ts}<br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></dd>
    <dt class="label">{$form.htmlFile.label}</dt><dd>{$form.htmlFile.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Browse to the <strong>HTML</strong> message file you have prepared for this mailing.{/ts}<br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></dd>
  </dl>
  <dl>
    <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
