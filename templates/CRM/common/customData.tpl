{literal}
<script type="text/javascript">

function buildCustomData( type, subType, subName, cgCount, groupID, isMultiple )
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
	{literal}

	if ( !cgCount ) {
		cgCount = 1;
		var prevCount = 1;		
	} else if ( cgCount >= 1 ) {
		var prevCount = cgCount;	
		cgCount++;
	}

	dataUrl = dataUrl + '&cgcount=' + cgCount;


	if ( isMultiple ) {
		var fname = '#custom_group_' + groupID + '_' + prevCount;
		cj("#add-more-link-"+prevCount).hide();
	} else {
		if ( subName ) {		
			var fname = '#customData' + subName ;
		} else {
			var fname = '#customData';
		}		
	}
	
	var response = cj.ajax({
						url: dataUrl,
						async: false
					}).responseText;

	cj( fname ).html( response );
}

</script>
{/literal}
