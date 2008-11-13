<div class="form-item">
<fieldset>
<legend>{ts}Send an Email{/ts}</legend>
{if $suppressedEmails > 0}
    <div class="status">
        <p>{ts count=$suppressedEmails plural='Email will NOT be sent to %count contacts - communication preferences specify DO NOT EMAIL.'}Email will NOT be sent to %count contact - communication preferences specify DO NOT EMAIL.{/ts}</p>
    </div>
{/if}
<dl>
<dt>{$form.fromEmailAddress.label}</dt><dd>{$form.fromEmailAddress.html}</dd>
{if $single eq false}
<dt>{ts}Recipient(s){/ts}</dt><dd>{$to|escape}</dd>
{else}
<dt>{$form.to.label}</dt><dd>{$form.to.html}{if $noEmails eq true}&nbsp;&nbsp;{$form.emailAddress.html}{/if}</dd>
{/if}
<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
</dl>
{include file="CRM/Contact/Form/Task/EmailCommon.tpl"}
<div class="spacer"> </div>
<dl>
{if $single eq false}
    <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
{/if}
{if $suppressedEmails > 0}
    <dt></dt><dd>{ts count=$suppressedEmails plural='Email will NOT be sent to %count contacts.'}Email will NOT be sent to %count contact.{/ts}</dd>
{/if}
</dl>
<dl>
<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>
