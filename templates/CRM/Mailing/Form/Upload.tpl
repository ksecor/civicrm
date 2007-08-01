{include file="CRM/common/WizardHeader.tpl"}
<div id="help">
<p>
    {ts}Before completing this step, you must create one or two files containing your mailing content.{/ts} {help id="id-upload"}
</p>
<p>
    {ts}CiviMail email messages must include an unsubscribe link, an opt-out link, and the postal address of your organization. These elements help reduce the chances of your email being categorized as SPAM.{/ts} 
    <a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a>
</p>
</div>

<div class="form-item">
  <fieldset><legend>{ts}Content{/ts}</legend>
    <dl>
    <dt class="label">{$form.from_name.label}</dt><dd>{$form.from_name.html}</dd>
    <dt class="label">{$form.from_email.label}</dt><dd>{$form.from_email.html}</dd>
    <dt class="label">{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
    <dt class="label extra-long-fourty">{$form.textFile.label}</dt>
        <dd>{$form.textFile.html}<br />
            <span class="description">{ts}Browse to the <strong>TEXT</strong> message file you have prepared for this mailing.{/ts}<br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></span>
        </dd>
    <dt class="label extra-long-fourty">{$form.htmlFile.label}</dt>
        <dd>{$form.htmlFile.html}<br />
            <span class="description">{ts}Browse to the <strong>HTML</strong> message file you have prepared for this mailing.{/ts}<br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></span>
        </dd>
    <dt class="label extra-long-fourty">{$form.header_id.label}</dt>
        <dd>{$form.header_id.html}<br />
            <span class="description">{ts}You may choose to include a pre-configured Header block above your message.{/ts}</span>
        </dd>
    <dt class="label extra-long-fourty">{$form.footer_id.label}</dt>
        <dd>{$form.footer_id.html}<br />
            <span class="description">{ts}You may choose to include a pre-configured Footer block below your message. This is a good place to include the required unsubscribe, opt-out and postal address tokens.{/ts}</span>
        </dd>
    </dl>
  </fieldset>
  <fieldset><legend>{ts}Tracking{/ts}</legend> 
    <dl>
    <dt class="label extra-long-sixty">{$form.track_urls.label}</dt>
        <dd>{$form.track_urls.html}<br />
            <span class="description">{ts}Track the number of times recipients click each link in this mailing. NOTE: When this feature is enabled, all links in the message body will be automaticallly re-written to route through your CiviCRM server prior to redirecting to the target page.{/ts}</span>
        </dd>
    <dt class="label">{$form.track_opens.label}</dt>
        <dd>{$form.track_opens.html}<br />
            <span class="description">{ts}Track the number of times recipients open this mailing in their email software.{/ts}</span>
        </dd>
    </dl>
  </fieldset>
  <fieldset><legend>{ts}Responding{/ts}</legend> 
    <dl>
        <dt class="label extra-long-fourty">{$form.forward_reply.label}</dt>
            <dd>{$form.forward_reply.html}<br />
                <span class="description">{ts}If a recipient replies to this mailing, forward the reply to the FROM Email address specified above.{/ts}</span>
            </dd>
    <dt class="label">{$form.auto_responder.label}</dt>
        <dd>{$form.auto_responder.html} &nbsp; {$form.reply_id.html}<br />
            <span class="description">{ts}If a recipient replies to this mailing, send an automated reply using the selected message.{/ts}</span>
        </dd>
    <dt class="label">{$form.unsubscribe_id.label}</dt>
        <dd>{$form.unsubscribe_id.html}<br />
            <span class="description">{ts}Select the automated message to be sent when a recipient unsubscribes from this mailing.{/ts}</span>
        </dd>
    <dt class="label extra-long-fourty">{$form.optout_id.label}</dt>
        <dd>{$form.optout_id.html}<br />
            <span class="description">{ts}Select the automated message to be sent when a recipient opts out of all mailings from your site.{/ts}</span>
        </dd>
   </dl>
  </fieldset>
  <dl>
    <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
  </dl>
</div>
