{literal}
<script type="text/javascript">
$(document).ready(function(){
	$("#activity-content").css({'overflow':'auto', 'width':'680px', 'height':'560px'});
});
	
function viewActivity( activityID, contactID ) {
    $("#view-activity").show( );

    $("#view-activity").dialog({
        title: "View Activity",
        modal: true, 
        width : 700,
        height : 650,
        resizable: true, 
        overlay: { 
            opacity: 0.5, 
            background: "black" 
        },
        open:function() {
            $(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar-close").remove();
            $("#activity-content").html("");
            var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
            $("#activity-content").load( viewUrl + "&cid="+contactID + "&aid=" + activityID);
        },

        buttons: { 
            "Done": function() { 	    
                $(this).dialog("close"); 
                $(this).dialog("destroy"); 
            }
        }
    });
}
</script>
{/literal}
