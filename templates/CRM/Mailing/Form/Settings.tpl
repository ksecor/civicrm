{include file="CRM/common/WizardHeader.tpl"}
{include file="CRM/Mailing/Form/Count.tpl"}

<div class="form-item">
  <fieldset><legend>{ts}Tracking{/ts}</legend> 
    <dl>
    <dt class="label extra-long-sixty">{$form.url_tracking.label}</dt>
        <dd>{$form.url_tracking.html}<br />
            <span class="description">{ts}Track the number of times recipients click each link in this mailing. NOTE: When this feature is enabled, all links in the message body will be automaticallly re-written to route through your CiviCRM server prior to redirecting to the target page.{/ts}</span>
        </dd>
    <dt class="label">{$form.open_tracking.label}</dt>
        <dd>{$form.open_tracking.html}<br />
            <span class="description">{ts}Track the number of times recipients open this mailing in their email software.{/ts}</span>
        </dd>
    </dl>
  </fieldset>
  <fieldset><legend>{ts}Responding{/ts}</legend> 
    <dl>
        <dt class="label extra-long-fourty">{$form.forward_replies.label}</dt>
            <dd>{$form.forward_replies.html}<br />
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

