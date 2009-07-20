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
    var elementName = "#contact-details tr";
    if ( blockName == 'Address' ) {
        elementName = "#addressBlock div";
    }
    
    var previousInstance = 1;
    cj( elementName ).each( function( ) {
        bID = cj(this).attr('id').split( '_', 3);
        if ( bID[0] == blockName ) {
            previousInstance = bID[2];
        } 
    });

    var currentInstance  = parseInt( previousInstance ) + 1;

    //show primary option if block count = 2
    if ( currentInstance == 2) {
        cj("#" + blockName + '-Primary').show( );
        cj("#" + blockName + '-Primary-html').show( );
    }

    var dataUrl = {/literal}"{crmURL h=0 q='snippet=4'}"{literal} + '&block=' + blockName + '&count=' + currentInstance;;
    
    if ( className == 'CRM_Event_Form_ManageEvent_Location' ) {
        dataUrl += '&subPage=Location';
    }

    {/literal}
    {if $qfKey}    
        dataUrl += "&qfKey={$qfKey}";
    {/if}
    {literal}

    if ( !dataUrl ) {
        return;
    }

    var fname = '#' + blockName + '_Block_'+ previousInstance;

    cj('#addMore' + blockName + previousInstance ).hide( );
    cj.ajax({ 
        url     : dataUrl,   
        async   : false,
        success : function(html){
            var html = html.split('<!-Add->',2);
            cj(fname).after(html[1]);
        }
    });
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
    // check if is_primary is checked, if yes set is primary to first block
    if ( cj( "#"+ blockName + "_" + blockId + "_IsPrimary").attr('checked') ) {
        cj( "#"+ blockName + "_1_IsPrimary").attr('checked', true);
    }

    //unset block from html
    cj( "#"+ blockName + "_Block_" + blockId ).remove();
	
	//cj('#addMore' + blockName ).show();
}
</script>
{/literal}