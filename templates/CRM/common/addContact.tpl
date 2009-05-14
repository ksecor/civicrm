<script type="text/javascript">
{literal}

function buildContact( count, pref )  {
    if ( count > 1 ) {
        prevCount = count - 1;
        {/literal}{if $action eq 1  OR $action eq 2}{literal}
            hide( pref + '_' + prevCount + '_show');
        {/literal}{/if}{literal}
    }

    // do not recreate if combo widget is already created
    if ( dijit.byId( pref + '[' + count + ']' ) ) {
        return;
    }

    var context = {/literal}"{$context}"{literal}
    var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4&count='}"{literal} + count + '&' + pref + '=1&context=' + context;

    {/literal}
    {if $urlPathVar}
        dataUrl = dataUrl + '&' + '{$urlPathVar}'
    {/if}
    {if $qfKey}
        dataUrl = dataUrl + '&qfKey=' + '{$qfKey}'
    {/if}
    {literal}
    
    var response = cj.ajax({
            url: dataUrl,
            async: false
        }).responseText;

    cj( '#' + pref + '_' + count ).html( response );
    dojo.parser.parse( pref + '_' + count );
}


{/literal}
</script>

