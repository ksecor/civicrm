<div class="form-item">
    <div id="help">
        <p>
        {ts}Please review the payment, name and address information carefully. Click <strong>Confirm
        Contribution</strong> to complete this transaction. Click <strong>Cancel</strong> to go back
        and make changes.{/ts} 
    </div>
    
    <table class="form-layout-compressed">
    <tr>
        <td class="label">{ts}Contribution Amount{/ts}</td><td><strong>${$amount|string_format:"%01.2f"}</strong></td>
    </tr>
    </table>

    <fieldset><legend>{ts}Billing Name and Address{/ts}</legend>
    <div id="billing-address" class="form-layout">
    <strong>{$name}</strong><br />
    {$street_address}<br />
    {$city} {$state_province}, {$postal_code} &nbsp; {$country}
    <p>
    {ts}Email:{/ts} {$email} 
    
    </div>
    </fieldset>

{if $contributeMode eq 'direct'}
    <fieldset><legend>{ts}Credit or Debit Card Information{/ts}</legend>
    <table class="form-layout-compressed">
    <tr>
        <td class="label">{ts}Card Type{/ts}</td><td>{$credit_card_type}</td>
    </tr>
    <tr>
        <td class="label">{ts}Card Number{/ts}</td><td>{$credit_card_number}</td>
    </tr>
    <tr>
        <td class="label">{ts}Expiration Date{/ts}</td><td>{$credit_card_exp_date}</td>
    </tr>
    </table>
    </fieldset>
{/if}

    <div id="crm-submit-buttons">
        {$form.buttons.html}
    </div>
</div>