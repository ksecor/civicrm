{literal}
<script language="javascript">
<!--
// Putting these functions directly in template so they are available for standalone forms

function useAmountOther() {
    for( i=0; i < document.Contribution.elements.length; i++) {
        element = document.Contribution.elements[i];
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
  if (document.Contribution.amount_other == null) return; // other_amt field not present; do nothing
  document.Contribution.amount_other.value = "";
}

//-->
</script>
{/literal}
{if $action eq 1024}
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"}
{/if}
{assign var='reqMark' value='<span class="marker">*</span>'}
<div class="form-item">
    <div id="intro_text">
    <p>
    {$intro_text}
    </p>
    </div>
 
    <table class="form-layout-compressed">
    <tr>
        <td class="label">{$form.amount.label}</td><td>{$form.amount.html}</td>
    </tr>
    {if $is_allow_other_amount}
        <tr><td class="label">{$form.amount_other.label}</td><td>{$form.amount_other.html}</td></tr>
    {/if}
    <tr>
        <td class="label">{$form.email.label}{$reqMark}</td><td>{$form.email.html}</td>
    </tr>
    </table>
    
    {include file="CRM/UF/Form/Block.tpl" fields=$customPre}

    <fieldset><legend>{ts}Credit or Debit Card Information{/ts}</legend>
    <table class="form-layout-compressed">
    <tr><td class="description">{ts}If you have a PayPal account, you can click the PayPal button to continue. Otherwise,
        fill in the credit card and billing information on this form and click <strong>Continue</strong> at the bottom of the page.{/ts}</td></tr>
    <tr><td>{$form._qf_Main_next_express.html}<span style="font-size:11px; font-family: Arial, Verdana;">Save time.  Checkout securely.  Pay without sharing your financial information.</span></td></tr>
    </table>
    <table class="form-layout-compressed">
    <tr><td class="label">{$form.credit_card_type.label}{$reqMark}</td><td>{$form.credit_card_type.html}</td></tr>
    <tr><td class="label">{$form.credit_card_number.label}{$reqMark}</td><td>{$form.credit_card_number.html}<br />
        <span class="description">{ts}Enter numbers only, no spaces or dashes.{/ts}</td></tr>
    <tr><td class="label">{$form.cvv2.label}{$reqMark}</td><td>{$form.cvv2.html} &nbsp; <img src="{$config->resourceBase}i/mini_cvv2.gif" alt="{ts}Security Code Location on Credit Card{/ts}" style="vertical-align: text-bottom;"><br />
        <span class="description">{ts}Usually the last 3-4 digits in the signature area on the back of the card.{/ts}</td></tr>
    <tr><td class="label">{$form.credit_card_exp_date.label}{$reqMark}</td><td>{$form.credit_card_exp_date.html}</td></tr>
    </table>
    </fieldset>
    
    <fieldset><legend>{ts}Billing Name and Address{/ts}</legend>
    <table class="form-layout-compressed">
    <tr><td colspan="2" class="description">{ts}Enter the name as shown on your credit or debit card, and the billing address for this card.{/ts}</td></tr>
    <tr><td class="label">{$form.first_name.label}{$reqMark}</td><td>{$form.first_name.html}</td></tr>
    <tr><td class="label">{$form.middle_name.label}</td><td>{$form.middle_name.html}</td></tr>
    <tr><td class="label">{$form.last_name.label}{$reqMark}</td><td>{$form.last_name.html}</td></tr>
    <tr><td class="label">{$form.street_address.label}{$reqMark}</td><td>{$form.street_address.html}</td></tr>
    <tr><td class="label">{$form.city.label}{$reqMark}</td><td>{$form.city.html}</td></tr>
    <tr><td class="label">{$form.state_province_id.label}{$reqMark}</td><td>{$form.state_province_id.html}</td></tr>
    <tr><td class="label">{$form.postal_code.label}{$reqMark}</td><td>{$form.postal_code.html}</td></tr>
    <tr><td class="label">{$form.country_id.label}{$reqMark}</td><td>{$form.country_id.html}</td></tr>
    </table>
    </fieldset>
    
    {include file="CRM/UF/Form/Block.tpl" fields=$customPost}

    <div id="crm-submit-buttons">
      {$form.buttons.html}
    </div>
</div>