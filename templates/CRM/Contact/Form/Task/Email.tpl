<div class="form-item">
<fieldset>
<legend>{ts}Send an Email{/ts}</legend>
{if $suppressedEmails > 0}
    <div class="status">
        <p>{ts count=$suppressedEmails plural='Email will NOT be sent to %count contacts - communication preferences specify DO NOT EMAIL.'}Email will NOT be sent to %count contact - communication preferences specify DO NOT EMAIL.{/ts}</p>
    </div>
{/if}
<table class="form-layout-compressed">
<tr>
    <td class="label">{$form.fromEmailAddress.label}</td><td>{$form.fromEmailAddress.html}</td>
</tr>
{if $single eq false}
    <tr>
        <td class="label">{ts}Recipient(s){/ts}</td><td>{$to|escape}</td>
    </tr>
{else}
    <tr>
        <td class="label">{$form.to.label}</td><td>{$form.to.html}{if $noEmails eq true}&nbsp;&nbsp;{$form.emailAddress.html}{/if}</td>
    </tr>
{/if}
<tr>
    <td class="label">{$form.subject.label}</td><td>{$form.subject.html|crmReplace:class:huge}</td>
</tr>
</table>

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
