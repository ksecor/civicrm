<div class="block-crm">
{if $config->includeDojo}
{literal}
<script type="text/javascript"> 
    dojo.require("dojox.data.QueryReadStore");
    dojo.require("dojo.parser");
    function getSearchURLValue( )
    {
        var contactId =  dijit.byId( 'id_sort_name' ).getValue();

        if ( ! contactId || isNaN( contactId ) ) {
            var sortValue = dojo.byId( 'id_sort_name' );
            //using xmlhttprequest check if there is only one contact and redirect to view page
            var dataUrl = {/literal}"{crmURL p='civicrm/ajax/contact' h=0 q='name='}"{literal} + sortValue.value;

            var response = cj.ajax({
        						url: dataUrl,
        						async: false
        					}).responseText;
        	
        	contactId = response;
        }

        if ( contactId ) {
            if ( isNaN ( contactId ) ) {
                dijit.byId( 'id_sort_name' ).valueNode.value = contactId;
            } else {
                var url = {/literal}"{crmURL p='civicrm/contact/view' h=0 q='reset=1&cid='}"{literal} + contactId;
                document.getElementById('id_search_block').action = url;
            }
        } else {
            dijit.byId( 'id_sort_name' ).valueNode.value = document.getElementById( 'id_sort_name' ).value;
        }

    }

</script>
{/literal}
{/if}
    <form action="{$postURL}" name="search_block" id="id_search_block" method="post" >

    <div dojoType="dojox.data.QueryReadStore" jsId="searchStore" url="{$dataURL}" doClientPaging="false"></div>
    <div class="tundra">
        <input type="text" name="sort_name" id="id_sort_name" value="" dojoType="civicrm.FilteringSelect" store="searchStore" mode="remote" searchAttr="name" pageSize="10" style="width: 10em;" />
	<input type="submit" name="_qf_Basic_refresh" value="{ts}Go{/ts}" class="form-submit"  onclick="getSearchURLValue( );"/>
        <br />
        <a href="{$advancedSearchURL}" title="{ts}Go to Advanced Search{/ts}">&raquo; {ts}Advanced Search{/ts}</a>
    </div>
    </form>
</div>
