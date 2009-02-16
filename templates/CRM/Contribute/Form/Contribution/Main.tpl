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

{capture assign='reqMark'}<span class="marker" title="{ts}This field is required.{/ts}">*</span>{/capture}
<div class="form-item">
<div id="intro_text">
<p>{$intro_text}</p>
</div>

{include file="CRM/Contribute/Form/Contribution/MembershipBlock.tpl" context="makeContribution"}

<table class="form-layout-compressed">
	{if $form.amount}
	<tr>
		<td class="label nowrap">{$form.amount.label}</td>
		<td>{$form.amount.html}</td>
	</tr>
	{/if} 
	{if $is_allow_other_amount}
	<tr>
		<td class="label">{$form.amount_other.label}</td>
		<td>{$form.amount_other.html|crmMoney}</td>
	</tr>
	{/if} 
	{if $pledgeBlock} 
	{if $is_pledge_payment}
	<tr>
		<td class="label">{$form.pledge_amount.label}<span class="marker"> *</span></td>
		<td>{$form.pledge_amount.html}</td>
	</tr>
	{else}
	<tr>
		<td>&nbsp;</td>
		<td>{$form.is_pledge.html}&nbsp;&nbsp;
		{if $is_pledge_interval}{$form.pledge_frequency_interval.html}&nbsp;&nbsp;
		{/if}
		{$form.pledge_frequency_unit.html}&nbsp;&nbsp;{ts}for{/ts}&nbsp;&nbsp;{$form.pledge_installments.html}&nbsp;&nbsp;{ts}installments.{/ts}</td>
	</tr>
	{/if} 
	{/if} 
	{if $form.is_pay_later}
	<tr>
		<td class="label">&nbsp;</td>
		<td>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</td>
	</tr>
	{/if} 
	{if $form.is_recur}
	<tr>
		<td>&nbsp;</td>
		<td><strong>{$form.is_recur.html} {ts}every{/ts} &nbsp;{$form.frequency_interval.html} &nbsp; {$form.frequency_unit.html}&nbsp; {ts}for{/ts} &nbsp; {$form.installments.html} &nbsp;{$form.installments.label}</strong><br />
		<p><span class="description">{ts}Your recurring contribution will be processed automatically for the number of installments you specify. You can leave the number of installments blank if you want to make an open-ended commitment. In either case, you can choose to cancel at any time.{/ts} 
		{if $is_email_receipt}
		{ts}You will receive an email receipt for each recurring contribution. The receipts will include a link you can use if you decide to modify or cancel your future contributions.{/ts} 
		{/if} </p>
		</td>
	</tr>
	{/if} 
	{if $pcpSupporterText}
	<tr>
		<td></td>
		<td>{$pcpSupporterText}</td>
	<tr>
	{/if}
	<tr>
		{assign var=n value=email-$bltID}
		<td class="label">{$form.$n.label}</td>
		<td>&nbsp;{$form.$n.html}
		{if $form.is_for_organization}&nbsp;&nbsp;&nbsp;{$form.is_for_organization.html}&nbsp;{$form.is_for_organization.label}
		{/if}</td>

	</tr>
</table>

{if $is_for_organization} 
{include file=CRM/Contact/Form/OnBehalfOf.tpl} 
{/if} 
{* User account registration option. Displays if enabled for one of the profiles on this page. *}

{include file="CRM/common/CMSUser.tpl"} 
{include file="CRM/Contribute/Form/Contribution/PremiumBlock.tpl" context="makeContribution"} 

{if $honor_block_is_active}
<fieldset><legend>{$honor_block_title}</legend>
{$honor_block_text}
<table class="form-layout-compressed">
	<tr>
		<td colspan="3">
            {$form.honor_type_id.html}
            &nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('honor_type_id', '{$form.formName}'); return false;">{ts}unselect{/ts}</a>&nbsp;)<br />
            <span class="description">{ts}Please include the name, and / or email address of the person you are honoring,{/ts}</span>
        </td>
	</tr>
	<tr>
		<td>{$form.honor_prefix_id.html}</td>
		<td>{$form.honor_first_name.html}<br />
            <span class="description">{$form.honor_first_name.label}</span>
        </td>
		<td>{$form.honor_last_name.html}<br />
            <span class="description">{$form.honor_last_name.label}</span>
        </td>
	</tr>
	<tr><td></td>
		<td colspan="2">{$form.honor_email.html}<br />
            <span class="description">{$form.honor_email.label}
        </td>
	</tr>
</table>
</fieldset>
{/if} 

{include file="CRM/UF/Form/Block.tpl" fields=$customPre} 

{if $pcp}
<fieldset>
<table class="form-layout-compressed">
	<tr>
	   <td colspan="2">
               {$form.pcp_display_in_roll.html} &nbsp;
               {$form.pcp_display_in_roll.label}
        </td>
	</tr>
	<tr id="nameID">
	    <td colspan="2">
            {$form.pcp_is_anonymous.html}
        </td>
	</tr>
	<tr id="nickID">
        <td>{$form.pcp_roll_nickname.label}</td>
        <td>{$form.pcp_roll_nickname.html}<br />
            <span class="description">{ts}Enter the name you want listed with this contribution. You can use a nick name like 'The Jones Family' or 'Sarah and Sam'.{/ts}</span>
        </td>
	</tr>
	<!--tr>
		<td style="vertical-align: top">{$form.pcp_personal_note.label}</td>
		<td>{$form.pcp_personal_note.html}</td>
	</tr-->
</table>
</fieldset>
{/if} 

{if $is_monetary} 
    {include file='CRM/Core/BillingBlock.tpl'} 
{/if} 

{include file="CRM/UF/Form/Block.tpl" fields=$customPost}

{if $isCaptcha} 
{include file='CRM/common/ReCAPTCHA.tpl'} 
{/if} 
<div id="paypalExpress">
{if $is_monetary} 
{* Put PayPal Express button after customPost block since it's the submit button in this case. *} 
{if $paymentProcessor.payment_processor_type EQ 'PayPal_Express'} 
{assign var=expressButtonName value='_qf_Main_upload_express'}
<fieldset><legend>{ts}Checkout with PayPal{/ts}</legend>
<table class="form-layout-compressed">
	<tr>
		<td class="description">{ts}Click the PayPal button to continue.{/ts}</td>
	</tr>
	<tr>
		<td>{$form.$expressButtonName.html} <span style="font-size: 11px; font-family: Arial, Verdana;">Checkout securely. Pay without sharing your financial information. </span></td>
	</tr>
</table>
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
function pcpAnonymous( ) {
    // clear nickname field if anonymous is true
    if ( document.getElementsByName("pcp_is_anonymous")[1].checked ) { 
        document.getElementById('pcp_roll_nickname').value = '';
    }
    if ( ! document.getElementsByName("pcp_display_in_roll")[0].checked ) { 
        hide('nickID', 'table-row');
        hide('nameID', 'table-row');
    } else {
        if ( document.getElementsByName("pcp_is_anonymous")[0].checked ) {
            show('nameID', 'table-row');
            show('nickID', 'table-row');
        } else {
            show('nameID', 'table-row');
            hide('nickID', 'table-row');
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
