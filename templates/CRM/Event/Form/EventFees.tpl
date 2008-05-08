{if $paid} {* We retrieve this tpl when event is selected - keep it empty if event is not paid *} 
    <fieldset>
    {if $priceSet}
    <table class="form-layout">
     <tr>  
     <td class="label">{$form.amount.label}</td>
     <td><table class="form-layout-compressed">
      {foreach from=$priceSet.fields item=element key=field_id}
         {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
            {assign var="element_name" value=price_$field_id}
            {assign var="count" value="1"}
            <tr><td class="label"> {$form.$element_name.label}</td>
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
      </table>
    </td>
    </tr>
    </table>
    {else} {* NOT Price Set *}
    <table style="border: none;">
    <tr>
        <td class="label">{$form.amount.label}<span class="marker"> *</span></td><td>{$form.amount.html}
       {if $action EQ 1}
        <br /><span class="description">{ts}Event Fee Level (if applicable).{/ts}</span>
       {/if}
        </td>
    </tr>
    <tr>
       <td class="label">{$form.record_contribution.label}</td><td class="html-adjust">{$form.record_contribution.html}<br />
          <span class="description">{ts}Check this box to enter payment information. You will also be able to generate a customized receipt.{/ts}</span>
       </td>
    </tr>
    <tr id="payment_information">
       <td class ='html-adjust' colspan=2>
           <fieldset><legend>{ts}Payment Information{/ts}</legend>
             <div class="form-item">
                 <dl id="recordContribution" class="html-adjust">	        
                    <dt class="label">{$form.contribution_type_id.label}</dt>
                    <dd>{$form.contribution_type_id.html}<br /><span class="description">{ts}Select the appropriate contribution type for this payment.{/ts}</span></dd>
                    <dt class="label" >{$form.receive_date.label}</dt><dd>{$form.receive_date.html}
        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership}
        {include file="CRM/common/calendar/body.tpl" dateVar=receive_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_membership}</dd> 
                    <dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
		    <dt class="label">{$form.trxn_id.label}</dt><dd>{$form.trxn_id.html}</dd>	
                    <dt class="label">{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>      
                 </dl>
             </div>
           </fieldset>
       </td>
    </tr> 
    </table>
    {/if}
    </fieldset>
    
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
