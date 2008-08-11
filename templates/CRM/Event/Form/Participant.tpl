{* This template is used for adding/editing/deleting offline Event Registrations *}
{if $showFeeBlock }
   {include file="CRM/Event/Form/EventFees.tpl"}
{elseif $cdType }
  {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
{if $participantMode == 'test' }
    {assign var=registerMode value="TEST"}
{else if $participantMode == 'live'}
    {assign var=registerMode value="LIVE"}
{/if}
{if $participantMode}
<div id="help">
    {ts 1=$displayName 2=$registerMode}Use this form to submit an event registration on behalf of %1. <strong>A %2 transaction will be submitted</strong> using the selected payment processor.{/ts}
</div>
{else}
<fieldset><legend>{if $action eq 1}{ts}New Event Registration{/ts}{elseif $action eq 8}{ts}Delete Event Registration{/ts}{else}{ts}Edit Event Registration{/ts}{/if}</legend>
{/if} 
    {if $action eq 1 AND $paid}
    <div id="help">
	{ts}If you are accepting offline payment from this participant, check <strong>Record Payment</strong>. You will be able to fill in the payment information, and optionally send a receipt.{/ts}
    </div>  
    {/if}
    
    <table class="form-layout">
    {if $action eq 8} {* If action is Delete *}
        <tr><td>
            <div class="messages status">
          	<dl>
        	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          	<dd> 
          	 {ts}WARNING: Deleting this registration will result in the loss of related payment records (if any).{/ts} {ts}Do you want to continue?{/ts} 
          	</dd> 
       	    </dl>
      	    </div> 
        </td></tr>
        <tr>{* <tr> for delete form button *}
    {else} {* If action is other than Delete *}
        {if $single}
        <tr><td class="right font-size12pt bold">{ts}Participant Name{/ts}&nbsp;&nbsp;</td><td class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</td></tr>
        {/if}
	<tr><td class="label nowrap">{$form.payment_processor_id.label}</td><td>{$form.payment_processor_id.html}</td>
        <tr><td class="label">{$form.event_id.label}</td><td>{$form.event_id.html}&nbsp;        
        {if $action eq 1 && !$past }<br /><a href="{$pastURL}">&raquo; {ts}Include past event(s) in this select list.{/ts}</a>{/if}    
        {if $is_test}
          {ts}(test){/ts}
        {/if}
            </td>
        </tr> 
    
        <tr><td class="label">{$form.role_id.label}</td><td>{$form.role_id.html}</td></tr>
        
        <tr><td class="label">{$form.register_date.label}</td><td>{$form.register_date.html}
    	{if $hideCalender neq true}<br />
	      {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event doTime=1}
    	  {include file="CRM/common/calendar/body.tpl" dateVar=register_date  offset=10 doTime=1  trigger=trigger_event ampm=1}       
	    {/if}    
     	    </td>
	    </tr>

        <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}{if $event_is_test} {ts}(test){/ts}{/if}
	{if $participant_status_id eq 5}{if $participant_is_pay_later}: {ts}Pay Later{/ts}{else}: {ts}Incomplete Transaction{/ts}{/if}{/if} 
	</td></tr>

        <tr><td class="label">{$form.source.label}</td><td>{$form.source.html|crmReplace:class:huge}</td></tr>

        <tr><td class="label">&nbsp;</td><td class="description">{ts}Source for this registration (if applicable).{/ts}</td></tr>
    </table>
    
    {* Fee block (EventFees.tpl) is injected here when an event is selected. *}
    <div id="feeBlock"></div>

    <div class="form-item">
        <fieldset>
            <dl>
            <dt style="vertical-align: top">{$form.note.label}</dt><dd>{$form.note.html}</dd>
            </dl>
        </fieldset>
    </div>
        
    <table class="form-layout">
        <tr><td colspan=2>
        {if $action eq 4} 
            {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
        {else}
            <div id="customData"></div>
        {/if} 
            </td>
        </tr>
        
        <tr> {* <tr> for add / edit form buttons *}
      	<td>&nbsp;</td>
    {/if}         
    <td>{$form.buttons.html}</td> 
    </tr> 
    </table>

	{if $accessContribution and $action eq 2 and $rows.0.contribution_id}
	    {include file="CRM/Contribute/Form/Selector.tpl" context="Search"}
	{/if}
</fieldset> 

{if $action eq 1 or $action eq 2}
{literal}
<script type="text/javascript">
//build fee block
buildFeeBlock( );

//build discount block
if ( document.getElementById('discount_id') ) {
    var discountId  = document.getElementById('discount_id').value;
    if ( discountId ) {
	var eventId  = document.getElementById('event_id').value;
	buildFeeBlock( eventId, discountId );    
    }
}

function buildFeeBlock( eventId, discountId )
{
	var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4'}"{literal};

{/literal}
{if $urlPathVar}
	dataUrl = dataUrl + '&' + '{$urlPathVar}'
{/if}
{literal}
	
	if ( !eventId ) {
	   var eventId  = document.getElementById('event_id').value;
	}

	if ( eventId) {
	   dataUrl = dataUrl + '&eventId=' + eventId;	
	} else {
  	   dojo.byId('feeBlock').innerHTML = '';
           return;
	}
	
	var participantId  = "{/literal}{$participantId}{literal}";

	if ( participantId ) {
	   dataUrl = dataUrl + '&participantId=' + participantId;	
	}

	if ( discountId ) {
	   dataUrl = dataUrl + '&discountId=' + discountId;	
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
                   dojo.byId('feeBlock').innerHTML = response;
                   // this executes any javascript in the injected block
                   executeInnerHTML( 'feeBlock' );
                }
        }
     });

}
</script>
{/literal}

{*include custom data js file*}
{include file="CRM/common/customData.tpl"}

{/if}
{/if} {* end of eventshow condition*}