{* template for adding form elements for selecting existing or creating new contact*}
<tr id="contact-success" style="display:none;"><td></td><td><span class="success-status">{ts}New contact has been created.{/ts}</span></td></tr>
<tr>
    <td class="label">{$form.contact.label}</td><td>{$form.contact.html}&nbsp;&nbsp;{ts}OR{/ts}&nbsp;&nbsp;{$form.profiles.html}</td>
</tr>

{literal}
	<script type="text/javascript">

        var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' h=0 }"{literal};

        cj("#contact").autocomplete( contactUrl, {
        	selectFirst: false 
        }).focus();

        cj("#contact").result(function(event, data, formatted) {
        	cj("input[name=contact_select_id]").val(data[1]);
        });

        cj("#contact").bind("keypress keyup", function(e) {
            if ( e.keyCode == 13 ) {
                return false;
            }
        });
</script>
{/literal}

{*include new contact dialog file*}
{include file="CRM/common/newContact.tpl"}


