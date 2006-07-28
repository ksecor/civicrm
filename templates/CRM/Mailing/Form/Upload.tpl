{include file="CRM/common/WizardHeader.tpl"}

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
    <dt class="label">{$form.htmlFile.label}</dt><dd>{$form.htmlFile.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
