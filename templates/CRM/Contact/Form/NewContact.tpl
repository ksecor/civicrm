{* added onload javascript for contact*}
<table class="form-layout-compressed">
    <tr>
        <td class="label" style="width:120px">{$form.contact.label}</td><td>{$form.contact.html}&nbsp;&nbsp;<a href="javascript:newContact( );">{ts}Create New Contact{/ts}</a></td>
    </tr>
    <tr id="newContact" style="display:none;">
        <td colspan="2">
            <div id="contact-dialog" style="display:none;"></div>
        </td>
    </tr>
</table>

<script type="text/javascript">
{literal}
var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' h=0 }"{literal};

cj("#contact").autocomplete( contactUrl, {
	selectFirst: false 
}).focus();

cj("#contact").result(function(event, data, formatted) {
	cj("input[name=contact_id]").val(data[1]);
});

cj("#contact").bind("keypress keyup", function(e) {
    if ( e.keyCode == 13 ) {
        return false;
    }
});

{/literal}
</script>
{*include new contact js file*}
{include file="CRM/common/newContact.tpl"}
