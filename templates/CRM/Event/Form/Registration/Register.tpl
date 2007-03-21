{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}
{capture assign='reqMark'}<span class="marker"  title="{ts}This field is required.{/ts}">*</span>{/capture}
<div class="form-item">
{if $eventPage.intro_text}
    <div id="intro_text">
        <p>{$eventPage.intro_text}</p>
    </div>
{/if}

 <table class="form-layout-compressed">
    <tr><td class="label nowrap">{$form.amount.label}</td><td>{$form.amount.html}</td></tr>
	{assign var=n value=email-$bltID}
    <tr><td class="label nowrap">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
 </table>

 {include file="CRM/UF/Form/Block.tpl" fields=$customPre} 

{if $paidEvent}   
{if $form.credit_card_number}
 <fieldset><legend>{ts}Credit or Debit Card Information{/ts}</legend>
    {if $config->paymentBillingMode & 2}
      <table class="form-layout-compressed">
        <tr><td class="description">{ts}If you have a PayPal account, you can click the PayPal button to continue. Otherwise, fill in the credit card and billing information on this form and click <strong>Continue</strong> at the bottom of the page.{/ts}</td></tr>
        <tr><td>{$form._qf_Register_next_express.html} <span style="font-size:11px; font-family: Arial, Verdana;">Save time.  Checkout securely.  Pay without sharing your financial information. </span></td></tr>
      </table>
    {/if}
    {if $config->paymentBillingMode & 1}
      <table class="form-layout-compressed">
        <tr><td class="label">{$form.credit_card_type.label}</td><td>{$form.credit_card_type.html}</td></tr>
        <tr><td class="label">{$form.credit_card_number.label}</td><td>{$form.credit_card_number.html}<br />
            <span class="description">{ts}Enter numbers only, no spaces or dashes.{/ts}</span></td></tr>
        <tr><td class="label">{$form.cvv2.label}</td><td>{$form.cvv2.html} &nbsp; <img src="{$config->resourceBase}i/mini_cvv2.gif" alt="{ts}Security Code Location on Credit Card{/ts}" style="vertical-align: text-bottom;" /><br />
            <span class="description">{ts}Usually the last 3-4 digits in the signature area on the back of the card.{/ts}</span></td></tr>
        <tr><td class="label">{$form.credit_card_exp_date.label}</td><td>{$form.credit_card_exp_date.html}</td></tr>
      </table>
      </fieldset>

      <fieldset><legend>{ts}Billing Name and Address{/ts}</legend>
      <table class="form-layout-compressed">
	  <tr><td colspan="2" class="description">{ts}Enter the name as shown on your credit or debit card, and the billing address for this card.{/ts}</td></tr>
        <tr><td class="label">{$form.billing_first_name.label} </td><td>{$form.billing_first_name.html}</td></tr>
        <tr><td class="label">{$form.billing_middle_name.label}</td><td>{$form.billing_middle_name.html}</td></tr>
        <tr><td class="label">{$form.billing_last_name.label} </td><td>{$form.billing_last_name.html}</td></tr>
	{assign var=n value=street_address-$bltID}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        {assign var=n value=city-$bltID}
        <tr><td class="label">{$form.$n.label} </td><td>{$form.$n.html}</td></tr>
        {assign var=n value=state_province_id-$bltID}
        <tr><td class="label">{$form.$n.label} </td><td>{$form.$n.html}</td></tr>
        {assign var=n value=postal_code-$bltID}
        <tr><td class="label">{$form.$n.label} </td><td>{$form.$n.html}</td></tr>
        {assign var=n value=country_id-$bltID}
        <tr><td class="label">{$form.$n.label} </td><td>{$form.$n.html}</td></tr>
       </table> 
    {/if}
 </fieldset>
{/if}        
{/if}        

 {include file="CRM/UF/Form/Block.tpl" fields=$customPost}    

{* Put PayPal Express button after customPost block since it's the submit button in this case. *}
{if $config->paymentProcessor EQ 'PayPal_Express'}
    <fieldset><legend>{ts}Checkout with PayPal{/ts}</legend>
    <table class="form-layout-compressed">
    <tr><td class="description">{ts}Click the PayPal button to continue.{/ts}</td></tr>
    <tr><td>{$form._qf_Register_next_express.html} <span style="font-size:11px; font-family: Arial, Verdana;">Checkout securely.  Pay without sharing your financial information. </span></td></tr>
    </table>
    </fieldset>
{/if}

   <div id="crm-submit-buttons">
     {$form.buttons.html}
   </div>

    {if $eventPage.footer_text}
        <div id="footer_text">
            <p>{$eventPage.footer_text}</p>
        </div>
    {/if}
</div>

