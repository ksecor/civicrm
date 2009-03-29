{include file="CRM/common/jquery.tpl"}
<div class="block-crm">
<script type="text/javascript"> 
{literal}
    function getSearchURLValue( )
    {
        var contactId =  cj( '#contact_id' ).val();
        if ( ! contactId || isNaN( contactId ) ) {
            var sortValue = cj( '#sort_name' ).val();
            if ( sortValue ) { 
                //using xmlhttprequest check if there is only one contact and redirect to view page
                var dataUrl = {/literal}"{crmURL p='civicrm/ajax/contact' h=0 q='name='}"{literal} + sortValue;

                var response = cj.ajax({
                    url: dataUrl,
                    async: false
                    }).responseText;

                contactId = response;
            }
        }

        if ( contactId ) {
            var url = {/literal}"{crmURL p='civicrm/contact/view' h=0 q='reset=1&cid='}"{literal} + contactId;
            document.getElementById('id_search_block').action = url;
        }
    }

    cj( function () {
        var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' h=0 }"{literal};

        cj( '#sort_name' ).autocomplete( contactUrl, {
            width: 200,
            selectFirst: false 
        });
        cj( '#sort_name' ).result(function(event, data, formatted) {
            cj("#contact_id").val(data[1]);
        });	
    });
{/literal}
</script>

    <form action="{$postURL}" name="search_block" id="id_search_block" method="post" >
        <input type="text" class="form-text" id="sort_name" name="sort_name" style="width: 12em;"/>
        <input type="hidden" id="contact_id" value="">
        <input type="submit" value="{ts}Go{/ts}" name="_qf_Basic_refresh" class="form-submit default" onclick="getSearchURLValue( );"/>
        <br />
        <a href="{$advancedSearchURL}" title="{ts}Go to Advanced Search{/ts}">&raquo; {ts}Advanced Search{/ts}</a>
    </form>
</div>
