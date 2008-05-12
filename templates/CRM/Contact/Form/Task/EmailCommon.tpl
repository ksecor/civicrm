{*common template for compose mail*}
<dl>
<dt>{$form.template.label}</dt><dd>{$form.template.html}</dd>
  <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
  <dt>{$form.token1.label}</dt><dd>{$form.token1.html}</dd>
  <dt>{$form.text_message.label}</dt><dd>{$form.text_message.html}</dd></dl>
  <dt>{$form.token2.label}</dt><dd>{$form.token2.html}</dd>
  <dl class="html-adjust">
  <dt>{$form.html_message.label}</dt><dd class="editor">{$form.html_message.html}</dd>
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

		xmlHttp=GetXmlHttpObject();
		var url={/literal}"{crmURL p='civicrm/ajax/template' q='tid='}"{literal} + val;
		xmlHttp.onreadystatechange=stateChanged;
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);
	}

    	function stateChanged() 
	{ 
		var editor = {/literal}"{$editor}"{literal}
		if (xmlHttp.readyState==4)
		{ 
			result = (xmlHttp.responseText).split('^A');
			document.getElementById("text_message").innerHTML = result[0] ;
			document.getElementById("subject").value = result[2];
			if ( editor == "fckeditor" ) {
				oEditor = FCKeditorAPI.GetInstance('html_message');
				oEditor.SetHTML( result[1]  );
			} else if ( editor == "tinymce" ) {
			     	tinyMCE.get('html_message').setContent( result[1] );
			} else {	
				document.getElementById("html_message").innerHTML = result[1] ;
			}
		}
	}

	function GetXmlHttpObject()
	{
		var xmlHttp=null;
		try
  		{
  			// Firefox, Opera 8.0+, Safari
			xmlHttp=new XMLHttpRequest();
		}	
		catch (e)
  		{
			// Internet Explorer
  			try
    			{
    				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    			}
 			catch (e)
    			{
    				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    			}
 		 }
		return xmlHttp;
	}
	document.getElementById("editMessageDetails").style.display = "block";

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
            
	function tokenReplText ( )
    	{
        	var token = document.getElementById("token1").options[document.getElementById("token1").selectedIndex].text;
         	document.getElementById("text_message").value =  document.getElementById("text_message").value + token;
    	}
   
    	function tokenReplHtml ( )
    	{
        	var token2 = document.getElementById("token2").options[document.getElementById("token2").selectedIndex].text;
	 	var editor = {/literal}"{$editor}"{literal}
	 	if ( editor == "tinymce" ) {
			var content= tinyMCE.get('html_message').getContent() +token2;
        		tinyMCE.get('html_message').setContent(content);
	 	} else if ( editor == "fckeditor" ) {
			oEditor = FCKeditorAPI.GetInstance('html_message');
			var msg=oEditor.GetHTML() +token2;	
			oEditor.SetHTML( msg );	
	 	} else {
			 document.getElementById("html_message").value =  document.getElementById("html_message").value + token2;
		}
	}
{/literal}
{if $editor eq "fckeditor"}
{literal}
	function FCKeditor_OnComplete( editorInstance )
	{
	 	oEditor = FCKeditorAPI.GetInstance('html_message');
		oEditor.SetHTML( {/literal}"{$message_html}"{literal});
    	}
{/literal}
{/if}
{literal}
</script>
{/literal}
