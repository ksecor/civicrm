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

{if $priceSet}
    <fieldset><legend>{$event.fee_label}</legend>
    <dl>
{if $priceSet.help_pre}
  <dt>&nbsp;</dt>
  <dd class="description">{$priceSet.help_pre}</dd>
{/if}
    {foreach from=$priceSet.fields item=element key=field_id}
        {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
            {assign var="element_name" value=price_$field_id}
            <dt>{$form.$element_name.label}</dt>
            <dd>
            {assign var="count" value="1"}
                <table class="form-layout-compressed">
                    <tr>
                    {foreach name=outer key=key item=item from=$form.$element_name}
                        {if is_numeric($key) }
                                <td class="labels font-light">{$form.$element_name.$key.html}</td>
                            {if $count == $element.options_per_line}
                            {assign var="count" value="1"}
                            </tr>
                            <tr>
                            {else}
                                {assign var="count" value=`$count+1`}
                            {/if}
                        {/if}
                    {/foreach}
                    </tr>
                </table>
            </dd>
        {else}
            {assign var="name" value=`$element.name`}
            {assign var="element_name" value="price_"|cat:$field_id}
            <dt>{$form.$element_name.label}</dt>
            <dd>&nbsp;{$form.$element_name.html}</dd>
        {/if}
        {if $element.help_post}
            <dt>&nbsp;</dt>
            <dd class="description">{$element.help_post}</dd>
        {/if}
    {/foreach}
{if $priceSet.help_post}
  <dt>&nbsp;</dt>
  <dd class="description">{$priceSet.help_post}</dd>
{/if}
    </dl>
    </fieldset>
    <dl>
        {if $form.is_pay_later}
            <dt>&nbsp;</dt>
            <dd>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</dd>
        {/if}
    </dl>
{else}
    {if $paidEvent}
     <table class="form-layout-compressed">
        <tr><td class="label nowrap">{$event.fee_label} <span class="marker">*</span></td>
            <td>&nbsp;</td>
            <td>{$form.amount.html}</td>
        </tr>
        {if $form.is_pay_later}
        <tr><td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</td>
        </tr>
        {/if}
    </table>
    {/if}
{/if}

{assign var=n value=email-$bltID}
<table class="form-layout-compressed">
    <tr><td class="label nowrap">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
 </table>
 {if $form.additional_participants.html}
 <div id="noOfparticipants_show" class="section-hidden section-hidden-border">
        <a href="#" onclick="hide('noOfparticipants_show'); show('noOfparticipants'); document.forms[1].additional_participants.focus(); return false;">&raquo; <label>{ts}Register additional people for this event{/ts}</label></a>
    </div>
{/if}
    <div id="noOfparticipants" class="section-hidden section-hidden-border" style="display:none">
        <a href="#" onclick="hide('noOfparticipants'); show('noOfparticipants_show'); return false;">&raquo; <label>{ts}Register additional people for this event{/ts}</label></a>
        <div class="form-item">
            <table class="form-layout">
            <tr><td><label>{$form.additional_participants.label}</label></td>
                <td>{$form.additional_participants.html|crmReplace:class:two}<br />
                    <span class="description">{ts}You will be able to enter registration information for each additional person after you complete this page and click Continue.{/ts}</span>
                </td>
       	    </tr>
            </table>
        </div>
    </div> 

{* User account registration option. Displays if enabled for one of the profiles on this page. *}
{include file="CRM/common/CMSUser.tpl"}

{include file="CRM/UF/Form/Block.tpl" fields=$customPre} 

{if $paidEvent}   
{if $form.credit_card_number}
<div id="payment_information">
 <fieldset><legend>{ts}Credit or Debit Card Information{/ts}</legend>
    {if $paymentProcessor.billing_mode & 2}
      <table class="form-layout-compressed">
        <tr><td class="description">{ts}If you have a PayPal account, you can click the PayPal button to continue. Otherwise, fill in the credit card and billing information on this form and click <strong>Continue</strong> at the bottom of the page.{/ts}</td></tr>
{if $buttonType eq 'upload'}
   {assign var=expressButtonName value='_qf_Register_upload_express'}
{else}
   {assign var=expressButtonName value='_qf_Register_next_express'}
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
</div>
{/if}        
{/if}        

{include file="CRM/UF/Form/Block.tpl" fields=$customPost}   

{if $isCaptcha}
  {include file='CRM/common/ReCAPTCHA.tpl'}
{/if}

{* Put PayPal Express button after customPost block since it's the submit button in this case. *}
{if $paymentProcessor.payment_processor_type EQ 'PayPal_Express'}
    <fieldset><legend>{ts}Checkout with PayPal{/ts}</legend>
    <table class="form-layout-compressed">
    <tr><td class="description">{ts}Click the PayPal button to continue.{/ts}</td></tr>
    <tr><td>{$form.$expressButtonName.html} <span style="font-size:11px; font-family: Arial, Verdana;">Checkout securely.  Pay without sharing your financial information. </span></td></tr>
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

{* Hide Credit Card Block and Billing information if registration is pay later. *}
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