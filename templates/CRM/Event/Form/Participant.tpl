{* This template is used for adding/editing/deleting offline Event Registrations *}
{if $showFeeBlock }
   {include file="CRM/Event/Form/EventFees.tpl"}
{elseif $cdType }
  {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
<fieldset><legend>{if $action eq 1}{ts}New Event Registration{/ts}{elseif $action eq 8}{ts}Delete Event Registration{/ts}{else}{ts}Edit Event Registration{/ts}{/if}</legend> 
    {if $action eq 1 AND $paid}
    <div id="help">
	{ts}If you are accepting offline payment from this participant, check <strong>Record Payment</strong>. You will be able to fill in the payment information, and optionally send a receipt.{/ts}
    </div>  
    {/if}
    
    <table class="form-layout" >
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

        <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}{if $event_is_test} {ts}(test){/ts}{/if}</td></tr>

        <tr><td class="label">{$form.source.label}</td><td>{$form.source.html|crmReplace:class:huge}</td></tr>

        <tr><td class="label">&nbsp;</td><td class="description">{ts}Source for this registration (if applicable).{/ts}</td></tr>
        {*if $paid*}
	<tr><td colspan="2"><div id="feeBlock"></div></td></tr>
        {*/if*}

    <tr><td class="label" style="vertical-align:top;">{$form.note.label}</td><td>{$form.note.html}</td></tr>
    {if $paid}
       <tr>
           <td class="label">{$form.record_contribution.label}</td><td class="html-adjust">{$form.record_contribution.html}<br />
              <span class="description">{ts}Check this box to enter payment information. You will also be able to generate a customized receipt.{/ts}</span>
           </td>
       </tr>
    {/if}

    {if $paid AND $form.contribution_type_id }
    <tr id="payment_information">
       <td class ='html-adjust' colspan=2>
           <fieldset><legend>{ts}Payment Information{/ts}</legend>
             <div class="form-item">
                 <dl id="recordContribution" class="html-adjust">	        
                    <dt class="label">{$form.contribution_type_id.label}</dt>
                    <dd>{$form.contribution_type_id.html}<br /><span class="description">{ts}Select the appropriate contribution type for this payment.{/ts}</span></dd>
                	<dt class="label" >{$form.receive_date.label}</dt><dd>{$form.receive_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership}
		{include file="CRM/common/calendar/body.tpl" dateVar=receive_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership}</dd> 
                    <dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
                    <dt class="label">{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>      
  	             </dl>
             </div>
           </fieldset>
       </td>
    </tr> 
    {/if}

    {if $email OR $batchEmail}
    <tr id="send_confirmation">
       <td class ='html-adjust' colspan=2>
	   <fieldset><legend>{if $paid}{ts}Registration Confirmation and Receipt{/ts}{else}{ts}Registration Confirmation{/ts}{/if}</legend>  
             <div class="form-item">
		 <dl> 
	    	     <dt class="label">{if $paid}{ts}Send Confirmation and Receipt{/ts}{else}{ts}Send Confirmation{/ts}{/if}</dt>
            	     <dd class ='html-adjust' >{$form.send_receipt.html}<br>
                     <span class="description">{ts}Automatically email a confirmation {if $paid} and receipt {/if}{if $email} to {$email}?{/if}{/ts}</span></dd>
            	 </dl>
                 <div id='notice'>
                    <dl>
 			<dt class="label">{$form.receipt_text.label}</dt> 
                	<dd class="html-adjust"><span class="description">{ts}Enter a message you want included at the beginning of the confirmation email. EXAMPLE: 'Thanks for registering for this event.'{/ts}</span><br/>
			{$form.receipt_text.html|crmReplace:class:huge}</dd>
                    </dl>
                 </div> 
             </div>
           </fieldset>
       </td>
    </tr> 
    {/if}
    
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

{if $paid} {* Record contribution field only present if this is a paid event. *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="record_contribution"
    trigger_value       =""
    target_element_id   ="payment_information" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{/if}

{if $email} {* Send receipt field only present if contact has a valid email address. *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="send_receipt"
    trigger_value       =""
    target_element_id   ="notice" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{/if}

{if $action eq 1 or $action eq 2}
{literal}
<script type="text/javascript">

buildFeeBlock( );

function buildFeeBlock( eventId )
{
	var dataUrl = {/literal}"{crmURL h=0 q='snippet=1'}"{literal};
	
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
                }else{
		   // on success
                   dojo.byId('feeBlock').innerHTML = response;
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