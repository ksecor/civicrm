{* this template is used for renewing memberships for a contact  *}
<div class="form-item">
<fieldset><legend>{if $action eq 32768}{ts}Renew Membership{/ts}{/if}</legend>
    <div id="help" class="description">
        {ts}Renewing will add the normal membership period to the End Date of the previous period for members whose status is Current or Grace. For Expired memberships, renewing will create a membership period commencing from the 'Date Renewal Entered'. This date can be adjusted including being set to the day after the previous End Date - if continuous membership is required.{/ts}
    </div>
    <dl>
 	<dt>{ts}Membership Organization and Type{/ts}</dt><dd class="html-adjust">{$orgName}&nbsp;&nbsp;-&nbsp;&nbsp;{$memType}
        {if $member_is_test} {ts}(test){/ts}{/if}</dd>
    <dt>{ts}Membership Status{/ts}</dt><dd class="html-adjust">&nbsp;{$membershipStatus}<br />
        <span class="description">{ts}Status of this membership.{/ts}</span></dd>

	<dt>{ts}Membership End Date{/ts}</dt><dd class="html-adjust">&nbsp;{$endDate}</dd>
	<dt>{$form.renewal_date.label}</dt><dd class="html-adjust">{$form.renewal_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_1}
		{include file="CRM/common/calendar/body.tpl" dateVar=renewal_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership_1}</dd>
    </dl>
	       
    <div id="contri">
        <dl>
        <dt>{$form.record_contribution.label}</dt><dd class="html-adjust">{$form.record_contribution.html}<br />
   	    <span class="description">{ts}Check this box to enter payment information. You will also be able to generate a customized receipt.{/ts}</span></dd>
        </dl>
	<div>
    <div class="spacer"></div>
    
    <fieldset id="recordContribution"><legend>{ts}Renewal Payment and Receipt{/ts}</legend>
    <dl>	
        <dt class="label">{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}<br />
            <span class="description">{ts}Select the appropriate contribution type for this payment.{/ts}</span></dd>
        <dt class="label">{$form.total_amount.label}</dt><dd>{$form.total_amount.html}<br />
            <span class="description">{ts}Membership payment amount. A contribution record will be created for this amount.{/ts}</span></dd>
        <dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
	<dt class="label">{$form.trxn_id.label}</dt><dd>{$form.trxn_id.html}</dd>
        <dt class="label">{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>
    </dl>
	{if $email}	
	    <dl>
	    <dt class="label">{$form.send_receipt.label}</dt><dd>{$form.send_receipt.html}<br />
		<span class="description">{ts}Automatically email a membership confirmation and contribution receipt to {$email}?{/ts}</span></dd>
	    </dl> 
	    <div id='notice'>
		<dl>		
    	   	<dt class="label">{$form.receipt_text_renewal.label}</dt>
            <dd class="html-adjust"><span class="description">{ts}Enter a message you want included at the beginning of the emailed receipt. EXAMPLE: 'Thanks for supporting our organization with your membership.'{/ts}</span>
		     {$form.receipt_text_renewal.html|crmReplace:class:huge}</dd> 
		</dl>
	    </div>
	{/if}
    </fieldset>
         
   <dl>
     <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
   </dl>
   <div class="spacer"></div>

</fieldset>
</div>

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="record_contribution"
    trigger_value       =""
    target_element_id   ="recordContribution" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="send_receipt"
    trigger_value       =""
    target_element_id   ="notice" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}

{literal}
<script type="text/javascript">
function checkPayment()
{
    showHideByValue('record_contribution','','recordContribution','table-row','radio',false);
    document.getElementsByName('send_receipt')[0].checked = true;
    showHideByValue('send_receipt','','notice','table-row','radio',false);                                                                                                     
}        
</script>
{/literal}