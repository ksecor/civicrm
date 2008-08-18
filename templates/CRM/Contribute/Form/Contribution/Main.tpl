{literal}
<script type="text/javascript">
<!--
// Putting these functions directly in template so they are available for standalone forms

function useAmountOther() {
    for( i=0; i < document.Main.elements.length; i++) {
        element = document.Main.elements[i];
        if (element.type == 'radio' && element.name == 'amount') {
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
{capture assign='reqMark'}<span class="marker"  title="{ts}This field is required.{/ts}">*</span>{/capture}
<div class="form-item">
   <div id="intro_text">
    <p>
    {$intro_text}
    </p>
   </div>
    {include file="CRM/Contribute/Form/Contribution/MembershipBlock.tpl" context="makeContribution"}     
 
    <table class="form-layout-compressed">
    {if $form.amount}
    <tr>
        <td class="label nowrap">{$form.amount.label}</td><td>{$form.amount.html}</td>
    </tr>
    {/if}
    {if $is_allow_other_amount}
        <tr><td class="label">{$form.amount_other.label}</td><td>{$form.amount_other.html|crmMoney}</td></tr>
    {/if}
    {if $pledgeBlock}
        {if $is_pledge_payment} 
            <tr><td class="label">{$form.pledge_amount.label}<span class="marker"> *</span></td><td>{$form.pledge_amount.html}</td></tr>
        {else}
            <tr><td>&nbsp;</td>
            <td>{$form.is_pledge.html}&nbsp;&nbsp;{if $is_pledge_interval}{$form.pledge_frequency_interval.html}&nbsp;&nbsp;{/if}{$form.pledge_frequency_unit.html}&nbsp;&nbsp;{ts}for{/ts}&nbsp;&nbsp;{$form.pledge_installments.html}&nbsp;&nbsp;{ts}installments.{/ts}</td></tr>
        {/if}
    {/if}
    {if $form.is_pay_later}
        <tr><td class="label">&nbsp;</td><td>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</td></tr>
    {/if}
    {if $form.is_recur}  
        <tr>
           <td>&nbsp;</td><td><strong>{$form.is_recur.html} {ts}every{/ts} &nbsp; {$form.frequency_interval.html} &nbsp; {$form.frequency_unit.html} &nbsp; {ts}for{/ts} &nbsp; {$form.installments.html} &nbsp; {$form.installments.label}</strong><br />
                           <p><span class="description">{ts}Your recurring contribution will be processed automatically for the number of installments you specify. You can leave the number of installments blank if you want to make an open-ended commitment. In either case, you can choose to cancel at any time.{/ts}
                           {if $is_email_receipt}
                                {ts}You will receive an email receipt for each recurring contribution. The receipts will include a link you can use if you decide to modify or cancel your future contributions.{/ts}
                           {/if}
                           </p>
           </td>
       </tr>
    {/if}
    <tr>
	{assign var=n value=email-$bltID}
        <td class="label">{$form.$n.label}</td>
        <td>&nbsp;{$form.$n.html}{if $form.is_for_organization}&nbsp;&nbsp;&nbsp;{$form.is_for_organization.html}&nbsp;{$form.is_for_organization.label}{/if}</td>
    </tr>
    </table>

    {if $form.is_for_organization}
        {include file=CRM/Contact/Form/OnBehalfOf.tpl}
    {/if}

    {* User account registration option. Displays if enabled for one of the profiles on this page. *}
    {include file="CRM/common/CMSUser.tpl"}
  
    {include file="CRM/Contribute/Form/Contribution/PremiumBlock.tpl" context="makeContribution"}

    {if $honor_block_is_active}
    <fieldset><legend>{$honor_block_title}</legend>
        {$honor_block_text}
      <table class="form-layout-compressed">
      <tr><td>{$form.honor_type_id.label}</td><td>{$form.honor_type_id.html}</td></tr>  
      <tr><td>{$form.honor_prefix_id.label}</td><td>{$form.honor_prefix_id.html}</td></tr>
	  <tr><td>{$form.honor_first_name.label}</td><td>{$form.honor_first_name.html}</td></tr>
	  <tr><td>{$form.honor_last_name.label}</td><td>{$form.honor_last_name.html}</td></tr>
      <tr><td>{$form.honor_email.label}</td><td>{$form.honor_email.html}</td></tr>
      </table>
    </fieldset>
    {/if}
    
    {include file="CRM/UF/Form/Block.tpl" fields=$customPre}

{if $is_monetary}
{if $form.credit_card_number}
<div id="payment_information">
    <fieldset><legend>{ts}Credit or Debit Card Information{/ts}</legend>
    {if $paymentProcessor.billing_mode & 2}
        <table class="form-layout-compressed">
        <tr><td class="description">{ts}If you have a PayPal account, you can click the PayPal button to continue. Otherwise, fill in the credit card and billing information on this form and click <strong>Continue</strong> at the bottom of the page.{/ts}</td></tr>
{if $buttonType eq 'upload'}
   {assign var=expressButtonName value='_qf_Main_upload_express'}
{else}
   {assign var=expressButtonName value='_qf_Main_next_express'}
{/if}
        <tr><td>{$form.$expressButtonName.html} <span style="font-size:11px; font-family: Arial, Verdana;">Save time.  Checkout securely.  Pay without sharing your financial information. </span></td></tr>
        </table>
    {/if}
    {if $paymentProcessor.billing_mode & 1}
        <table class="form-layout-compressed">
        <tr><td class="label">{$form.credit_card_type.label}</td><td colspan="2">{$form.credit_card_type.html}</td></tr>
        <tr><td class="label">{$form.credit_card_number.label}</td><td colspan="2">{$form.credit_card_number.html}<br />
            <span class="description">{ts}Enter numbers only, no spaces or dashes.{/ts}</span></td></tr>
        <tr><td class="label">{$form.cvv2.label}</td><td style="vertical-align: top;">{$form.cvv2.html}</td><td><img src="{$config->resourceBase}i/mini_cvv2.gif" alt="{ts}Security Code Location on Credit Card{/ts}" style="vertical-align: text-bottom;" /><br />
            <span class="description">{ts}Usually the last 3-4 digits in the signature area on the back of the card.{/ts}</span></td></tr>
        <tr><td class="label">{$form.credit_card_exp_date.label}</td><td colspan="2">{$form.credit_card_exp_date.html}</td></tr>
        </table>
        </fieldset>
        
        <fieldset><legend>{ts}Billing Name and Address{/ts}</legend>
        <table class="form-layout-compressed">
        <tr><td colspan="2" class="description">{ts}Enter the name as shown on your credit or debit card, and the billing address for this card.{/ts}</td></tr>
        <tr><td class="label">{$form.billing_first_name.label} </td><td>{$form.billing_first_name.html}</td></tr>
        <tr><td class="label">{$form.billing_middle_name.label}</td><td>{$form.billing_middle_name.html}</td></tr>
        <tr><td class="label">{$form.billing_last_name.label}</td><td>{$form.billing_last_name.html}</td></tr>
	{assign var=n value=street_address-$bltID}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        {assign var=n value=city-$bltID}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        {assign var=n value=state_province_id-$bltID}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        {assign var=n value=postal_code-$bltID}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        {assign var=n value=country_id-$bltID}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        </table>
    {/if}
    </fieldset>
</div>
{/if}
{/if}

{include file="CRM/UF/Form/Block.tpl" fields=$customPost}

{if $isCaptcha}
  {include file='CRM/common/ReCAPTCHA.tpl'}
{/if}

{if $is_monetary}
{* Put PayPal Express button after customPost block since it's the submit button in this case. *}
{if $paymentProcessor.payment_processor_type EQ 'PayPal_Express'}
    {assign var=expressButtonName value='_qf_Main_next_express'}
    <fieldset><legend>{ts}Checkout with PayPal{/ts}</legend>
    <table class="form-layout-compressed">
    <tr><td class="description">{ts}Click the PayPal button to continue.{/ts}</td></tr>
    <tr><td>{$form.$expressButtonName.html} <span style="font-size:11px; font-family: Arial, Verdana;">Checkout securely.  Pay without sharing your financial information. </span></td></tr>
    </table>
    </fieldset>
{/if}
{/if}

<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
{if $footer_text}
    <div id="footer_text">
    <p>
    {$footer_text}
    </p>
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

{* Disable pay later option if not monetary *}
{literal}
<script type="text/javascript">

var is_monetary = {/literal}{$is_monetary}{literal}

if (! is_monetary) {
   if ( document.getElementsByName("is_pay_later")[0] ) {
       document.getElementsByName("is_pay_later")[0].disabled = true;
   }
}

</script>
{/literal}
