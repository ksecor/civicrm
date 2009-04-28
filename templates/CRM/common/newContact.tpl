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

