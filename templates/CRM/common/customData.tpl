{literal}
<script type="text/javascript">
hide('customData');
buildCustomData( );

function buildCustomData( subType )
{
	show('customData');
	
	var type     = "{/literal}{$customDataType}{literal}";
	
	var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4&type='}"{literal} + type;
{/literal}
{if $urlPathVar}
	dataUrl = dataUrl + '&' + '{$urlPathVar}'
{/if}
{if $groupID}
	dataUrl = dataUrl + '&groupID=' + '{$groupID}'
{/if}
{if $qfKey}
        dataUrl = dataUrl + '&qfKey=' + '{$qfKey}'
{/if}
{literal}
	if ( !subType ) {
	   var subType  = "{/literal}{$customDataSubType}{literal}";
	}
	if ( !subName ) {
	   var subName  = "{/literal}{$customDataSubName}{literal}";
	}
	
	if ( subType) {
	    /* special case to handle relationship custom data*/
	    if ( type == 'Relationship' ) {
		subType = subType.replace( '_a_b', '' );
		subType = subType.replace( '_b_a', '' );
	    }
	   dataUrl = dataUrl + '&subType=' + subType + '&subName=' + subName ;	
	}
	
	var entityId  = "{/literal}{$entityId}{literal}";

	if ( entityId ) {
	   dataUrl = dataUrl + '&entityId=' + entityId;	
	}

        var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
        timeout: 5000, //Time in milliseconds
        handle: function(response, ioArgs){
                if(response instanceof Error){
                        if(response.dojoType == "cancel"){
                                //The request was canceled by some other JavaScript code.
                                console.debug("Request canceled.");
                        }else if(response.dojoType == "timeout"){
                                //The request took over 5 seconds to complete.
                                console.debug("Request timed out.");
                        }else{
                                //Some other error happened.
                                console.error(response);
                        }
                } else {
		   // on success
                   dojo.byId('customData').innerHTML = response;

		   {/literal}
		   {if $isMultiValue }
		        createMultiValueLink( ); 
		   {/if}
		   {literal}

		   executeInnerHTML( 'customData' );
	       }
        }
     });


}

function createMultiValueLink( ) {
{/literal}
{if $groupID}
	groupID = '{$groupID}';
{/if}
{literal}

    cj("#add-more-"+ groupID).html('<a href="javascript:createMultipleValues( );">Add More</a>');
}

function createMultipleValues( subType )
{
	show('customData');
	
	var type     = "{/literal}{$customDataType}{literal}";

	var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4&type='}"{literal} + type;
{/literal}
{if $urlPathVar}
	dataUrl = dataUrl + '&' + '{$urlPathVar}'
{/if}
{literal}
	if ( !subType ) {
	   var subType  = "{/literal}{$customDataSubType}{literal}";
	}

	if ( subType) {
	    /* special case to handle relationship custom data*/
	    if ( type == 'Relationship' ) {
		subType = subType.replace( '_a_b', '' );
		subType = subType.replace( '_b_a', '' );
	    }
	   dataUrl = dataUrl + '&subType=' + subType;	
	}
	
	var entityId  = "{/literal}{$entityId}{literal}";

	if ( entityId ) {
	   dataUrl = dataUrl + '&entityId=' + entityId;	
	}

        var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
        timeout: 5000, //Time in milliseconds
        handle: function(response, ioArgs){
                if(response instanceof Error){
                        if(response.dojoType == "cancel"){
                                //The request was canceled by some other JavaScript code.
                                console.debug("Request canceled.");
                        }else if(response.dojoType == "timeout"){
                                //The request took over 5 seconds to complete.
                                console.debug("Request timed out.");
                        }else{
                                //Some other error happened.
                                console.error(response);
                        }
                } else {
		   // on success
		    cj('#customData').append( response );
		    // executeInnerHTML( 'customData' );
		    {/literal}
		    {if $isMultiValue }
		        createMultiValueLink( ); 
		    {/if}
		    {literal}
	       }
        }
     });
}

</script>
{/literal}