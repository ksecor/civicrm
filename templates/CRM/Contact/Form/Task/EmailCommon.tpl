{*common template for compose mail*}
<table class="form-layout">
    <tr>
        <td class="label-left">{$form.template.label}</td><td>{$form.template.html}</td>
    </tr>
    <tr>
        <td class="label-left">{$form.token1.label} {help id="id-token-text"}</td>
        <td>{$form.token1.html}</td>
    </tr>
    <tr>
        <td colspan="2">{$form.text_message.label} {help id="id-message-text"}<br />
            {$form.text_message.html}
        </td>
    </tr>
    <tr>
        <td class="label-left">{$form.token2.label} {help id="id-token-html"}</td>
        <td>{$form.token2.html}</td>
    </tr>
    <tr>
        <td colspan="2">{$form.html_message.label}<br />
            {if $editor EQ 'textarea'}
                <span class="description">{ts}If you are composing HTML-formatted messages, you may want to enable a WYSIWYG editor (Administer CiviCRM &raquo; Global Settings &raquo; Site Preferences).{/ts}</span><br />
            {/if}
            {$form.html_message.html}
        </td>
    </tr>
</table>

{if ! $noAttach}
    <div class="spacer"></div>
    {include file="CRM/Form/attachment.tpl"}
{/if}

<div class="spacer"></div>

<div id="editMessageDetails">
    <div id="updateDetails" >
        {$form.updateTemplate.html}&nbsp;{$form.updateTemplate.label}
    </div>
    <div>
        {$form.saveTemplate.html}&nbsp;{$form.saveTemplate.label}
    </div>
</div>

<div id="saveDetails">
    {$form.saveTemplateName.label}</dt><dd>{$form.saveTemplateName.html}
</div>

{literal}
<script type="text/javascript" >
var editor = {/literal}"{$editor}"{literal};
	function loadEditor()
	{
		var msg =  {/literal}"{$htmlContent}"{literal};
		if (msg) {
			if ( editor == "fckeditor" ) {
				oEditor = FCKeditorAPI.GetInstance('html_message');
				oEditor.SetHTML( msg );
			} else if ( editor == "tinymce" ) {
			     	tinyMCE.get('html_message').setContent( msg );
			}
		}
	}
		
	

	function showSaveUpdateChkBox()
	{
		if ( document.getElementById('template') == null ) {
			if (document.getElementsByName("saveTemplate")[0].checked){
	    			document.getElementById("saveDetails").style.display = "block";
 				document.getElementById("editMessageDetails").style.display = "block";
			} else {
				 document.getElementById("saveDetails").style.display = "none";
 				 document.getElementById("editMessageDetails").style.display = "none";
			}
			return;
		}
	
		if ( document.getElementsByName("saveTemplate")[0].checked && document.getElementsByName("updateTemplate")[0].checked == false  ) {
			document.getElementById("updateDetails").style.display = "none";
		}else if ( document.getElementsByName("saveTemplate")[0].checked && document.getElementsByName("updateTemplate")[0].checked ){
			document.getElementById("editMessageDetails").style.display = "block";	
			document.getElementById("saveDetails").style.display = "block";	
		}else if ( document.getElementsByName("saveTemplate")[0].checked == false && document.getElementsByName("updateTemplate")[0].checked ){
			document.getElementById("saveDetails").style.display = "none";
 			document.getElementById("editMessageDetails").style.display = "block";
		} else {
			document.getElementById("saveDetails").style.display = "none";
 			document.getElementById("editMessageDetails").style.display = "none";
		}

	}
	function selectValue( val )
   	{
        	if ( !val ) {
		document.getElementById("text_message").value ="";
		document.getElementById("subject").value ="";
			if ( editor == "fckeditor" ) {
				oEditor = FCKeditorAPI.GetInstance('html_message');
				oEditor.SetHTML('');
			} else if ( editor == "tinymce" ) {
			     	tinyMCE.get('html_message').setContent('');
			} else {	
				document.getElementById("html_message").value = '' ;
			}
		  	return;
        	}
 
		var dataUrl = {/literal}"{crmURL p='civicrm/ajax/template' h=0 q='tid='}"{literal} + val;
        
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
	           	result = response.split('^A');
			document.getElementById("text_message").value =  result[0] ;
			document.getElementById("subject").value = result[2];
			if ( editor == "fckeditor" ) {
				oEditor = FCKeditorAPI.GetInstance('html_message');
				oEditor.SetHTML( result[1]  );
			} else if ( editor == "tinymce" ) {
			     	tinyMCE.get('html_message').setContent( result[1] );
			} else {	
				document.getElementById("html_message").value = result[1] ;
			}
		}
		}});
	
	}

 
  	document.getElementById("editMessageDetails").style.display = "block";

      	function verify( select )
      	{
		if ( document.getElementsByName("saveTemplate")[0].checked  == false ) {
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

	
       	showSaveUpdateChkBox();
            
	function tokenReplText ( )
    	{
        	var token = document.getElementById("token1").options[document.getElementById("token1").selectedIndex].text;
         	document.getElementById("text_message").value =  document.getElementById("text_message").value + token;
		verify();
    	}
   
    	function tokenReplHtml ( )
    	{
        	var token2 = document.getElementById("token2").options[document.getElementById("token2").selectedIndex].text;
	 	var editor = {/literal}"{$editor}"{literal};
	 	if ( editor == "tinymce" ) {
			var content= tinyMCE.get('html_message').getContent() +token2;
        		tinyMCE.get('html_message').setContent(content);
	 	} else if ( editor == "fckeditor" ) {
			oEditor = FCKeditorAPI.GetInstance('html_message');
			var msg=oEditor.GetHTML() + token2;	
			oEditor.SetHTML( msg );	
	 	} else {
			 document.getElementById("html_message").value =  document.getElementById("html_message").value + token2;
		}
		verify();
	}
{/literal}
{if $editor eq "fckeditor"}
{literal}
	function FCKeditor_OnComplete( editorInstance )
	{
	 	oEditor = FCKeditorAPI.GetInstance('html_message');
		oEditor.SetHTML( {/literal}"{$message_html}"{literal});
		loadEditor();	
		editorInstance.Events.AttachEvent( 'OnFocus',verify ) ;
    	}
{/literal}
{/if}
{if $editor eq "tinymce"}
{literal}
	function customEvent() {
		loadEditor();
		tinyMCE.get('html_message').onKeyPress.add(function(ed, e) {
 		verify();
		});
	}

tinyMCE.init({
	oninit : "customEvent"
});

{/literal}
{/if}
{literal}
</script>
{/literal}
