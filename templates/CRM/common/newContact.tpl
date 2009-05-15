<div id="contact-dialog" style="display:none;"/>
<script type="text/javascript">
{literal}
function newContact( gid ) {
    cj("#newContact").toggle( );
    var dataURL = {/literal}"{crmURL p='civicrm/profile/create?reset=1&snippet=5&context=dialog' h=0 }"{literal};
    dataURL = dataURL + '&gid=' + gid;
    cj.ajax({
       url: dataURL,
       success: function( content ){
           cj("#contact-dialog").show( ).html( content ).dialog({
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
     });
}
{/literal}
</script>


