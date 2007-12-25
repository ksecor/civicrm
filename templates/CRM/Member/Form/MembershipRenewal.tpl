{* this template is used for renewing memberships for a contact  *}
<div class="form-item">
<fieldset><legend>{if $action eq 32768}{ts}Renew Membership{/ts}{/if}</legend> 
  
    <dl>
 	<dt>{$form.membership_type_id.label}</dt><dd class="html-adjust">{$form.membership_type_id.html}
    {if $member_is_test} {ts}(test){/ts}{/if}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Select Membership Organization and then Membership Type.{/ts}</dd> 	
        <dt>{ts}Membership Status{/ts}</dt><dd class="html-adjust">&nbsp;{$membershipStatus}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Status of this membership.{/ts}</dd>

	<dt>{ts}Membership End Date{/ts}</dt><dd class="html-adjust">&nbsp;{$endDate}</dd>
	<dt>{$form.renewal_date.label}</dt><dd class="html-adjust">{$form.renewal_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_1}
		{include file="CRM/common/calendar/body.tpl" dateVar=renewal_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership_1}
		</dd>
    </dl>
	       
    <div id="contri">
        <dl>
        <dt>{$form.record_contribution.label}</dt><dd class="html-adjust">{$form.record_contribution.html}<br />
   	    <span class="description">{ts}Check this box to enter payment information. You will also be able to generate a customized receipt.{/ts}</span></dd>
	<div>
    <div class="spacer"></div>
     <fieldset id="recordContribution"><legend>{ts}Renewal Payment and Receipt{/ts}</legend>
	 <dl>	
		    <dt class="label">{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}<br />
		<span class="description">{ts}Select the appropriate contribution type for this payment.{/ts}</span></dd>
		    <dt class="label">{$form.total_amount.label}</dt><dd>{$form.total_amount.html}<br />
		<span class="description">{ts}Membership payment amount. A contribution record will be created for this amount.{/ts}</span></dd>
		    <dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
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
		<dd class="html-adjust"><span class="description">{ts}Enter a message you want included at the beginning of the emailed receipt. EXAMPLE: "Thanks for supporting our organization with your membership."{/ts}</span>
		     {$form.receipt_text_renewal.html|crmReplace:class:huge}</dd> 
		</dl>
	    </div>
	{/if}
     </fieldset></dd>
	</div>
        </dl>
    </div>
         
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

function reload(refresh) {
    var membershipTypeValue = document.getElementsByName("membership_type_id[1]")[0].options[document.getElementsByName("membership_type_id[1]")[0].selectedIndex].value;
    var url = {/literal}"{$refreshURL}"{literal}
    var post = url + "&subType=" + membershipTypeValue;
    if ( refresh ) {
        window.location= post; 
    }
}

</script>
{/literal}
