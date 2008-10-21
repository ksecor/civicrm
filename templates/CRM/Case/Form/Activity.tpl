{if $caseAction }
   {include file="CRM/Case/Form/Activity/$caseAction.tpl"}
{else}
<fieldset><legend id="caseBlockTitle">{ts}Case Action{/ts}</legend>
<table class="form-layout">
   <tr><td class="label" width="30%">{$form.case_action.label}</td><td>{$form.case_action.html}</td>        
</table>

{* case block is injected here when an case action is selected. *}
<div id="caseBlock"></div>
</fieldset>

{literal}
<script type="text/javascript">
var val = document.getElementById('case_action').options[document.getElementById('case_action').selectedIndex].value;
buildCaseBlock( val );

function buildCaseBlock( caseAction )
{
	if ( caseAction ) {
    	var dataUrl = {/literal}"{crmURL p='civicrm/case/activity' q='caseaction=case_action&snippet=4'}"{literal};
        dataUrl = dataUrl.replace('case_action', caseAction);
        dataUrl = dataUrl.replace('&amp;', '&');

  	    dojo.byId('caseBlockTitle').innerHTML = document.getElementById('case_action').options[document.getElementById('case_action').selectedIndex].text;
	} else {
        dojo.byId('caseBlockTitle').innerHTML = 'Case Activity';
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
                       dojo.parser.parse('caseBlock');
                       executeInnerHTML( 'caseBlock' );
                     }
                }
        });
}
</script>
{/literal}

{/if}