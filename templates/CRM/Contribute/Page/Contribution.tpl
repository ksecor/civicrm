<div class="form-item">  
<fieldset>
      <legend>{ts}View Contribution{/ts}</legend>
      <dl>  
        <dt>{ts}From{/ts}</dt><dd><strong>{$displayName}</strong>&nbsp;</dd>
        <dt>{ts}Contribution Type{/ts}</dt><dd>{$contribution_type}</dd>
        <dt>{ts}Received{/ts}</dt><dd>{$receive_date|truncate:10:''|crmDate}</dd>
        <dt>{ts}Paid By{/ts}</dt><dd>{$payment_instrument}</dd>
        <dt>{ts}Source{/ts}</dt><dd>{$source}</dd>
        <dt>{ts}Total Amount{/ts}</dt><dd>{$total_amount|crmMoney}</dd>
        <dt>{ts}Non-deductible Amount{/ts}</dt><dd>{$non_deductible_amount|crmMoney}</dd>
        <dt>{ts}Fee Amount{/ts}</dt><dd>{$fee_amount|crmMoney}</dd>
        <dt>{ts}Net Amount{/ts}</dt><dd>{$net_amount|crmMoney}</dd>
        <dt>{ts}Transaction ID{/ts}</dt><dd>{$trxn_id}</dd>
{if $receipt_date}
        <dt>{ts}Receipt Sent{/ts}</dt><dd>{$receipt_date|truncate:10:''|crmDate}</dd>
{/if}
{if $thankyou_date}
        <dt>{ts}Thank-you Sent{/ts}</dt><dd>{$thankyou_date|truncate:10:''|crmDate}</dd>
{/if}
{if $cancel_date}
        <dt>{ts}Cancelled{/ts}</dt><dd>{$cancel_date|truncate:10:''|crmDate}</dd>
        <dt>{ts}Cancellation Reason{/ts}</dt><dd>{$cancel_reason}</dd>
{/if}
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>  
</div>  
 