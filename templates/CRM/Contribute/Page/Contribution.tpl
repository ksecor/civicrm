<div class="form-item">  
<fieldset>
      <legend>{ts}View Contribution{/ts}</legend>
      <dl>  
        <dt>{ts}Contribution Type{/ts}</dt><dd>{$contribution_type}</dd>
        <dt>{ts}Receive Date{/ts}</dt><dd>{$receive_date}</dd>
        <dt>{ts}Payment Instrument{/ts}</dt><dd>{$payment_instrument}</dd>
        <dt>{ts}Source{/ts}</dt><dd>{$source}</dd>
        <dt>{ts}Total Amount{/ts}</dt><dd>{$total_amount}</dd>
        <dt>{ts}Non Deductible Amount{/ts}</dt><dd>{$non_deductible_amount}</dd>
        <dt>{ts}Fee Amount{/ts}</dt><dd>{$fee_amount}</dd>
        <dt>{ts}Net Amount{/ts}</dt><dd>{$net_amount}</dd>
        <dt>{ts}Transaction ID{/ts}</dt><dd>{$trxn_id}</dd>
{if $receipt_date}
        <dt>{ts}Receipt Date{/ts}</dt><dd>{$receipt_date}</dd>
{/if}
{if $thankyou_date}
        <dt>{ts}Thank You Date{/ts}</dt><dd>{$thankyou_date}</dd>
{/if}
{if $cancel_date}
        <dt>{ts}Cancel Date{/ts}</dt><dd>{$cancel_date}</dd>
        <dt>{ts}Cancel Reason{/ts}</dt><dd>{$cancel_reason}</dd>
{/if}
</fieldset>  
</div>  
 