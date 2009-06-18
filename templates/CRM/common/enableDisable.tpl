{* handle common enable/disable actions *}
{literal}
<script type="text/javascript">
function modifyLinkAttributes( recordID, op ) {
 //we changed record from enable to disable
 if ( op == 'enable-disable' ) {
    //change html title
    cj( "#row_"+ recordID + " a.disable-action" ).html( 'Enable'); 	
   
    //change title
    cj("#row_"+ recordID + " a.disable-action").attr({title:"Enable Event"});

    //need to update js - change op from js since 
    var updatedJavaScript = cj("#row_"+ recordID + " a.disable-action").attr("onClick").replace( "enable-disable", "disable-enable" );
  
    //set updated js
    cj("#row_"+ recordID + " a.disable-action").attr({ onClick : updatedJavaScript });

    //finally change class to enable-action.
    cj("#row_"+ recordID + " a.disable-action").attr({class:"enable-action"});
 } else if ( op == 'disable-enable' ) {
    //we changed record from enable to disable.
    //change html title
    cj("#row_"+ recordID + " a.enable-action").html('Disable'); 	
   
    //change title
    cj("#row_"+ recordID + " a.enable-action").attr({title:"Disable Event"});

    //need to update js - change op from js
    var updatedJavaScript = cj("#row_"+ recordID + " a.enable-action").attr("onClick").replace( "disable-enable", "enable-disable" );

    //set updated js
    cj("#row_"+ recordID + " a.enable-action").attr({ onClick : updatedJavaScript });

    //finally change class to disable-action.
    cj("#row_"+ recordID + " a.enable-action").attr({class:"disable-action"});
 } 
}

function modifySelectorRow( recordID, op ) {
 if ( op == "disable-enable" ) {
    //we are enabling record.
    if ( cj( "#row_" + recordID ).hasClass( "even-row" ) ) {
       cj( "#row_" + recordID ).removeClass( );
       cj( "#row_" + recordID ).addClass("even-row");
    } else if ( cj( "#row_" + recordID ).hasClass( "odd-row" ) ) {
       cj( "#row_" + recordID ).removeClass( );
       cj( "#row_" + recordID ).addClass("odd-row");
    }
 } else if ( op == "enable-disable" )  {
    //we are disabling record.
    cj( "#row_" + recordID ).addClass("disabled");
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