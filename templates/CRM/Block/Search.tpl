<div class="block-crm">
{if $config->includeDojo}
<script type="text/javascript"> 
  dojo.require("dojox.data.QueryReadStore");
</script>
{literal}
<script type="text/javascript"> 
function getValue( button )
{
    var element  = document.getElementById('id_search_block');
    var element1 = dijit.byId( 'id_sort_name' );
    var value    = element1.getValue();

    if ( ! value ) {
	var sortValue = document.getElementById( 'id_sort_name' );
	//using httprequest check if there is only one contact and redirect to view page
	var dataUrl = {/literal}"{crmURL p='civicrm/ajax/contact' h=0 q='name='}"{literal} + sortValue.value;
	
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
		    value = response;
		}
	    }
	});
    }

    if ( value ) {
	var url = {/literal}"{crmURL p='civicrm/contact/view' h=0 q='reset=1&cid='}"{literal} + value;
        element.action = url;
    } else {
        var element2             = dojo.byId( 'id_sort_name' );
        element1.valueNode.value = element2.value;
        button.name          = '_qf_Basic_refresh';
    }
}

</script>
{/literal}
{/if}
    <form action="{$postURL}" name="search_block" id="id_search_block" method="post">

    <div dojoType="dojox.data.QueryReadStore" jsId="searchStore" url="{$dataURL}" doClientPaging="false"></div>
    <div class="tundra">
        <input type="hidden" name="contact_type" value="" />
        <input type="text" name="sort_name" id="id_sort_name" value="" dojoType="civicrm.FilteringSelect" store="searchStore" mode="remote" searchAttr="name"  pageSize="10" />
        <br />
        <input type="submit" name="_qf_Edit_next_view" value="{ts}View Contact{/ts}" class="form-submit" onclick="getValue( this );" onSubmit="getValue( this );"/>
        <input type="submit" name="_qf_Basic_refresh" value="{ts}Search{/ts}" class="form-submit" onclick="getValue( this );" onSubmit="getValue( this );"/>
       
        <br />
        <a href="{$advancedSearchURL}" title="{ts}Go to Advanced Search{/ts}">&raquo; {ts}Advanced Search{/ts}</a>
    </div>
    </form>
</div>
