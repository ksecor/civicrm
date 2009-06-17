{* handle common enable/disable actions *}
{literal}
<script type="text/javascript">

//inital show/hide enable/disable action links.
initialShowHideLinks( );

function initialShowHideLinks( ) {
{/literal}
 {foreach from=$rows item=row}
    {literal}
    var recordID = {/literal}{$row.id}{literal};
    {/literal} 
    {if $row.is_active eq 1}{* row is enabled so hide enable action link*}
        {literal}
         cj("#row_"+ recordID + " a.32").hide( );
        {/literal}
    {else}{* row is disabled so hide disabled action link*}
        {literal}
        cj("#row_"+ recordID + " a.64").hide( );
        {/literal}
    {/if}
 {/foreach}
{literal}
}

function modifySelectorRow( recordID, isActive ) {
 if ( isActive ) {
    //we are enabling record.
    if ( cj( "#row_" + recordID ).hasClass( "even-row" ) ) {
       cj( "#row_" + recordID ).removeClass( );
       cj( "#row_" + recordID ).addClass("even-row");
    } else if ( cj( "#row_" + recordID ).hasClass( "odd-row" ) ) {
       cj( "#row_" + recordID ).removeClass( );
       cj( "#row_" + recordID ).addClass("odd-row");
    }
    //need to hide enable action link.
    cj("#row_"+ recordID + " a.32").hide( );	

    //show disable action link
    cj("#row_"+ recordID + " a.64").show( );
 } else {
    //we are disabling record.
    cj( "#row_" + recordID ).addClass("disabled");
   
    //need to hide disable action link.
    cj("#row_"+ recordID + " a.64").hide( );
   
    //show enable action link
    cj("#row_"+ recordID + " a.32").show( );
 }
}

function hideEnableDisableStatusMsg( ) {
  cj( '#enableDisableStatusMsg' ).hide( );
}

function enableDisable( recordID, recordDAO, isActive ) {
 
 //hack to pass false.
 var statusMsg = '{/literal}{ts}Are you sure you want to enable this record?{/ts}{literal}';
 if ( !isActive ) {
    isActive  = 0;
    statusMsg = '{/literal}{ts}Are you sure you want to disable this record?{/ts}{literal}';
 }

 var confirmMsg =  statusMsg + '&nbsp; <a href="javascript:saveEnableDisable( ' + recordID + ',\'' + recordDAO + '\'' + ', '+ isActive +'  );" style="text-decoration: underline;">{/literal}{ts}Yes{/ts}{literal}</a>&nbsp;&nbsp;&nbsp;<a href="javascript:hideEnableDisableStatusMsg();" style="text-decoration: underline;">{/literal}{ts}No{/ts}{literal}</a>';
        cj( '#enableDisableStatusMsg' ).show( ).html( confirmMsg );
}

function saveEnableDisable( recordID, recordDAO, isActive ) {
        var postUrl = {/literal}"{crmURL p='civicrm/ajax/ed' h=0 }"{literal};

        var statusMsg = '{/literal}{ts}The selected record has been disabled.{/ts}{literal}';
        if ( isActive ) {
           statusMsg = '{/literal}{ts}The selected record has been enabled.{/ts}{literal}';
        }

        cj.ajax({
          type: "POST",
          data:  "recordID=" + recordID + "&recordDAO=" + recordDAO + "&isActive=" + isActive,    
          url: postUrl,
 
          success: function(html){
              var resourceBase   = {/literal}"{$config->resourceBase}"{literal};
              var successMsg =  statusMsg + '&nbsp;&nbsp;<a href="javascript:hideEnableDisableStatusMsg();"><img title="{/literal}{ts}close{/ts}{literal}" src="' +resourceBase+'i/close.png"/></a>';
              cj( '#enableDisableStatusMsg' ).show( ).html( successMsg );
          }
        });

        //change row class and show/hide action links.
        modifySelectorRow( recordID, isActive );
 }
</script>
{/literal}