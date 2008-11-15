{literal}
<script type="text/javascript">
//cj('#customData').hide();
//buildCustomData( );

function buildCustomData( type, subName, subType )
{
	var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4&type='}"{literal} + type; 

	if ( subType ) {
		// special case to handle relationship custom data
	    if ( type == 'Relationship' ) {
			subType = subType.replace( '_a_b', '' );
			subType = subType.replace( '_b_a', '' );
	    }
	    
		dataUrl = dataUrl + '&subType=' + subType;
	}

	if ( subName ) {
		dataUrl = dataUrl + '&subName=' + subName;
		cj('#customData' + subName ).show();
	} else {
		cj('#customData').show();		
	}
	
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
		{if $entityID}
			dataUrl = dataUrl + '&entityID=' + '{$entityID}'
		{/if}
		{if $cgCount}
			var cgcount = '{$cgCount}'
		{else}
			var cgcount = 1
		{/if}

		dataUrl = dataUrl + '&cgcount=' + cgcount;

	{literal}
	
	if ( subName ) {		
		cj('#customData' + subName ).load( dataUrl);
	} else {
		cj('#customData').load( dataUrl);
	}

	{/literal}
	{if $isMultiValue }
		createMultiValueLink( cgcount ); 
	{/if}
	{literal}
}

function createMultiValueLink( cgcount ) {
	{/literal}
	{if $groupID}
		groupID = '{$groupID}';
	{/if}

	{literal}

    cj("#add-more-"+ groupID).html('<a href="javascript:createMultipleValues(' + cgcount + ' );">Add More</a>');
}

</script>
{/literal}
