{* handle common enable/disable actions *}
{literal}
<script type="text/javascript">
function modifyLinkAttributes( recordID, op ) {
 //we changed record from enable to disable
    if ( op == 'enable-disable' ) {
	var fieldID     = "#row_"+ recordID + " a." + "disable-action";
	var operation   = "disable-enable";
	var htmlContent = 'Enable';
	var newClass    = 'enable-action';
	var newTitle    = 'Enable';
    } else if ( op == 'disable-enable' ) {
	var fieldID     = "#row_"+ recordID + " a." + "enable-action";
	var operation   = "enable-disable";
	var htmlContent = 'Disable';
	var newClass    = 'disable-action';
	var newTitle    = 'Disable';
    }

    //change html title
    cj( fieldID ).html( htmlContent ); 	
   
    //change title
    cj( fieldID ).attr({title:newTitle});

    //need to update js - change op from js since 
    var updatedJavaScript = cj( fieldID ).attr("onClick").replace( op, operation );
  
    //set updated js
    cj( fieldID ).attr({ onClick : updatedJavaScript });

    //finally change class to enable-action.
    cj( fieldID ).attr({class: newClass });
}

function modifySelectorRow( recordID, op ) {
    var elementID = "#row_" + recordID;
    if ( op == "disable-enable" ) {
	cj( elementID ).removeClass("disabled");
    } else if ( op == "enable-disable" )  {
	//we are disabling record.
	cj( elementID ).addClass("disabled");
    }
}

function hideEnableDisableStatusMsg( ) {
  cj( '#enableDisableStatusMsg' ).hide( );
}

function enableDisable( recordID, recordDAO, op ) {
 var statusMsg = '{/literal}{ts}Are you sure you want to enable this record?{/ts}{literal}';
 if ( op == 'enable-disable' ) {
    statusMsg = '{/literal}{ts}Are you sure you want to disable this record?{/ts}{literal}';
 }

 var confirmMsg =  statusMsg + '&nbsp; <a href="javascript:saveEnableDisable( ' + recordID + ',\'' + recordDAO + '\'' + ', \'' + op + '\'' + ' );" style="text-decoration: underline;">{/literal}{ts}Yes{/ts}{literal}</a>&nbsp;&nbsp;&nbsp;<a href="javascript:hideEnableDisableStatusMsg();" style="text-decoration: underline;">{/literal}{ts}No{/ts}{literal}</a>';

       cj( '#enableDisableStatusMsg' ).show( ).html( confirmMsg );
}

function saveEnableDisable( recordID, recordDAO, op ) {
    var postUrl = {/literal}"{crmURL p='civicrm/ajax/ed' h=0 }"{literal};
    
    var statusMsg = '{/literal}{ts}The selected record has been disabled.{/ts}{literal}';
    if ( op == 'disable-enable' ) {
	statusMsg = '{/literal}{ts}The selected record has been enabled.{/ts}{literal}';
    } 
    
    cj.ajax({
	    type: "POST",
	    data:  "recordID=" + recordID + "&recordDAO=" + recordDAO + "&op=" + op,    
	    url: postUrl,
		
	    success: function(html){
		var resourceBase   = {/literal}"{$config->resourceBase}"{literal};
		var successMsg =  statusMsg + '&nbsp;&nbsp;<a href="javascript:hideEnableDisableStatusMsg();"><img title="{/literal}{ts}close{/ts}{literal}" src="' +resourceBase+'i/close.png"/></a>';
		cj( '#enableDisableStatusMsg' ).show( ).html( successMsg );
	    }
    });
    
    //change row class and show/hide action links.
    modifySelectorRow( recordID, op );
    
    //modify action link html        
    modifyLinkAttributes( recordID, op );
}
</script>
{/literal}