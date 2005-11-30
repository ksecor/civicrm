{* this template is used for adding/editing/deleting contribution *} 
<div class="form-item"> 
<fieldset><legend>{if $action eq 1}{ts}New Contribution{/ts}{elseif $action eq 2}{ts}Edit Contribution{/ts}{else}{ts}Delete Contribution{/ts}{/if}</legend> 
   
   {if $action eq 8} 
      <div class="messages status"> 
        <dl> 
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt> 
          <dd>     
          {ts}WARNING: Deleting this contribution will result in the loss of this contribution and the associated financial transactions (if any).{/ts}{ts}Do you want to continue?{/ts} 
          </dd> 
       </dl> 
      </div> 
   {else} 
      <dl> 
        <dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd> 
        <dt>{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd> 
        <dt>{$form.receive_date.label}</dt><dd>{$form.receive_date.html}</dd> 
        <dt>{$form.receipt_date.label}</dt><dd>{$form.receipt_date.html}</dd> 
        <dt>{$form.thankyou_date.label}</dt><dd>{$form.thankyou_date.html}</dd> 
        <dt>{$form.cancel_date.label}</dt><dd>{$form.cancel_date.html}</dd> 
        <dt>{$form.non_deductible_amount.label}</dt><dd>{$form.non_deductible_amount.html}</dd> 
        <dt>{$form.total_amount.label}</dt><dd>{$form.total_amount.html}</dd> 
        <dt>{$form.fee_amount.label}</dt><dd>{$form.fee_amount.html}</dd> 
        <dt>{$form.net_amount.label}</dt><dd>{$form.net_amount.html}</dd> 
        <dt>{$form.trxn_id.label}</dt><dd>{$form.trxn_id.html}</dd> 
        <dt>{$form.source.label}</dt><dd>{$form.source.html}</dd> 
        <dt>{$form.cancel_reason.label}</dt><dd>{$form.cancel_reason.html}</dd> 
      </dl>  
     {/if} 
    <dl>    
      <dt></dt><dd>{$form.buttons.html}</dd> 
    </dl> 
</fieldset> 
</div> 
