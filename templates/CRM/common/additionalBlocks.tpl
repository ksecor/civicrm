{literal}
<script type="text/javascript" >
cj( function( ) {
    {/literal}
    {if $generateAjaxRequest}
        {foreach from=$ajaxRequestBlocks key="blockName" item="instances"}
            {foreach from=$instances key="instance" item="active"}
                buildAdditionalBlocks( '{$blockName}', '{$className}' );
            {/foreach}  	
        {/foreach}
    {/if}
    {literal}
});

function buildAdditionalBlocks( blockName, className ) {
    var element = blockName + '_Block_';

    //get blockcount of last element of relevant blockName
    var previousInstance = cj( '[id^="'+ element +'"]:last' ).attr('id').slice( element.length );
    var currentInstance  = parseInt( previousInstance ) + 1;

    //show primary option if block count = 2
    if ( currentInstance == 2) {
        cj("#" + blockName + '-Primary').show( );
        cj("#" + blockName + '-Primary-html').show( );
    }

    var dataUrl = {/literal}"{crmURL h=0 q='snippet=4'}"{literal} + '&block=' + blockName + '&count=' + currentInstance;;

    if ( className == 'CRM_Event_Form_ManageEvent_Location' ) {
        dataUrl = ( currentInstance <= 2 ) ? dataUrl + '&subPage=Location' : '';
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
            cj(fname).after(html);
        }
    });

    if ( blockName == 'Address' ) {
        checkLocation('address_' + currentInstance + '_location_type_id', true );
    }
}

//select single for is_bulk & is_primary
function singleSelect( object ) {
    var element = object.split( '_', 3 );

    var block = (element['0'] == 'Address') ? 'Primary' : element['2'].slice('2');
    var execBlock  = '#' + element['0'] + '-' + block + '-html Input[id*="' + element['2'] + '"]';

    //element to check for checkbox
    var elementChecked =  cj( '#' + object ).attr('checked');
    if ( elementChecked ) {
        cj( execBlock ).each( function() {
            if ( cj(this).attr('id') != object ) {
                cj(this).attr( 'checked', false );
            }
        });
    } else {
        cj( '#' + object ).attr( 'checked', false );
    }

	//check if non of elements is set Primary / Allowed to Login.
	if( cj.inArray( element['2'].slice('2'), [ 'Primary', 'Login' ] ) != -1 ) {
		primary = false;
		cj( execBlock ).each( function( ) { 
			if ( cj(this).attr( 'checked' ) ) {
				primary = true;				
			}
		});
		if( ! primary ) {
			cj('#' + object).attr( 'checked', true );
		}
	}
}

function removeBlock( blockName, blockId ) {
    // check if is_primary is checked, if yes set is primary to first block
    if ( cj( "#"+ blockName + "_" + blockId + "_IsPrimary").attr('checked') ) {
        cj( "#"+ blockName + "_1_IsPrimary").attr('checked', true);
    }

    //remove the spacer for address block only.
    if( blockName == 'Address' && cj( "#"+ blockName + "_Block_" + blockId ).prev().attr('class') == 'spacer' ){
        cj( "#"+ blockName + "_Block_" + blockId ).prev().remove();
    }

    //unset block from html
    cj( "#"+ blockName + "_Block_" + blockId ).remove();

    //show the link 'add address' to last element of Address Block
    if ( blockName == 'Address' ) {
        bID = cj( "#addressBlock div:last" ).attr('id').split( '_', 3);
        if ( bID['2'] ) {
            cj( '#addMoreAddress' + bID['2'] ).show();
        } else {
            cj( '#addMoreAddress1' ).show();
        }
    }
}
</script>
{/literal}