<div class="form-item">
<fieldset>
<dl>{$form.buttons.html}</dl>
{if $suppressedEmails > 0}
    <div class="status">
        <p>{ts count=$suppressedEmails plural='Email will NOT be sent to %count contacts - (no email address on file, or communication preferences specify DO NOT EMAIL, or contact is deceased).'}Email will NOT be sent to %count contact - (no email address on file, or communication preferences specify DO NOT EMAIL, or contact is deceased).{/ts}</p>
    </div>
{/if}
<table class="form-layout-compressed">
<tr>
    <td class="label">{$form.fromEmailAddress.label}</td><td>{$form.fromEmailAddress.html} {help id ="id-from_email"}</td>
</tr>
{if $single eq false}
    <tr>
        <td class="label">{ts}Recipient(s){/ts}</td><td>{$to|escape}
{else}
    <tr>
        <td class="label">{$form.to.label}</td>
        <td>{$form.to.html}{if $noEmails eq true}&nbsp;&nbsp;{$form.emailAddress.html}{/if}
{/if}
        <br /><a href="#" id="addcc">{ts}Add CC{/ts}</a>&nbsp;&nbsp;<a href="#" id="addbcc"">{ts}Add BCC{/ts}</a></td>
    </tr>
<tr id="cc" style="display:none;"><td class="label">{$form.cc_id.label}</td><td>{$form.cc_id.html}</td></tr>
<tr id="bcc" style="display:none;"><td class="label">{$form.bcc_id.label}</td><td>{$form.bcc_id.html}</td></tr>
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
</fieldset>
</div>

{literal}
<script type="text/javascript">
var toContact = '';

{/literal}
{foreach from=$toContact key=id item=name}
     {literal} toContact += '{"name":"'+{/literal}"{$name}"{literal}+'","id":"'+{/literal}"{$id}"{literal}+'"},';{/literal}
{/foreach}
{literal} eval( 'toContact = [' + toContact + ']');

cj('#addcc').toggle( function() { cj(this).text('Remove CC');
                                  cj('#cc').show();
                   },function() { cj(this).text('Add CC');
                                  cj('#cc').hide();
});
cj('#addbcc').toggle( function() { cj(this).text('Remove BCC');
                                   cj('#bcc').show();
                    },function() { cj(this).text('Add BCC');
                                   cj('#bcc').hide();
});

eval( 'tokenClass = { tokenList: "token-input-list-facebook", token: "token-input-token-facebook", tokenDelete: "token-input-delete-token-facebook", selectedToken: "token-input-selected-token-facebook", highlightedToken: "token-input-highlighted-token-facebook", dropdown: "token-input-dropdown-facebook", dropdownItem: "token-input-dropdown-item-facebook", dropdownItem2: "token-input-dropdown-item2-facebook", selectedDropdownItem: "token-input-selected-dropdown-item-facebook", inputToken: "token-input-input-token-facebook" } ');

var sourceDataUrl = "{/literal}{crmURL p='civicrm/ajax/checkemail'}{literal}";
var toDataUrl     = "{/literal}{crmURL p='civicrm/ajax/checkemail' q='id=1' }{literal}";
cj( "#to"     ).tokenInput( toDataUrl, { prePopulate: toContact, classes: tokenClass });
cj( "#cc_id"  ).tokenInput( sourceDataUrl, { classes: tokenClass });
cj( "#bcc_id" ).tokenInput( sourceDataUrl, { classes: tokenClass });
cj( 'ul.token-input-list-facebook, div.token-input-dropdown-facebook' ).css( 'width', '450px' );
</script>
{/literal}