{if $caseAction }
   {include file="CRM/Case/Form/Activity/$caseAction.tpl"}
{else}
<fieldset><legend>{ts}Case Action{/ts}</legend>
<table class="form-layout">
   <tr><td class="label" width="30%">{$form.case_action.label}</td><td>{$form.case_action.html}</td>        
</table>

{* case block is injected here when an case action is selected. *}
<div id="caseBlock"></div>
<br/>
<table class="form-layout">
   <tr><td width="30%">&nbsp;</td><td>{$form.buttons.html}</td></tr>
</table>
</fieldset>

{literal}
<script type="text/javascript">
function buildCaseBlock( caseAction )
{
	if ( caseAction ) {
    	var dataUrl = {/literal}"{crmURL p='civicrm/case/activity' q='caseaction=case_action&snippet=4'}"{literal};
        dataUrl = dataUrl.replace('case_action', caseAction);
        dataUrl = dataUrl.replace('&amp;', '&');
	} else {
  	    dojo.byId('caseBlock').innerHTML = '';
        return;
	}

        var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
     	sync: true,
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
                     }else{
     		           // on success
                       dojo.byId('caseBlock').innerHTML = response;
                       // this executes any javascript in the injected block
                       executeInnerHTML( 'caseBlock' );
                     }
                }
        });
}
</script>
{/literal}

{/if}