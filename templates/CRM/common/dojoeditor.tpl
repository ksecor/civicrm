
<script type="text/javascript">
{literal}
	 dojo.connect( dijit.byId("{/literal}{$field}{literal}"), 'onload', 'setHTMLMessage')
   	 dojo.connect( dijit.byId("{/literal}{$field}{literal}"), 'onsubmit', 'getHTMLMessage')
   
	function getHTMLMessage ( ) {
		document.{/literal}{$form.formName}{literal}.hvalue.value = dijit.byId("{/literal}{$field}{literal}").getValue();
     	} 	
  	function setHTMLMessage ( ) {
	        var d  = {/literal}'{$description}'{literal};
        	dijit.byId("{/literal}{$field}{literal}").setValue( d );
     	} 
{/literal}
</script>