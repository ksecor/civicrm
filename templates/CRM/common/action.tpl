{literal}
<script type="text/javascript">

cj( function( ) {
    var currentId  = null;
    var oldcolor   = null;
    var hideAction = false;
   
     // Hiding action menu while clicking outside
    cj(document).click(function( ) {
     if ( !hideAction ) {
           cj(".btn-slide").each(function( ) {
                 cj(this).find("ul").hide( ); 
            });
        }
        hideAction = false;
    });
  
    // Effects for action menu
    cj(".btn-slide").click(
        function( ) {
            currentId = cj(this).find("ul").attr('id');
            cj(".btn-slide").each(function( ) {
                if ( currentId != cj(this).find("ul").attr('id') ) {
                    cj(this).find("ul").hide( );
                    hideAction = true;
                 }
            });
            
            cj(this).find("ul").toggle( );
        }
    );
    
    // Setting Background Color to selected link
    cj("#crm-container .panel li").hover(
	    function( ) {
		    oldcolor = cj(this).css('background-color');
            cj(this).css('background-color', '#3399FF');
            cj(this).find("a").css('color', '#FFFFFF');
	    },
	    function( ) {
            cj(this).css('background-color', oldcolor);
            cj(this).find("a").css('color', '#333333');
        }
    );
});
</script>
{/literal}