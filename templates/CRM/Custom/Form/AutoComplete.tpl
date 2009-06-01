 {literal}
	<script type="text/javascript">
   	       var url       = "{/literal}{$dataUrl}{literal}";
	       var custom    = "{/literal}#{$element_name}{literal}";
	       var custom_id = "{/literal}#{$element_name}_id{literal}";
    	       cj(custom).autocomplete( url, { width : 180, selectFirst : false
               }).result( function(event, data, formatted) { cj( custom_id ).val( data[1] );
    	       });
    	       {/literal}
        </script>