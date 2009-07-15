{literal}
<script type="text/javascript" >
cj( function( ) {
{/literal}
{if $generateAjaxRequest}
    {foreach from=$ajaxRequestBlocks key="blockName" item="instances"}
      //reset count to 1 since each time counter get increamented.
      cj( "#hidden_" + "{$blockName}" + "_Instances" ).val( 1 );
        {foreach from=$instances key="instance" item="active"}
             buildAdditionalBlocks( '{$blockName}', '{$className}' );
        {/foreach}  	
    {/foreach}
{/if}
{literal}
});

function buildAdditionalBlocks( blockName, className ) {

  var previousBlockCount = cj( "#hidden_" + blockName + "_Instances" ).val( ).substr(-1);
  var currentBlockCount  = parseInt( previousBlockCount ) + 1;
  var dataUrl = null;
  if ( className == 'CRM_Contact_Form_Contact' ) {
     dataUrl = {/literal}"{crmURL p='civicrm/contact/add' h=0 q='snippet=4'}"{literal} + '&block=' + blockName + '&count=' + currentBlockCount;{/literal}
 
     {if $qfKey}    
     dataUrl += "&qfKey={$qfKey}";
     {/if}
     {literal}
  } else if ( className == 'CRM_Event_Form_ManageEvent_Location' && currentBlockCount <= 2 ) {
     dataUrl = {/literal}"{crmURL p='civicrm/event/manage' h=0 q='snippet=4'}"{literal} + '&subPage=Location&block=' + blockName + '&count=' + currentBlockCount;
  }
  
  if ( !dataUrl ) {
     return;
  }
    
  blockId = (cj('#' + blockName + '_Block_'+ previousBlockCount ).html()) ? previousBlockCount : 1;  
  var fname = '#' + blockName + '_Block_'+ blockId;

  cj('#addMore' + blockName ).hide();
  cj.ajax({ 
            url     : dataUrl,   
            async   : false,
            success : function(html){
                         var html = html.split('<!-Add->',2);
                         cj(fname).after(html[1]);
                      }
         });
  cj( "#hidden_" + blockName + "_Count" ).val( currentBlockCount );

  //build the hidden block instance string used in post.
  var prevousBlockCntStr = cj( "#hidden_" + blockName + "_Instances" ).val( );
  var currentBlockCntStr = prevousBlockCntStr + ',' + currentBlockCount;
  cj( "#hidden_" + blockName + "_Instances" ).val( currentBlockCntStr );

  if ( blockName == 'Address' ) cj("#addressBlock").show( );

}

//select single is_bulk & is_primary
function singleSelect( blockName, blockId, flagName ) {
  var instances = cj( "#hidden_" + blockName + "_Instances" ).val( ).split(',');
  var instance  = 1;
  cj(instances).each( function( ) { 
    if ( instance != blockId ) {
        cj( '#'+blockName+'_'+instance+'_'+flagName).attr( 'checked', false );
    }
    instance++;	
  });
}

function removeBlock( blockName, blockId ) {
 //update string for removing block instance from qf during post.
 var updateStr = cj( "#hidden_" + blockName + "_Instances" ).val( ).replace( ',' + blockId, '' );
 cj( "#hidden_" + blockName + "_Instances" ).val(  updateStr );
 
 //unset block from html
 cj( "#"+ blockName + "_Block_" + blockId ).remove();
}
</script>
{/literal}
