{* this template is used for adding/editing/deleting memberships for a contact  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Membership{/ts}{elseif $action eq 2}{ts}Edit Membership{/ts}{else}{ts}Delete Membership{/ts}{/if}</legend> 
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this membership will also delete related membership log and payment records. This action can not be undone. Consider modifying the membership status instead if you want to maintain a record of this membership.{/ts}
          {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
   {else}
    <dl>
 	<dt>{$form.membership_type_id.label}</dt><dd class="html-adjust">{$form.membership_type_id.html}
    {if $member_is_test} {ts}(test){/ts}{/if}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Select Membership Organization and then Membership Type.{/ts}</dd> 	
        <dt>{$form.source.label}</dt><dd class="html-adjust">&nbsp;{$form.source.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Source of this membership. This value is searchable.{/ts}</dd>
	<dt>{$form.join_date.label}</dt><dd class="html-adjust">{$form.join_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_1}
		{include file="CRM/common/calendar/body.tpl" dateVar=join_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_membership_1}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}When did this contact first become a member?{/ts}</dd>
 	<dt>{$form.start_date.label}</dt><dd class="html-adjust">{$form.start_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_2}
		{include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_membership_2}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}First day of current continuous membership period. Start Date will be automatically set based on Membership Type if you don't select a date.{/ts}</dd>
 	<dt>{$form.end_date.label}</dt><dd class="html-adjust">{$form.end_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_3}
		{include file="CRM/common/calendar/body.tpl" dateVar=end_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_membership_3}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Latest membership period expiration date. End Date will be automatically set based on Membership Type if you don't select a date.{/ts}</dd>
    <dt>{$form.is_override.label}</dt><dd class="html-adjust">{$form.is_override.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Membership status is set and updated automatically based on your configured membership status rules. Check this box if you want to bypass this process, and manually set a status for this membership. The selected status which will remain in force unless it is again modified on this screen.{/ts}</dd>
    </dl>
	
     
    {* Show read-only Status block - when action is UPDATE and is_override is FALSE *}
    <div id="memberStatus_show">
        {if $action eq 2}
        <dl>
        <dt>{$form.status_id.label}</dt><dd class="html-adjust">{$membershipStatus}</dd>
        </dl>
        {/if}
    </div>

    {* Show editable status field when is_override is TRUE *}
    <div id="memberStatus">
        <dl>
        <dt>{$form.status_id.label}</dt><dd class="html-adjust">{$form.status_id.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}If <strong>Status Hold?</strong> is checked, the selected status will be in in force (it will NOT be modified by the automated status update script).{/ts}</dd>
        </dl>
    </div>
    
    <div id="contri">
        <dl>
        <dt>{$form.record_contribution.label}</dt><dd class="html-adjust">{$form.record_contribution.html}</dd>
	<div>
            <dt>&nbsp;&nbsp;</dt><dd class="html-adjust">
		<fieldset id="recordContribution"><legend>{ts}Contribution{/ts}</legend>
		    <dt class="label">{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
 		    <dt class="label">&nbsp;</dt><dd class="description">{ts}Select the appropriate contribution type for this transaction.{/ts}</dd><br />
		    <dt class="label">{$form.total_amount.label}</dt><dd>{$form.total_amount.html}</dd>
		    <dt class="label">&nbsp;</dt><dd class="description">{ts}Actual amount given by contributor.{/ts}</dd><br />
        	    <dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
		    <dt class="label">&nbsp;</dt><dd class="description">{ts}This field is blank for non-monetary contributions.{/ts}</dd><br />
		    <dt class="label">{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd><br />
		    {if $email}
  		        <dt class="label"></dt><dd>{$form.send_receipt.html}{$form.send_receipt.label}</dd>
	   	        <dt class="label">&nbsp;</dt> <dd class="description">{ts}Automatically email a receipt for this contribution to {$email}?{/ts}</dd>
		        <div id='notice'>
		    	   <dt class="label">{$form.receipt_text.label}</dt><dd>{$form.receipt_text.html}<dt class="label"></dt><dd class="description">{ts}Enter a message you want included at the beginning of the emailed receipt. EXAMPLE: "Thanks for supporting our organization with your membership."{/ts}</dd>
		        </div>
		    {/if}
	        </fieldset>
	    </dd>
	
	</div>
        </dl>
    </div>
      
    {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1} 
     
   {/if}

   <dl>
     <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
   </dl>
   <div class="spacer"></div>

</fieldset>
</div>

{literal}
<script type="text/javascript">
showHideMemberStatus();
showRecordContribution();  
showReceiptText();
function showHideMemberStatus() {
	if (document.getElementsByName("is_override")[0].checked == true) {
	   show('memberStatus');
       hide('memberStatus_show');
	} else {
	   hide('memberStatus');
       show('memberStatus_show');
	}
}

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
