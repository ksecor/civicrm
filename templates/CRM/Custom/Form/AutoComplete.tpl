{literal}
<script type="text/javascript">
cj( function( ) {
    var url       = "{/literal}{$customUrls.$element_name}{literal}";
    var custom    = "{/literal}#{$element_name}{literal}";
    var custom_id = "{/literal}#{$element_name}_id{literal}";
    cj(custom).autocomplete( url, { width : 250, selectFirst : false
    }).result( 
        function(event, data, formatted) { 
            cj( custom_id ).val( data[1] );
        }
    );
});
</script>
{/literal}