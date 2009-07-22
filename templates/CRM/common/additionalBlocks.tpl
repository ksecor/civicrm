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

//select single for is_bulk & is_primary
function singleSelect( object ) {
	var element = object.split( '_', 3 );
	if ( element['0'] == 'Address' && element['2'] == 'IsBilling' ) {
		currentElement = 'td#' + element['0'] + '-Primary-html Input';
	} else {
		currentElement = 'td#' + element['0'] + '-' + element['2'].slice('2')+'-html Input';
	}
	//element to check for radio / checkbox
	var elementChecked =  cj( '#' + object ).attr('checked');
	if ( elementChecked && confirm ( 'Do you want to make this '+ element['0'] + ' as '+ element['2'].slice('2') + '?' ) ) {
		cj( currentElement ).each( function( ) { 
			selectedElement = cj(this).attr('id').split( '_', 3); 
			if ( cj(this).attr('id') != object && selectedElement['2'] == element['2']) {
				cj(this).attr( 'checked', false );
			}
		});
	} else {
		cj( '#' + object ).attr( 'checked', false );
	}
	
	//check if non of elements is set Primary.
	if( element['2'].slice('2') == 'Primary' || element['2'].slice('2') == 'Login' ) {
		primary = false;
		cj( 'td#' + element['0'] + '-' + element['2'].slice('2') + '-html Input').each( function( ) { 
			selectedElement = cj(this).attr('id').split( '_', 3); 
			if ( cj(this).attr( 'checked' ) && selectedElement['2'] == element['2']) {
				primary = true;				
			}
		});
		
		if( ! primary ) {
			alert('At least one ' + element['0'] +' must be set for '+ element['2'].slice('2') +'!');
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
	if( blockName == 'Address' ) {
		bID = cj( "#addressBlock div:last" ).attr('id').split( '_', 3);
		if( bID['2'] ) {
			cj( '#addMoreAddress' + bID['2'] ).show();
		} else {
			cj( '#addMoreAddress1' ).show();
		}
	}
}
</script>
{/literal}