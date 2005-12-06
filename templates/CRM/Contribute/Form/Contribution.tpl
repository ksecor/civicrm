{* this template is used for adding/editing/deleting contribution *} 
<div class="form-item"> 
<fieldset><legend>{if $action eq 1}{ts}New Contribution{/ts}{elseif $action eq 8}{ts}Delete Contribution{/ts}{else}{ts}Edit Contribution{/ts}{/if}</legend> 
   
   {if $action eq 8} 
      <div class="messages status"> 
        <dl> 
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt> 
          <dd>     
          {ts}WARNING: Deleting this contribution will result in the loss of this contribution and the associated financial transactions (if any).{/ts} {ts}Do you want to continue?{/ts} 
          </dd> 
       </dl> 
      </div> 
   {else} 
      <dl> 
        <dt>{ts}From{/ts}</dt><dd><strong>{$displayName}</strong>&nbsp;</dd>
        <dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}Select the appropriate contribution type for this transaction.{/ts}</dd>
        <dt>{$form.receive_date.label}</dt><dd>{$form.receive_date.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}The date this contribution was received.{/ts}</dd>
        <dt>{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}Leave this field blank for non-monetary contributions.{/ts}</dd>
        <dt>{$form.source.label}</dt><dd>{$form.source.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}Optional identifier for the contribution source (campaign name, event, mailer, etc.).{/ts}</dd>
        <dt>{$form.total_amount.label}</dt><dd>{$form.total_amount.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}Actual amount given by contributor.{/ts}</dd>
        <dt>{$form.non_deductible_amount.label}</dt><dd>{$form.non_deductible_amount.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}You can optionally record a non-deductible portion of this contribution.{/ts}</dd>
        <dt>{$form.fee_amount.label}</dt><dd>{$form.fee_amount.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}Processing fee for this transaction (if applicable).{/ts}</dd>
        <dt>{$form.net_amount.label}</dt><dd>{$form.net_amount.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}Net value of the contribution (Total Amount minus Fee).{/ts}</dd>
        <dt>{$form.trxn_id.label}</dt><dd>{$form.trxn_id.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}Unique identifier for this transaction. Online contributions will have the Payment Processor's tranaction ID here. For offline contributions, you can enter an account+check number, bank transfer identifier, etc.{/ts}</dd>
        <dt>{$form.receipt_date.label}</dt><dd>{$form.receipt_date.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}Date that a receipt was sent to the contributor.{/ts}</dd>
        <dt>{$form.thankyou_date.label}</dt><dd>{$form.thankyou_date.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Date that a thank-you message was sent to the contributor.{/ts}</dd>
        <dt>{$form.cancel_date.label}</dt><dd>{$form.cancel_date.html}</dd> 
        <dt>&nbsp;</dt><dd class="description">{ts}To mark a contribution as cancelled, enter the cancellation date here.{/ts}</dd>
        <dt>{$form.cancel_reason.label}</dt><dd>{$form.cancel_reason.html|crmReplace:class:huge}</dd> 
        {include file="CRM/Contact/Form/CustomData.tpl" mainEditForm=1}
      </dl>  
     {/if} 
    <dl>    
      <dt></dt><dd>{$form.buttons.html}</dd> 
    </dl> 
</fieldset> 
</div> 
