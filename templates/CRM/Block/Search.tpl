{include file="CRM/common/jquery.tpl"}
<div class="block-crm">
<script type="text/javascript"> 
{literal}
    jQuery( function ($) {
        var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' h=0 }"{literal};

        $( '#sort_name' ).autocomplete( contactUrl, {
            width: 200,
            delay:200,
            selectFirst: false 
        })
        .result(function(event, data, formatted) {
           document.location={/literal}"{crmURL p='civicrm/contact/view' h=0 q='reset=1&cid='}"{literal}+data[1];
            cj("#contact_id").val(data[1]);// XAV: what's the purpose ?
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
