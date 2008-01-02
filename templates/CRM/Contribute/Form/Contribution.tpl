{* this template is used for adding/editing/deleting contribution *} 
<div class="form-item"> 
<fieldset><legend>{if $action eq 1}{ts}New Contribution{/ts}{elseif $action eq 8}{ts}Delete Contribution{/ts}{else}{ts}Edit Contribution{/ts}{/if}</legend> 
   
   {if $action eq 8} 
      <div class="messages status"> 
        <dl> 
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          <dd> 
          {ts}WARNING: Deleting this contribution will result in the loss of the associated financial transactions (if any).{/ts} {ts}Do you want to continue?{/ts} 
          </dd> 
       </dl> 
      </div> 
   {else} 
      <table class="form-layout-compressed">
        <tr>
            <td class="font-size12pt right"><strong>{ts}Contributor{/ts}</strong></td><td class="font-size12pt"><strong>{$displayName}</strong></td>
        </tr>
        <tr><td class="label">{$form.contribution_type_id.label}</td><td>{$form.contribution_type_id.html}&nbsp;
        {if $is_test}
        {ts}(test){/ts}
        {/if}
        </td></tr> 
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Select the appropriate contribution type for this transaction.{/ts}</td></tr>
        <tr><td class="label">{$form.total_amount.label}</td><td>{$config->defaultCurrencySymbol()}&nbsp;{$form.total_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Actual amount given by contributor.{/ts}</td></tr>
        <tr><td class="label">{$form.receive_date.label}</td><td>{$form.receive_date.html}
        {if $hideCalender neq true}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=receive_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_1}
        {/if}    
        </td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}The date this contribution was received.{/ts}</td></tr>

        <tr><td class="label">{$form.payment_instrument_id.label}</td><td>{$form.payment_instrument_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Leave blank for non-monetary contributions.{/ts}</td></tr>
        <tr><td class="label">{$form.source.label}</td><td>{$form.source.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Optional identifier for the contribution source (campaign name, event, mailer, etc.).{/ts}</td></tr>
        {if $email}
            <tr><td class="label">{$form.is_email_receipt.label}</td><td>{$form.is_email_receipt.html}</td></tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Automatically email a receipt for this contribution to {$email}?{/ts}</td></tr>
        {/if}
        <tr id="receiptDate"><td class="label">{$form.receipt_date.label}</td><td>{$form.receipt_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=receipt_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}Date that a receipt was sent to the contributor.{/ts}</span></td></tr>
        <tr><td class="label">{$form.contribution_status_id.label}</td><td>{$form.contribution_status_id.html}</td></tr>
        {* Cancellation fields are hidden unless contribution status is set to Cancelled *}
        <tr id="cancelInfo"> 
           <td>&nbsp;</td> 
           <td><fieldset><legend>{ts}Cancellation Information{/ts}</legend>
                <table class="form-layout-compressed">
                  <tr id="cancelDate"><td class="label">{$form.cancel_date.label}</td><td>{$form.cancel_date.html}
                   {if $hideCalendar neq true}
                     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_4}
                     {include file="CRM/common/calendar/body.tpl" dateVar=cancel_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_4}
                   {/if}
                   </td></tr>
                  <tr id="cancelDescription"><td class="label">&nbsp;</td><td class="description">{ts}Enter the cancellation date, or you can skip this field and the cancellation date will be automatically set to TODAY.{/ts}</td></tr>
                  <tr id="cancelReason"><td class="label" style="vertical-align: top;">{$form.cancel_reason.label}</td><td>{$form.cancel_reason.html|crmReplace:class:huge}</td></tr>
               </table>
               </fieldset>
           </td>
        </tr>
      </table>
      {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}

 <div id="id-additional-show" class="section-hidden section-hidden-border" style="clear: both;">
        <a href="#" onclick="hide('id-additional-show'); show('id-additional'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Additional Details{/ts}</label><br />
 </div>
 <div id="id-additional" class="section-shown">
    <fieldset>
      <legend><a href="#" onclick="hide('id-additional'); show('id-additional-show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Additional Details{/ts}</legend>
      <table class="form-layout-compressed">
        <tr><td class="label" style="vertical-align:top;">{$form.note.label}</td><td>{$form.note.html}</td></tr>
        <tr><td class="label">{$form.non_deductible_amount.label}</td><td>{$config->defaultCurrencySymbol()}&nbsp;{$form.non_deductible_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Non-deductible portion of this contribution.{/ts}</td></tr>
        <tr><td class="label">{$form.fee_amount.label}</td><td>{$config->defaultCurrencySymbol()}&nbsp;{$form.fee_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Processing fee for this transaction (if applicable).{/ts}</td></tr>
        <tr><td class="label">{$form.net_amount.label}</td><td>{$config->defaultCurrencySymbol()}&nbsp;{$form.net_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Net value of the contribution (Total Amount minus Fee).{/ts}</td></tr>
        <tr><td class="label">{$form.invoice_id.label}</td><td>{$form.invoice_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Unique internal reference ID for this contribution.{/ts}</td></tr>
        <tr><td class="label">{$form.trxn_id.label}</td><td>{$form.trxn_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Unique payment ID for this transaction. The Payment Processor's transaction ID will be automatically stored here on online contributions.{/ts}<br />{ts}For offline contributions, you can enter an account+check number, bank transfer identifier, etc.{/ts}</td></tr>
        <tr><td class="label">{$form.thankyou_date.label}</td><td>{$form.thankyou_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_3}
            {include file="CRM/common/calendar/body.tpl" dateVar=thankyou_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_3}
        </td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Date that a thank-you message was sent to the contributor.{/ts}</td></tr>
    </table>
  </fieldset>
 </div>     

       
 <div id="id-honoree-show" class="section-hidden section-hidden-border" style="clear: both;">
        <a href="#" onclick="hide('id-honoree-show'); show('id-honoree'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Honoree Information{/ts}</label><br />
 </div>
 <div id="id-honoree" class="section-shown">
    <fieldset>
      <legend><a href="#" onclick="hide('id-honoree'); show('id-honoree-show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Honoree Information{/ts}</legend>
      <table class="form-layout-compressed">
        <tr id="showHonorOfDetailsType"><td class="label">{$form.honor_type_id.label}</td><td>{$form.honor_type_id.html}</td></tr>  
        <tr id="showHonorOfDetailsPrefix"><td class="label">{$form.honor_prefix_id.label}</td><td>{$form.honor_prefix_id.html}</td></tr>
        <tr id="showHonorOfDetailsFname"><td class="label">{$form.honor_first_name.label}</td><td>{$form.honor_first_name.html}</td>
        <tr id="showHonorOfDetailsLname"><td class="label">{$form.honor_last_name.label}</td><td>{$form.honor_last_name.html}</td>
        <tr id="showHonorOfDetailsEmail"><td class="label">{$form.honor_email.label}</td><td>{$form.honor_email.html}</td>
      </table>
   </fieldset>
 </div>     

    {literal}
    <script type="text/javascript">
    function reload(refresh) {
        var contributionType = document.getElementById("contribution_type_id");
        var url = {/literal}"{$refreshURL}"{literal};
        var post = url + "&subType=" + contributionType.value;
        if ( refresh ) {
            window.location= post; 
        }
     }

     function verify( ) {
       var element = document.getElementsByName("is_email_receipt");
        if ( element[0].checked ) {
         var ok = confirm( "Click OK to save this contribution record AND send a receipt to the contributor now." );    
          if (!ok ) {
            return false;
          }
        }
     }
     function status() {
       document.getElementById("cancel_date[M]").value = "";
       document.getElementById("cancel_date[d]").value = "";
       document.getElementById("cancel_date[Y]").value = "";
       document.getElementById("cancel_reason").value = "";
     }

    </script>
    {/literal}

 <div id="id-premium-show" class="section-hidden section-hidden-border" style="clear: both;">
        <a href="#" onclick="hide('id-premium-show'); show('id-premium'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Premium Information{/ts}</label><br />
 </div>
 <div id="id-premium" class="section-shown">
      {if $premiums }
      <fieldset>
        <legend><a href="#" onclick="hide('id-premium'); show('id-premium-show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Premium Information{/ts}</legend>
           <dl>
           <dt class="label">{$form.product_name.label}</dt><dd>{$form.product_name.html}</dd>
           </dl>

           <div id="premium_contri">
            <dl>
            <dt class="label">{$form.min_amount.label}</dt><dd>{$form.min_amount.html|crmReplace:class:texttolabel}</dd>
            </dl>
            <div class="spacer"></div>
           </div>

           <dl>
           <dt class="label">{$form.fulfilled_date.label}</dt><dd>{$form.fulfilled_date.html}
           {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_5}
           {include file="CRM/common/calendar/body.tpl" dateVar=fulfilled_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_5}      
           </dd>
           </dl>

      </fieldset>
      {/if} 
</div>

      {literal}
        <script type="text/javascript">
            var min_amount = document.getElementById("min_amount");
            min_amount.readOnly = 1;
    	    function showMinContrib( ) {
               var product = document.getElementsByName("product_name[0]")[0];
               var product_id = product.options[product.selectedIndex].value;
               var min_amount = document.getElementById("min_amount");
 	 
	       
               var amount = new Array();
               amount[0] = '';  
	
               if( product_id > 0 ) {  
		  show('premium_contri');	      	
               } else {
	          hide('premium_contri');	      
             }
      {/literal}
     
      var index = 1;
      {foreach from= $mincontribution item=amt key=id}
            {literal}amount[index]{/literal} = "{$amt}"
            {literal}index = index + 1{/literal}
      {/foreach}
      {literal}
          if(amount[product_id]) {  
              min_amount.value = '$'+amount[product_id];
          } else {
              min_amount.value = "";
          }           
     }  
     </script> 
     {/literal}

     {/if} 
    <dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
    </dl> 
</fieldset> 
</div> 

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_email_receipt"
    trigger_value       =""
    target_element_id   ="receiptDate" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 1
}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="contribution_status_id"
    trigger_value       = '3'
    target_element_id   ="cancelInfo" 
    target_element_type ="table-row"
    field_type          ="select"
    invert              = 0
}

{if $action eq 1 or $action eq 2 }
    <script type="text/javascript">
       showMinContrib( );
    </script>            
{/if}

{if $action ne 2 or $showOption eq true}
{$initHideBoxes}
{/if}
{include file="CRM/common/showHide.tpl"}

