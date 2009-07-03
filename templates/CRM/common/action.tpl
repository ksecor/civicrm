{literal}
<script type="text/javascript">

cj( function( ) {
    var funVariable = 'bind';
    {/literal}
    {if $isSnippet }
        cj(".btn-slide").die('click');
        funVariable = 'live';
    {/if}
    {literal}
    var currentId  = null;
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
  
    eval('cj(".btn-slide").'+ funVariable +'( "click", function( ) {currentId = cj(this).find("ul").attr("id");cj(".btn-slide").each(function( ) {if ( currentId == cj(this).find("ul").attr("id") ) {cj(this).find("ul").show( );hideAction = true;} else {cj(this).find("ul").hide( );}});});');

});
</script>
{/literal}