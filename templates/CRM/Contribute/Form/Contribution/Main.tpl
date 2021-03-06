{literal}
<script type="text/javascript">
<!--
// Putting these functions directly in template so they are available for standalone forms

function useAmountOther() {
    for( i=0; i < document.Main.elements.length; i++ ) {
        element = document.Main.elements[i];
        if ( element.type == 'radio' && element.name == 'amount' ) {
            if (element.value == 'amount_other_radio' ) {
                element.checked = true;
            } else {
                element.checked = false;
            }
        }
    }
}

function clearAmountOther() {
  if (document.Main.amount_other == null) return; // other_amt field not present; do nothing
  document.Main.amount_other.value = "";
}

//-->
</script>
{/literal}

{if $action & 1024} 
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"} 
{/if}

{include file="CRM/common/TrackingFields.tpl"}

{capture assign='reqMark'}<span class="marker" title="{ts}This field is required.{/ts}">*</span>{/capture}
<div class="form-item">
    <div id="intro_text">
        <p>{$intro_text}</p>
    </div>

{if $priceSet}
    <div id="priceset">
         {include file="CRM/Price/Form/PriceSet.tpl"}
    </div>
{else}
    {include file="CRM/Contribute/Form/Contribution/MembershipBlock.tpl" context="makeContribution"}

	{if $form.amount}
	    <div class="section {$form.amount.name}-section">
			<div class="label">{$form.amount.label}</div>
			<div class="content">{$form.amount.html}</div>
			<div class="clear"></div> 
	    </div>
	{/if} 
	{if $is_allow_other_amount}
	    <div class="section {$form.amount_other.name}-section">
			<div class="label">{$form.amount_other.label}</div>
			<div class="content">{$form.amount_other.html|crmMoney}</div>
			<div class="clear"></div> 
	    </div>
	{/if} 
	{if $pledgeBlock} 
	    {if $is_pledge_payment}
	    <div class="section {$form.pledge_amount.name}-section">
			<div class="label">{$form.pledge_amount.label}&nbsp;<span class="marker">*</span></div>
			<div class="content">{$form.pledge_amount.html}</div>
			<div class="clear"></div> 
	    </div>
	    {else}
	    <div class="section {$form.is_pledge.name}-section">
			<div class="content">
				{$form.is_pledge.html}&nbsp;
				{if $is_pledge_interval}
					{$form.pledge_frequency_interval.html}&nbsp;
				{/if}
				{$form.pledge_frequency_unit.html}&nbsp;{ts}for{/ts}&nbsp;{$form.pledge_installments.html}&nbsp;{ts}installments.{/ts}
			</div>
	    </div>
	    {/if} 
	{/if} 
{/if}
	{if $form.is_pay_later}
	    <div class="section {$form.is_pay_later.name}-section">
			<div class="content">{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</div>
	    </div>
	{/if} 
	{if $form.is_recur}
	    <div class="section {$form.is_recur.name}-section">
			<div div class="content">
				<p><strong>{$form.is_recur.html} {ts}every{/ts} &nbsp;{$form.frequency_interval.html} &nbsp; {$form.frequency_unit.html}&nbsp; {ts}for{/ts} &nbsp; {$form.installments.html} &nbsp;{$form.installments.label}</strong>
				</p>
				<p><span class="description">{ts}Your recurring contribution will be processed automatically for the number of installments you specify. You can leave the number of installments blank if you want to make an open-ended commitment. In either case, you can choose to cancel at any time.{/ts} 
		{if $is_email_receipt}
		    {ts}You will receive an email receipt for each recurring contribution. The receipts will include a link you can use if you decide to modify or cancel your future contributions.{/ts} 
		{/if} </p>
		</div>
	    </div>
	{/if} 
	{if $pcpSupporterText}
	    <div class="section pcpSupporterText-section">
			<div class="content">{$pcpSupporterText}</div>
	    </div>
	{/if}
	    {assign var=n value=email-$bltID}
	    <div class="section {$form.$n.name}-section">
	    	<div class="label">{$form.$n.label}</div>
	    	<div class="content">
	    		{$form.$n.html}
	    	</div>
	    	<div class="clear"></div> 
	    </div>
	
	{if $form.is_for_organization}
		<div class="section {$form.is_for_organization.name}-section">
	    	<div class="content">
	    		{$form.is_for_organization.html}&nbsp;{$form.is_for_organization.label}
	    	</div>
	    </div>
	{/if}


    {if $is_for_organization} 
        {include file=CRM/Contact/Form/OnBehalfOf.tpl} 
    {/if} 
    {* User account registration option. Displays if enabled for one of the profiles on this page. *}

    {include file="CRM/common/CMSUser.tpl"} 
    {include file="CRM/Contribute/Form/Contribution/PremiumBlock.tpl" context="makeContribution"} 

    {if $honor_block_is_active}
	<fieldset class="honor_block-group">
		<legend>{$honor_block_title}</legend>
	    	<div class="section honor_block_text-section">
	    		{$honor_block_text}
	    	</div>
		{if $form.honor_type_id.html}
		    <div class="section {$form.honor_type_id.name}-section">
				<div class="content" >
					{$form.honor_type_id.html}
					<span class="unselect">(<a href="#" title="unselect" onclick="unselectRadio('honor_type_id', '{$form.formName}');enableHonorType(); return false;">{ts}unselect{/ts}</a>)</span>
					<div class="description">{ts}Please include the name, and / or email address of the person you are honoring,{/ts}</div>
				</div>
		    </div>
		{/if}
		<div id="honorType" class="section">
			<div class="section {$form.honor_prefix_id.name}-section">	
			    <div class="content">{$form.honor_prefix_id.html}</div>
			</div>
			<div class="section {$form.honor_first_name.name}-section">	
				<div class="label">{$form.honor_first_name.label}</div>
			    <div class="content">
			        {$form.honor_first_name.html}
				</div>
				<div class="clear"></div> 
			</div>
			<div class="section {$form.honor_last_name.name}-section">	
			    <div class="label">{$form.honor_last_name.label}</div>
			    <div class="content">
			        {$form.honor_last_name.html}
				</div>
				<div class="clear"></div> 
			</div>
			<div id="honorTypeEmail" class="section {$form.honor_email.name}-section">
				<div class="label">{$form.honor_email.label}</div>
			    <div class="content">
				    {$form.honor_email.html}
				</div>
				<div class="clear"></div> 
			</div>
		</div>
	</fieldset>
    {/if} 

    <div class="custom_pre-profile">
    	{include file="CRM/UF/Form/Block.tpl" fields=$customPre} 	
    </div>

    {if $pcp}
    <fieldset class="pcp-group">
    	<div class="section pcp-section">
			<div class="section">
				<div class="content">
			        {$form.pcp_display_in_roll.html} &nbsp;
			        {$form.pcp_display_in_roll.label}
			    </div>
			</div>
			<div id="nameID" class="section nameId-section">
			    <div class="content">
			        {$form.pcp_is_anonymous.html}
			    </div>
			</div>
			<div id="nickID" class="section nickID-section">
			    <div class="label">{$form.pcp_roll_nickname.label}</div>
			    <div class="content">{$form.pcp_roll_nickname.html}
				<div class="description">{ts}Enter the name you want listed with this contribution. You can use a nick name like 'The Jones Family' or 'Sarah and Sam'.{/ts}</div>
			    </div>
			    <div class="clear"></div> 
			</div>
			<div id="personalNoteID" class="section personalNoteID-section">
			    <div class="label">{$form.pcp_personal_note.label}</div>
			    <div class="content">
			    	{$form.pcp_personal_note.html}
    		        <div class="description">{ts}Enter a message to accompany this contribution.{/ts}</div>
			    </div>
			    <div class="clear"></div> 
			</div>
    	</div>
    </fieldset>
    {/if} 

    {if $is_monetary} 
        {include file='CRM/Core/BillingBlock.tpl'} 
    {/if} 

    <div class="custom_post-profile">
    	{include file="CRM/UF/Form/Block.tpl" fields=$customPost}
	</div>
	
    {if $is_monetary and $form.bank_account_number}
    <div id="payment_notice">
      <fieldset class="payment_notice-group">
          <legend>{ts}Agreement{/ts}</legend>
          {ts}Your account data will be used to charge your bank account via direct debit. While submitting this form you agree to the charging of your bank account via direct debit.{/ts}
      </fieldset>
    </div>
    {/if}

    {if $isCaptcha} 
	{include file='CRM/common/ReCAPTCHA.tpl'} 
    {/if} 
    <div id="paypalExpress">
    {if $is_monetary} 
	{* Put PayPal Express button after customPost block since it's the submit button in this case. *} 
	{if $paymentProcessor.payment_processor_type EQ 'PayPal_Express'} 
	    {assign var=expressButtonName value='_qf_Main_upload_express'}
	    <fieldset class="paypal_checkout-group">
	    	<legend>{ts}Checkout with PayPal{/ts}</legend>
	    	<div class="section">
				<div class="section paypalButtonInfo-section">
					<div class="content">
					    <span class="description">{ts}Click the PayPal button to continue.{/ts}</span>
					</div>
					<div class="clear"></div> 
				</div>	
				<div class="section {$form.$expressButtonName.html}-section">
				    <div class="content">
				    	{$form.$expressButtonName.html} <span class="description">Checkout securely. Pay without sharing your financial information. </span>
				    </div>
				    <div class="clear"></div> 
				</div>
	    	</div>	
	    </fieldset>
	{/if} 
    {/if}
    </div>
    <div id="crm-submit-buttons">{$form.buttons.html}</div>
    {if $footer_text}
    	<div id="footer_text">
			<p>{$footer_text}</p>
    	</div>
    {/if}
</div>

{* Hide Credit Card Block and Billing information if contribution is pay later. *}
{if $form.is_pay_later and $hidePaymentInformation} 
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_pay_later"
    trigger_value       =""
    target_element_id   ="payment_information" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 1
}
{/if}
{*{if $pcp}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="pcp_display_in_roll"
    trigger_value       =""
    target_element_id   ="nameID|nickID" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{/if}*}

<script type="text/javascript">
{if $pcp}pcpAnonymous();{/if}
{literal}
var is_monetary = {/literal}{$is_monetary}{literal}
if (! is_monetary ) {
    if ( document.getElementsByName("is_pay_later")[0] ) {
	document.getElementsByName("is_pay_later")[0].disabled = true;
    }
}
if ( {/literal}"{$form.is_recur}"{literal} ) {
    if ( document.getElementsByName("is_recur")[0].checked == true ) { 
	window.onload = function() {
	    enablePeriod();
	}
    }
}
function enablePeriod ( ) {
    var frqInt  = {/literal}"{$form.frequency_interval}"{literal};
    if ( document.getElementsByName("is_recur")[0].checked == true ) { 
	document.getElementById('installments').value = '';
	if ( frqInt ) {
	    document.getElementById('frequency_interval').value    = '';
	    document.getElementById('frequency_interval').disabled = true;
	}
	document.getElementById('installments').disabled   = true;
	document.getElementById('frequency_unit').disabled = true;
    } else {
	if ( frqInt ) {
	    document.getElementById('frequency_interval').disabled = false;
	}
	document.getElementById('installments').disabled   = false;
	document.getElementById('frequency_unit').disabled = false;
    }
}

{/literal}{if $honor_block_is_active AND $form.honor_type_id.html}{literal}
    enableHonorType();
{/literal} {/if}{literal}

function enableHonorType( ) {
    var element = document.getElementsByName("honor_type_id");
    for (var i = 0; i < element.length; i++ ) {
	var isHonor = false;	
	if ( element[i].checked == true ) {
	    var isHonor = true;
	    break;
	}
    }
    if ( isHonor ) {
	show('honorType', 'block');
	show('honorTypeEmail', 'block');
    } else {
	document.getElementById('honor_first_name').value = '';
	document.getElementById('honor_last_name').value  = '';
	document.getElementById('honor_email').value      = '';
	document.getElementById('honor_prefix_id').value  = '';
	hide('honorType', 'block');	
	hide('honorTypeEmail', 'block');
    }
}

function pcpAnonymous( ) {
    // clear nickname field if anonymous is true
    if ( document.getElementsByName("pcp_is_anonymous")[1].checked ) { 
        document.getElementById('pcp_roll_nickname').value = '';
    }
    if ( ! document.getElementsByName("pcp_display_in_roll")[0].checked ) { 
        hide('nickID', 'table-row');
        hide('nameID', 'table-row');
	hide('personalNoteID', 'table-row');
    } else {
        if ( document.getElementsByName("pcp_is_anonymous")[0].checked ) {
            show('nameID', 'table-row');
            show('nickID', 'table-row');
	    show('personalNoteID', 'table-row');
        } else {
            show('nameID', 'table-row');
            hide('nickID', 'table-row');
	    hide('personalNoteID', 'table-row');
        }
    }
}
{/literal}{if $form.is_pay_later and $paymentProcessor.payment_processor_type EQ 'PayPal_Express'}{literal} 
    showHidePayPalExpressOption();
{/literal} {/if}{literal}
function showHidePayPalExpressOption()
{
    if (document.getElementsByName("is_pay_later")[0].checked) {
	show("crm-submit-buttons");
	hide("paypalExpress");
    } else {
	show("paypalExpress");
	hide("crm-submit-buttons");
    }
}
{/literal}
</script>
