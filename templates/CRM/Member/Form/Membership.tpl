{* this template is used for adding/editing/deleting memberships for a contact  *}
<div class="spacer"></div>
{if $cdType }
  {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
{if $membershipMode == 'test' }
    {assign var=registerMode value="TEST"}
{else if $membershipMode == 'live'}
    {assign var=registerMode value="LIVE"}
{/if}
{if !$emailExists and $action neq 8}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{ts}You will not be able to send an automatic email receipt for this Membership because there is no email address recorded for this contact. If you want a receipt to be sent when this Membership is recorded, click Cancel and then click Edit from the Summary tab to add an email address before recording the Membership.{/ts}</p>
    </dd>
  </dl>
</div>
{/if}
{if $membershipMode}
    <div id="help">
        {ts 1=$displayName 2=$registerMode}Use this form to submit Membership Record on behalf of %1. <strong>A %2 transaction will be submitted</strong> using the selected payment processor.{/ts}
    </div>
{/if}
<fieldset><legend>{if $action eq 1}{ts}New Membership{/ts}{elseif $action eq 2}{ts}Edit Membership{/ts}{else}{ts}Delete Membership{/ts}{/if}</legend> 
    <div class="form-item">
    {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this membership will also delete related membership log and payment records. This action cannot be undone. Consider modifying the membership status instead if you want to maintain a record of this membership.{/ts}
          {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
    {else}
    <dl>
	<dt>{$form.payment_processor_id.label}</dt><dd class="html-adjust">{$form.payment_processor_id.html}</dd><br />
 	<dt>{$form.membership_type_id.label}</dt><dd class="html-adjust">{$form.membership_type_id.html}
    {if $member_is_test} {ts}(test){/ts}{/if}<br />
        <span class="description">{ts}Select Membership Organization and then Membership Type.{/ts}</span></dd> 	
    <dt>{$form.source.label}</dt><dd class="html-adjust">&nbsp;{$form.source.html}<br />
        <span class="description">{ts}Source of this membership. This value is searchable.{/ts}</span></dd>
	<dt>{$form.join_date.label}</dt><dd class="html-adjust">{$form.join_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_1}
		{include file="CRM/common/calendar/body.tpl" dateVar=join_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership_1}
		<br />
        <span class="description">{ts}When did this contact first become a member?{/ts}</span></dd>
 	<dt>{$form.start_date.label}</dt><dd class="html-adjust">{$form.start_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_2}
		{include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership_2}
		<br />
        <span class="description">{ts}First day of current continuous membership period. Start Date will be automatically set based on Membership Type if you don't select a date.{/ts}</span></dd>
 	<dt>{$form.end_date.label}</dt><dd class="html-adjust">{$form.end_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_3}
		{include file="CRM/common/calendar/body.tpl" dateVar=end_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership_3}
		<br />
        <span class="description">{ts}Latest membership period expiration date. End Date will be automatically set based on Membership Type if you don't select a date.{/ts}</span></dd>
    {if ! $membershipMode}
        <dt>{$form.is_override.label}</dt><dd class="html-adjust">{$form.is_override.html}&nbsp;&nbsp;{help id="id-status-override"}</dd><br />
    {/if}
    </dl>	
    {if ! $membershipMode}
    {* Show read-only Status block - when action is UPDATE and is_override is FALSE *}
    <div id="memberStatus_show">
        {if $action eq 2}
        <dl>
        <dt>{$form.status_id.label}</dt><dd class="html-adjust">{$membershipStatus}
             {if $membership_status_id eq 5}{if $is_pay_later}: {ts}Pay Later{/ts}{else}: {ts}Incomplete Transaction{/ts}{/if}{/if}</dd>
        </dl>
        {/if}
    </div>

    {* Show editable status field when is_override is TRUE *}
    <div id="memberStatus">
        <dl>
        <dt>{$form.status_id.label}</dt><dd class="html-adjust">{$form.status_id.html}
        {if $membership_status_id eq 5}{if $is_pay_later}: {ts}Pay Later{/ts}{else}: {ts}Incomplete Transaction{/ts}{/if}{/if}<br />
            <span class="description">{ts}If <strong>Status Override</strong> is checked, the selected status will remain in force (it will NOT be modified by the automated status update script).{/ts}</span></dd>
        </dl>
    </div>
	{else if $membershipMode}
        <div class="spacer"></div>
        <fieldset><legend>{ts}Credit or Debit Card Information{/ts}</legend>
	
	       	<dt class="label">{$form.credit_card_type.label}</dt><dd class="html-adjust">{$form.credit_card_type.html}</dd><br />
        	<dt class="label">{$form.credit_card_number.label}</dt><dd class="html-adjust">{$form.credit_card_number.html}<br />
            		<span class="description">{ts}Enter numbers only, no spaces or dashes.{/ts}</span></dd><br />
	        <dt class="label">{$form.cvv2.label}</dt><dd class="html-adjust">{$form.cvv2.html} &nbsp; <img src="{$config->resourceBase}i/mini_cvv2.gif" alt="{ts}Security Code Location on Credit Card{/ts}" style="vertical-align: text-bottom;" /><br />
        		<span class="description">{ts}Usually the last 3-4 digits in the signature area on the back of the card.{/ts}</span></dd><br />
        	<dt class="label">{$form.credit_card_exp_date.label}</dt><dd class="html-adjust">{$form.credit_card_exp_date.html}</dd><br />
        </fieldset>
        
        <fieldset><legend>{ts}Billing Name and Address{/ts}</legend>
        	<dd colspan="2" class="description">{ts}Enter the name as shown on the credit or debit card, and the billing address for this card.{/ts}</dd><br />
        	<dt class="label">{$form.billing_first_name.label} </dt><dd class="html-adjust">{$form.billing_first_name.html}</dd><br />
        	<dt class="label">{$form.billing_middle_name.label}</dt><dd class="html-adjust">{$form.billing_middle_name.html}</dd><br />
        	<dt class="label">{$form.billing_last_name.label}</dt><dd class="html-adjust">{$form.billing_last_name.html}</dd><br />
            {assign var=n value=street_address-$bltID}
        	<dt class="label">{$form.$n.label}</dt><dd class="html-adjust">{$form.$n.html}</dd><br />
        	{assign var=n value=city-$bltID}
        	<dt class="label">{$form.$n.label}</dt><dd class="html-adjust">{$form.$n.html}</dd><br />
        	{assign var=n value=state_province_id-$bltID}
        	<dt class="label">{$form.$n.label}</dt><dd class="html-adjust">{$form.$n.html}</dd><br />
        	{assign var=n value=postal_code-$bltID}
        	<dt class="label">{$form.$n.label}</dt><dd class="html-adjust">{$form.$n.html}</dd><br />
        	{assign var=n value=country_id-$bltID}
        	<dt class="label">{$form.$n.label}</dt><dd class="html-adjust">{$form.$n.html}</dd><br />
        </fieldset>
 	{/if}
    {if $accessContribution and ! $membershipMode AND ! ($action eq 2 AND $rows.0.contribution_id) }
        <div id="contri">
            <dl>
            <dt>{$form.record_contribution.label}</dt><dd class="html-adjust">{$form.record_contribution.html}<br />
                <span class="description">{ts}Check this box to enter payment information. You will also be able to generate a customized receipt.{/ts}</span></dd>
            </dl>
        <div>
        <div class="spacer"></div>
        <fieldset id="recordContribution"><legend>{ts}Membership Payment and Receipt{/ts}</legend>
        <dl>
		<dt class="label">{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}<br />
                	<span class="description">{ts}Select the appropriate contribution type for this payment.{/ts}</span></dd>
		<dt class="label">{$form.total_amount.label}</dt><dd>{$form.total_amount.html}<br />
                	<span class="description">{ts}Membership payment amount. A contribution record will be created for this amount.{/ts}</span></dd>
           	<dt class="label" >{$form.receive_date.label}</dt><dd>{$form.receive_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_4}
		{include file="CRM/common/calendar/body.tpl" dateVar=receive_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership_4}</dd>  
          
            	<dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
	   	{if $action neq 2 }	
	    	<dt class="label">{$form.trxn_id.label}</dt><dd>{$form.trxn_id.html}</dd>
	   	{/if}		
		<dt class="label">{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>
        </dl>
       	</fieldset>
    {else}
        <div class="spacer"></div>
	{/if}
    {if $emailExists }
        <dl>
        <dt class="label">{$form.send_receipt.label}</dt><dd class="html-adjust">{$form.send_receipt.html}<br />
            <span class="description">{ts}Automatically email a membership confirmation and receipt to {$emailExists}?{/ts}</span></dd>
        </dl>
        <div id='notice'>
            <dl>
            <dt class="label">{$form.receipt_text_signup.label}</dt>
            <dd class="html-adjust"><span class="description">{ts}Enter a message you want included at the beginning of the emailed receipt. EXAMPLE: 'Thanks for supporting our organization with your membership.'{/ts}</span>
                 {$form.receipt_text_signup.html|crmReplace:class:huge}</dd>
            </dl>
        </div>
    {/if}
    <div id="customData"></div>
    {*include custom data js file*}
    {include file="CRM/common/customData.tpl"}
	{if $accessContribution and $action eq 2 and $rows.0.contribution_id}
        <fieldset>	 
            {include file="CRM/Contribute/Form/Selector.tpl" context="Search"}
        </fieldset>
	{/if}
   {/if}

    <dl>
        <dt>&nbsp;</dt><dd class="html-adjust">{$form.buttons.html}</dd>
    </dl>
    <div class="spacer"></div>
    </div>
</fieldset>
{if $action neq 8} {* Jscript additions not need for Delete action *} 
{if $accessContribution and ! $membershipMode AND ! ($action eq 2 AND $rows.0.contribution_id) }
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="record_contribution"
    trigger_value       =""
    target_element_id   ="recordContribution" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{/if}
{if $emailExists }
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="send_receipt"
    trigger_value       =""
    target_element_id   ="notice" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{/if}
{literal}
<script type="text/javascript">
{/literal}
{if !$membershipMode}
{literal}
showHideMemberStatus();

function showHideMemberStatus() {
	if (document.getElementsByName("is_override")[0].checked == true) {
	   show('memberStatus');
       hide('memberStatus_show');
	} else {
	   hide('memberStatus');
       show('memberStatus_show');
	}
}
{/literal}
{/if}
{literal}
function setPaymentBlock( memType ) 
{
    var dataUrl = {/literal}"{crmURL p='civicrm/ajax/memType' h=0 q='mtype='}"{literal} + memType;

    var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
        timeout: 5000, //Time in milliseconds
        handle: function(response, ioArgs){
                if (response instanceof Error) {
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
		    // on success
		    result = (response).split('^A');
		    document.getElementById('contribution_type_id').value =  result[0] ;
		    document.getElementById('total_amount').value =  result[1] ;
	       }
        }
     });
}
</script>
{/literal}
{/if} {* closing of delete check if *} 
{/if}{* closing of custom data if *}
