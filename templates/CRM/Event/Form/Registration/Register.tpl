<div class="form-item">
 {ts}<strong>{$eventPage.intro_text}{/ts}<br/>	

 <table class="form-layout-compressed">
    <tr><td class="label nowrap">{$form.amount.label}</td><td>{$form.amount.html}</td></tr>
    <tr><td class="label nowrap">{$form.email.label}</td><td>{$form.email.html}</td></tr>
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
        <tr><td class="label">{$form.credit_card_type.label}{$reqMark}</td><td>{$form.credit_card_type.html}</td></tr>
        <tr><td class="label">{$form.credit_card_number.label}{$reqMark}</td><td>{$form.credit_card_number.html}<br />
            <span class="description">{ts}Enter numbers only, no spaces or dashes.{/ts}</span></td></tr>
        <tr><td class="label">{$form.cvv2.label}{$reqMark}</td><td>{$form.cvv2.html} &nbsp; <img src="{$config->resourceBase}i/mini_cvv2.gif" alt="{ts}Security Code Location on Credit Card{/ts}" style="vertical-align: text-bottom;" /><br />
            <span class="description">{ts}Usually the last 3-4 digits in the signature area on the back of the card.{/ts}</span></td></tr>
        <tr><td class="label">{$form.credit_card_exp_date.label}{$reqMark}</td><td>{$form.credit_card_exp_date.html}</td></tr>
      </table>
      </fieldset>

      <fieldset><legend>{ts}Billing Name and Address{/ts}</legend>
      <table class="form-layout-compressed">
	  <tr><td colspan="2" class="description">{ts}Enter the name as shown on your credit or debit card, and the billing address for this card.{/ts}</td></tr>
	  <tr><td class="label">{$form.first_name.label} {$reqMark}</td><td>{$form.first_name.html}</td></tr>
	  <tr><td class="label">{$form.middle_name.label}</td><td>{$form.middle_name.html}</td></tr>
	  <tr><td class="label">{$form.last_name.label} {$reqMark}</td><td>{$form.last_name.html}</td></tr>
	  <tr><td class="label">{$form.street_address.label} {$reqMark}</td><td>{$form.street_address.html}</td></tr>
	  <tr><td class="label">{$form.city.label} {$reqMark}</td><td>{$form.city.html}</td></tr>
	  <tr><td class="label">{$form.state_province_id.label} {$reqMark}</td><td>{$form.state_province_id.html}</td></tr>
	  <tr><td class="label">{$form.postal_code.label} {$reqMark}</td><td>{$form.postal_code.html}</td></tr>
	  <tr><td class="label">{$form.country_id.label} {$reqMark}</td><td>{$form.country_id.html}</td></tr>
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

 {ts}<strong>{$eventPage.footer_text}{/ts}<br/>	  
   <div id="crm-submit-buttons">
     {$form.buttons.html}
   </div>
</div>

