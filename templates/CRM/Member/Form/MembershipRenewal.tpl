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
		{include file="CRM/common/calendar/body.tpl" dateVar=renewal_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_membership_1}
		</dd>
    </dl>
	       
    <div id="contri">
        <dl>
        <dt>{$form.record_contribution.label}</dt>
	<dd class="html-adjust">{$form.record_contribution.html}</dd>
	<div>
            <dt>&nbsp;&nbsp;</dt><dd class="html-adjust">
		<fieldset id="recordContribution"><legend>{ts}Renewal Payment and Receipt{/ts}</legend>
		    <dt class="label">{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
	   	    <dt class="label">&nbsp;</dt><dd class="description">{ts}Select the appropriate contribution type for this transaction.{/ts}</dd>
		    <dt class="label">{$form.total_amount.label}</dt><dd>{$form.total_amount.html}</dd>
        	    <dt class="label">&nbsp;</dt><dd class="description">{ts}Actual amount given by contributor.{/ts}</dd>
       		    <dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
 		    <dt class="label">&nbsp;</dt><dd class="description">{ts}This field is blank for non-monetary contributions.{/ts}</dd>
		    <dt class="label">{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>
   	   	    {if $email}	
		        <dt class="label"></dt><dd>{$form.send_receipt.html}{$form.send_receipt.label}</dd>
	 	        <dt class="label">&nbsp;</dt><dd class="description">{ts}Automatically email a receipt for this contribution to {$email}?{/ts}</dd>
		        <div id='notice'>
		    	   <dt class="label">{$form.receipt_text_renewal.label}</dt><dd>{$form.receipt_text_renewal.html}<dt class="label"><br/></dt><dd class="description">{ts}Enter a message you want included at the beginning of the emailed receipt. EXAMPLE: "Thanks for supporting our organization with your membership."{/ts}</dd>
		        </div>
		    {/if}
	        </fieldset>
	    </dd>
	
	</div>
        </dl>
    </div>
         
   <dl>
     <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
   </dl>
   <div class="spacer"></div>

</fieldset>
</div>

{literal}
<script type="text/javascript">
showRecordContribution();  
showReceiptText();

function showRecordContribution() {
	if (document.getElementsByName("record_contribution")[0].checked == true) {
	   show('recordContribution');
       	} else {
	   hide('recordContribution');
       	}
}

function showReceiptText() {
	if (document.getElementsByName("send_receipt")[0].checked == true) {
	   show('notice');
       	} else {
	   hide('notice');
       	}
}

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
