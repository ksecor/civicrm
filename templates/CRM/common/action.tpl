{literal}
<script type="text/javascript">

cj(document).ready(function(){
    var oldId    = null;
    var oldcolor = null;

    // Hiding action menu while clicking outside
    cj("#container").click(function(){
        cj(this).toggleClass("slide-btn");
        cj("#panel_"+oldId).hide("slow");
    });
  
    // Effects for action menu
    cj(".btn-slide").toggle(
                        function() {
                                    var currentId = cj(this).attr('id');
                                    if ( currentId != oldId ){
                                        cj("#panel_"+currentId).slideDown("slow");
                                        cj("#panel_"+oldId).hide("slow");
                                        }
                                    cj("#panel_"+currentId).slideDown("slow");
                                    cj(this).toggleClass("slide-btn");
                                    oldId = currentId;
                                    },
           			    function() {
                                    var currentId = cj(this).attr('id');
                                    if ( currentId != oldId ){
                                        cj("#panel_"+currentId).slideDown("slow");
                                        cj("#panel_"+oldId).hide("slow");
                                       }
                                    cj(this).toggleClass("slide-btn");
                                    cj("#panel_"+oldId).hide("slow");
                                    oldId = currentId;
    });

   // Setting Background Color to selected link
    cj("#crm-container .panel li").hover(
				    function() {
      					        oldcolor = cj(this).css('background-color');
                                cj(this).css('background-color', '#3399FF');
                                cj(this).find("a").css('color', '#FFFFFF');
				    },
				    function() {
					            cj(this).css('background-color', oldcolor);
                                cj(this).find("a").css('color', '#333333');
    });

    //Setting complete block clickable.
    cj("#crm-container .panel li").click(function(){
        var scriptText = cj(this ).find("a").attr("js");
        if ( scriptText ) {      
                var scriptText = scriptText.split('\'');
                 if( confirm( scriptText[1] ) ) {
      	            window.location=cj(this).find("a").attr("href");
                   }
          } else {
            window.location=cj(this).find("a").attr("href");
          }
    });
});
</script>
{/literal}