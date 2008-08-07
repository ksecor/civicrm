{if $paid} {* We retrieve this tpl when event is selected - keep it empty if event is not paid *} 
<fieldset>
    <div class="form-item">
    <table class="form-layout">
    {if $priceSet}
     <tr>  
     <td class="label">{$form.amount.label}</td>
     <td><table class="form-layout-compressed">
         {if $priceSet.help_pre AND $action eq 1}
            <tr><td colspan=2 class="description">{$priceSet.help_pre}</td></tr>
         {/if}
      {foreach from=$priceSet.fields item=element key=field_id}
         {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
            {assign var="element_name" value=price_$field_id}
            {assign var="count" value="1"}
            <tr><td class="label">{$form.$element_name.label}</td>
                <td class="view-value">
                <table class="form-layout-compressed">
                {foreach name=outer key=key item=item from=$form.$element_name}
                    <tr>	
                        {if is_numeric($key) }
                            <td class="labels font-light"><td>{$form.$element_name.$key.html}</td>
                            {if $count == $element.options_per_line}
                                {assign var="count" value="1"}
                                </tr>
                                <tr>			
                            {else}
                                {assign var="count" value=`$count+1`}
                            {/if}
                        {/if}
                    </tr>
                {/foreach}
                {if $element.help_post AND $action eq 1}
                    <tr><td></td><td class="description">{$element.help_post}</td></tr>
                {/if}
                </table>
              </td>
            </tr>
          {else}	
            {assign var="name" value=`$element.name`}
            {assign var="element_name" value="price_"|cat:$field_id}
            <tr><td class="label"> {$form.$element_name.label}</td>
                <td class="view-value">{$form.$element_name.html}
                    {if $element.help_post AND $action eq 1}
                        <br /><span class="description">{$element.help_post}</span>
                    {/if}
               </td>
            </tr>
          {/if}
       {/foreach}
         {if $priceSet.help_post AND $action eq 1}
            <tr><td colspan=2 class="description">{$priceSet.help_post}</td></tr>
         {/if}
      </table>
    </td>
    </tr>

    {else} {* NOT Price Set *}
     <tr>
     <td class ='html-adjust' colspan=2>
     	<dl class="html-adjust">
        {if $form.discount_id.label}
            <dt class="label">{$form.discount_id.label}</dt><dd>{$form.discount_id.html}</dd>
        {/if}
        <dt class="label">{$form.amount.label}<span class="marker"> *</span></dt><dd>{$form.amount.html}
        {if $action EQ 1}
            <br /><span class="description">{ts}Event Fee Level (if applicable).{/ts}</span>
        {/if}
        </dd>
     	</dl>
     </td>
     </tr>
    {/if}

     {if ! $participantMode}
        <tr>
        <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$form.record_contribution.label}</td>
        <td>{$form.record_contribution.html}<br />
            <span class="description">{ts}Check this box to enter payment information. You will also be able to generate a customized receipt.{/ts}</span>
        </td>
        </tr>
        <tr id="payment_information">
           <td class ='html-adjust' colspan=2>
           <fieldset><legend>{ts}Payment Information{/ts}</legend>
             <dl id="recordContribution" class="html-adjust">	        
                <dt class="label">{$form.contribution_type_id.label}</dt>
                <dd>{$form.contribution_type_id.html}<br /><span class="description">{ts}Select the appropriate contribution type for this payment.{/ts}</span></dd>
                <dt class="label" >{$form.receive_date.label}</dt><dd>{$form.receive_date.html}
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership}
                {include file="CRM/common/calendar/body.tpl" dateVar=receive_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership}</dd> 
                <dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
                {if $action neq 2 and $showTransactionId }	
                    <dt class="label">{$form.trxn_id.label}</dt><dd>{$form.trxn_id.html}</dd>	
                {/if}	
                <dt class="label">{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>      
             </dl>
           </fieldset>
           </td>
        </tr>

    {* Record contribution field only present if this is a paid event. *}
    {include file="CRM/common/showHideByFieldValue.tpl" 
        trigger_field_id    ="record_contribution"
        trigger_value       =""
        target_element_id   ="payment_information" 
        target_element_type ="table-row"
        field_type          ="radio"
        invert              = 0
    }
    {/if}
    </table>
    </div>
</fieldset>
{/if}

{* credit card block when it is live or test mode*}
{if $participantMode and $paid}	
 <div class="spacer"></div>
 <fieldset><legend>{ts}Credit or Debit Card Information{/ts}</legend>
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
        <tr><td colspan="2" class="description">{ts}Enter the name as shown on the credit or debit card, and the billing address for this card.{/ts}</td></tr>
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
    </fieldset>
{/if}

{if $email OR $batchEmail}
    <fieldset><legend>{if $paid}{ts}Registration Confirmation and Receipt{/ts}{else}{ts}Registration Confirmation{/ts}{/if}</legend>  
      <div class="form-item">
		 <dl> 
            <dt class="label">{if $paid}{ts}Send Confirmation and Receipt{/ts}{else}{ts}Send Confirmation{/ts}{/if}</dt>
            <dd class ='html-adjust' >{$form.send_receipt.html}<br>
                     <span class="description">{ts}Automatically email a confirmation {if $paid} and receipt {/if} to {$email}?{/ts}</span></dd>
        </dl>
        <div id='notice'>
            <dl>
 			<dt class="label">{$form.receipt_text.label}</dt> 
                	<dd class="html-adjust"><span class="description">{ts}Enter a message you want included at the beginning of the confirmation email. EXAMPLE: 'Thanks for registering for this event.'{/ts}</span><br/>
                    {$form.receipt_text.html|crmReplace:class:huge}</dd>
            </dl>
        </div> 
      </div>
    </fieldset>
{/if}
{if $email} {* Send receipt field only present if contact has a valid email address. *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="send_receipt"
    trigger_value       =""
    target_element_id   ="notice" 
    target_element_type ="block"
    field_type          ="radio"
    invert              = 0
}
{/if}
