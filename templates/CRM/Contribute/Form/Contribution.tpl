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
        <tr><td class="label font-size12pt">{ts}From{/ts}</td><td class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</td></tr>
        <tr><td class="label">{$form.contribution_type_id.label}</td><td>{$form.contribution_type_id.html}</td></tr> 
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Select the appropriate contribution type for this transaction.{/ts}</td></tr>
        <tr><td class="label">{$form.receive_date.label}</td><td>{$form.receive_date.html}
{if $hideCalender neq true}
{include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_1}
{include file="CRM/common/calendar/body.tpl" dateVar=receive_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_contribution_1}
{/if}    
</td>
</tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}The date this contribution was received.{/ts}</td></tr>
        <tr><td class="label">{$form.payment_instrument_id.label}</td><td>{$form.payment_instrument_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}This field is blank for non-monetary contributions.{/ts}</td></tr>
        <tr><td class="label">{$form.source.label}</td><td>{$form.source.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Optional identifier for the contribution source (campaign name, event, mailer, etc.).{/ts}</td></tr>
        <tr><td class="label">{$form.note.label}</td><td>{$form.note.html}</td></tr>
        <tr><td class="label">{$form.total_amount.label}</td><td>{$form.total_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Actual amount given by contributor.{/ts}</td></tr>
        <tr><td class="label">{$form.non_deductible_amount.label}</td><td>{$form.non_deductible_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Non-deductible portion of this contribution.{/ts}</td></tr>
        <tr><td class="label">{$form.fee_amount.label}</td><td>{$form.fee_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Processing fee for this transaction (if applicable).{/ts}</td></tr>
        <tr><td class="label">{$form.net_amount.label}</td><td>{$form.net_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Net value of the contribution (Total Amount minus Fee).{/ts}</td></tr>
        <tr><td class="label">{$form.invoice_id.label}</td><td>{$form.invoice_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Unique internal reference ID for this contribution.{/ts}</td></tr>
        <tr><td class="label">{$form.trxn_id.label}</td><td>{$form.trxn_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Unique payment ID for this transaction. The Payment Processor's transaction ID will be automatically stored here on online contributions.{/ts}<br />{ts}For offline contributions, you can enter an account+check number, bank transfer identifier, etc.{/ts}</td></tr>
        <tr><td class="label">{$form.receipt_date.label}</td><td>{$form.receipt_date.html}
{include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
{include file="CRM/common/calendar/body.tpl" dateVar=receipt_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_contribution_2}
</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Date that a receipt was sent to the contributor.{/ts}</td></tr>
        <tr><td class="label">{$form.thankyou_date.label}</td><td>{$form.thankyou_date.html}
{include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_3}
{include file="CRM/common/calendar/body.tpl" dateVar=thankyou_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_contribution_3}
</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Date that a thank-you message was sent to the contributor.{/ts}</td></tr>
        <tr><td class="label">{$form.cancel_date.label}</td><td>{$form.cancel_date.html}
{include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_4}
{include file="CRM/common/calendar/body.tpl" dateVar=cancel_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_contribution_4}
</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}To mark a contribution as cancelled, enter the cancellation date here.{/ts}</td></tr>
        <tr><td class="label" style="vertical-align: top;">{$form.cancel_reason.label}</td><td>{$form.cancel_reason.html|crmReplace:class:huge}</td></tr>
      </table>
      {if $premiums }
      <fieldset><legend>{ts}Premium Information{/ts}</legend> 
        <table class="form-layout-compressed">
           <tr><td class="label">{$form.product_name.label}</td><td>{$form.product_name.html}</td></tr>
           <tr><td class="label">{$form.min_amount.label}</td><td>{$form.min_amount.html|crmReplace:class:texttolabel}</td></tr>
           <tr><td class="label">{$form.fulfilled_date.label}</td><td>{$form.fulfilled_date.html}
           {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_5}
           {include file="CRM/common/calendar/body.tpl" dateVar=fulfilled_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_contribution_5}      
           </td></tr>
        </table>            
      </fieldset>
      {/if} 
      {include file="CRM/Contact/Form/CustomData.tpl" mainEditForm=1}

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
      <dt>&nbsp;</dt><dt>{$form.buttons.html}</dt> 
    </dl> 
</fieldset> 
</div> 





{if $action eq 1 or $action eq 2 }
    <script type="text/javascript">
    showMinContrib( );
    </script>            
{/if}

{if $action ne 2 or $showOption eq true}
{$initHideBoxes}
{/if}
