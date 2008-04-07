{*common template for compose mail*}
<dl>
<dt>{$form.template.label}</dt><dd>{$form.template.html}</dd>
  <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
  <dt>{$form.token1.label}</dt><dd>{$form.token1.html}</dd>
  <dt>{$form.text_message.label}</dt><dd>{$form.text_message.html}</dd></dl>
  <dt>{$form.token2.label}</dt><dd>{$form.token2.html}</dd>
  <dl class="html-adjust">
  <dt>{$form.html_message.label}</dt><dd id="dojoEditor" class ="tundra">{$form.html_message.html}</dd>
</dl>
<div class="spacer"></div>
<div id="editMessageDetails">
<dl id="updateDetails" >
    <dt>&nbsp;</dt><dd>{$form.updateTemplate.html}&nbsp;{$form.updateTemplate.label}</dd>
</dl><dl>
    <dt>&nbsp;</dt><dd>{$form.saveTemplate.html}&nbsp;{$form.saveTemplate.label}</dd>
</dl>
</div>
<div id="saveDetails">
<dl>
    <dt>{$form.saveTemplateName.label}</dt><dd>{$form.saveTemplateName.html}</dd>
</dl>
</div>

{literal}
<script type="text/javascript" >

    function selectValue( val )
    {
        if ( !val ) {
	    return;
        }

	var dataUrl = {/literal}"{crmURL p='civicrm/ajax/template' q='tid='}"{literal} + val;
        
        var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
        timeout: 5000, //Time in milliseconds
        handle: function(response, ioArgs){
                if(response instanceof Error){
                        if(response.dojoType == "cancel"){
                                //The request was canceled by some other JavaScript code.
                                console.debug("Request canceled.");
                        }else if(response.dojoType == "timeout"){
                                //The request took over 5 seconds to complete.
                                console.debug("Request timed out.");
                        }else{
                                //Some other error happened.
                                console.error(response);
                        }
                } else {
	           res = response.split('^A');

		   //set text message
		   document.getElementById("text_message").value = res[0];
           //set html message
           dijit.byId('html_message').setValue( res[1] ); 
		   // set subject	
		   document.getElementById("subject").value = res[2];
               }
         }
      });
    }


     function verify( select )
     {
	if ( document.getElementsByName("saveTemplate")[0].checked  == false) {
	    document.getElementById("saveDetails").style.display = "none";
	}

        document.getElementById("editMessageDetails").style.display = "block";

	var templateExists = true;
	if ( document.getElementById('template') == null ) {
	    templateExists = false;
	}

	if ( templateExists && document.getElementById('template').value ) {
	    document.getElementById("updateDetails").style.display = '';
	} else {
  	    document.getElementById("updateDetails").style.display = 'none';
	}

	document.getElementById("saveTemplateName").disabled = false;
     }
   
     function showSaveDetails(chkbox) 
     {
	    if (chkbox.checked) {
	        document.getElementById("saveDetails").style.display = "block";
	        document.getElementById("saveTemplateName").disabled = false;
	    } else {
	        document.getElementById("saveDetails").style.display = "none";
	        document.getElementById("saveTemplateName").disabled = true;
	    }
     }

    document.getElementById("saveDetails").style.display = "none";
    document.getElementById("editMessageDetails").style.display = "none";
    dojo.connect( dijit.byId('html_message'), 'onload', 'setHTMLMessage')
    dojo.connect( dijit.byId('html_message'), 'onsubmit', 'getHTMLMessage')
           
    function tokenReplText ( ){
         var token = document.getElementById("token1").options[document.getElementById("token1").selectedIndex].text;
         document.getElementById("text_message").value =  document.getElementById("text_message").value + token;
     }   
    function tokenReplHtml ( ){
         var token2 = document.getElementById("token2").options[document.getElementById("token2").selectedIndex].text;
         var message = dijit.byId("html_message").getValue() + token2;
         dijit.byId('html_message').setValue( message );
     }
     function getHTMLMessage ( ) {
	document.{/literal}{$form.formName}{literal}.hmsg.value = dijit.byId("html_message").getValue();	
     } 	
  	function setHTMLMessage ( ) {
        var message_html  = {/literal}'{$message_html}'{literal};
        dijit.byId('html_message').setValue( message_html );
     } 
 
</script>
{/literal}
