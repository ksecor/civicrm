{* handle common enable/disable actions *}
<span id="enableDisableStatusMsg" class="success-status" style="display:none;"></span>
{literal}
<script type="text/javascript">
function modifyLinkAttributes( recordID, op ) {
    //we changed record from enable to disable
    if ( op == 'enable-disable' ) {
        var fieldID     = "#row_"+ recordID + " a." + "disable-action";
        var operation   = "disable-enable";
        var htmlContent = {/literal}'{ts}Enable{/ts}'{literal};
        var newClass    = 'enable-action';
        var newTitle    = {/literal}'{ts}Enable{/ts}'{literal};
	var newText     = {/literal}'{ts}Inactive{/ts}'{literal};
    } else if ( op == 'disable-enable' ) {
        var fieldID     = "#row_"+ recordID + " a." + "enable-action";
        var operation   = "enable-disable";
        var htmlContent = {/literal}'{ts}Disable{/ts}'{literal};
        var newClass    = 'disable-action';
        var newTitle    = {/literal}'{ts}Disable{/ts}'{literal};
	var newText     = {/literal}'{ts}Active{/ts}'{literal};
    }

    //change html
    cj( fieldID ).html( htmlContent ); 	

    //change title
    cj( fieldID ).attr({title:newTitle});

    //need to update js - change op from js to new allow operation. 
    var updatedJavaScript = cj( fieldID ).attr("onClick").replace( op, operation );

    //set updated js
    cj( fieldID ).attr({ onClick : updatedJavaScript });  

    //set the updated status
    var fieldStatus = "#row_"+ recordID + "_status";
    cj( fieldStatus ).text( newText );

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

function enableDisable( recordID, recordBAO, op ) {
    var postUrl = {/literal}"{crmURL p='civicrm/ajax/statusmsg' h=0 }"{literal};
    cj.post( postUrl, { recordID: recordID, recordBAO: recordBAO, op: op  }, function( statusMessage ) {
       if ( statusMessage.status ) {
	  var confirmMsg = statusMessage.status + '&nbsp;<a href="javascript:saveEnableDisable( ' + recordID + ',\'' + recordBAO + '\'' + ', \'' + op + '\'' + ' );" style="text-decoration: underline;">{/literal}{ts}Yes{/ts}{literal}</a>&nbsp;&nbsp;&nbsp;<a href="javascript:hideEnableDisableStatusMsg();" style="text-decoration: underline;">{/literal}{ts}No{/ts}{literal}</a>';

    	   cj( '#enableDisableStatusMsg' ).show( ).html( confirmMsg );
       }
   }, 'json' );
}

//check is server properly processed post.
var responseFromServer = false; 

function noServerResponse( ) {
    if ( !responseFromServer ) { 
        var serverError =  '{/literal}{ts}There is no response from server therefore selected record is not updated.{/ts}{literal}'  + '&nbsp;&nbsp;<a href="javascript:hideEnableDisableStatusMsg();"><img title="{/literal}{ts}close{/ts}{literal}" src="' +resourceBase+'i/close.png"/></a>';
        cj( '#enableDisableStatusMsg' ).show( ).html( serverError ); 
    }
}

function saveEnableDisable( recordID, recordBAO, op ) {
    cj( '#enableDisableStatusMsg' ).hide( );

    var postUrl     = {/literal}"{crmURL p='civicrm/ajax/ed' h=0 }"{literal};
    var statusMsg   = '{/literal}{ts}The selected record has been disabled.{/ts}{literal}';

    if ( op == 'disable-enable' ) {
        statusMsg = '{/literal}{ts}The selected record has been enabled.{/ts}{literal}';
    } 

    //post request and get response
    cj.post( postUrl, { recordID: recordID, recordBAO: recordBAO, op:op  }, function( html ){
        responseFromServer = true;      
        var resourceBase   = {/literal}"{$config->resourceBase}"{literal}; 

        var successMsg =  '{/literal}{ts}There is some error occurred in AJAX post therefore selected record is not updated.{/ts}{literal}' + '&nbsp;&nbsp;<a href="javascript:hideEnableDisableStatusMsg();"><img title="{/literal}{ts}close{/ts}{literal}" src="' +resourceBase+'i/close.png"/></a>';


        //this is custom status set when record update success.
        if ( html.status == 'record-updated-success' ) {
            var successMsg =  statusMsg + '&nbsp;&nbsp;<a href="javascript:hideEnableDisableStatusMsg();"><img title="{/literal}{ts}close{/ts}{literal}" src="' +resourceBase+'i/close.png"/></a>';

            //change row class and show/hide action links.
            modifySelectorRow( recordID, op );

            //modify action link html        
            modifyLinkAttributes( recordID, op ); 
        } 

        cj( '#enableDisableStatusMsg' ).show( ).html( successMsg );
        }, 'json' );

        //if no response from server give message to user.
        setTimeout( "noServerResponse( )", 1500 ); 
    }
    </script>
    {/literal}
