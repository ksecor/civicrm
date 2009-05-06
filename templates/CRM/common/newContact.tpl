<div id="contact-dialog" style="display:none;"></div>
<script type="text/javascript">
{literal}
function newContact( ) {
    cj("#newContact").toggle( );
    var dataURL = {/literal}"{crmURL p='civicrm/contact/profilecreate?reset=1&gid=1&snippet=4' h=0 }"{literal};
    cj("#contact-dialog").show();
    cj("#contact-dialog").load( dataURL ).dialog({
		title: "Create New Contact",
		modal: true,
		width: 680, 
		overlay: { 
			opacity: 0.5, 
			background: "black" 
		},
        
        beforeclose: function(event, ui) {
            cj(this).dialog("destroy");
        }
        
	});
}
{/literal}
</script>


