{assign var="customGreeting"        value=$n|cat:"_custom"}
{assign var="greetingDisplay" value=$n|cat:"_display"}
     <table class="form-layout-compressed">
           <tr id="greetings">     
                <td id="{$n}">{$form.$n.html|crmReplace:class:big}</td>
                 <td class="description" id="{$customGreeting}" style="display:none;">
                     {$form.$customGreeting.label}&nbsp;&nbsp;&nbsp;
                     {$form.$customGreeting.html|crmReplace:class:big}
                  </td>
                  <td id="{$greetingDisplay}" style="font-size:10px;"></td>
           </tr>
    </table> 

{literal}
<script type="text/javascript">
function showCustomized(element){
    var eleHtml  = 'td#'+element+'_custom';
    var selText  = cj('#'+element+' :selected').text();
    if (  selText == 'Customized' ) { 
        cj( eleHtml ).toggle( );
    } else {
        cj( eleHtml ).hide( );
		var inputElement   = 'input#' + element + '_custom';
        var displayElement = '#' + element + '_display';
		cj( inputElement ).val('');
        if ( selText != '- select -' ) {
             cj(displayElement).html('<strong>Display :: </strong>'+cj('#'+element+' :selected').text()).show().fadeOut(5000);
        }
    }
}

cj('tr#greetings td').each(function() { 
	element = cj(this).attr('id');
    if( element == 'addressee' || element == 'email_greeting' || element == 'postal_greeting' ){
	    if ( cj( '#'+element +' :selected' ).text() == 'Customized' ){
		   showCustomized( element );
      	}
     }
});
</script>
{/literal}