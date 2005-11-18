<div class="form-item">
    <fieldset><legend>{$thankyou_title}</legend>
<p>
{$thankyou_text}
</p>
    <dl>
        <dt>Amount</dt><dd>${$amount}</dd> 
	<dt>Trxn ID</dt><dd>{$trxn_id}</dd>
        <dt>Name</dt><dd>{$name}</dd>
        <dt>Billing Address</dt><dd>{$street1}</dd>
        <dt>City</dt><dd>{$city}</dd>
        <dt>State</dt><dd>{$state_province}</dd>
        <dt>Postal Code</dt><dd>{$postal_code}</dd>
        <dt>Country</dt><dd>{$country}</dd>
{if $contributeMode eq 'direct'}
        <dt>Credit Card Number</dt><dd>{$credit_card_number}</dd>
        <dt>Credit Card Type</dt><dd>{$credit_card_type}</dd>
        <dt>Credit Card Exp Date</dt><dd>{$credit_card_exp_date}</dd>
{/if}
        <div id="crm-submit-buttons">
          <dt></dt><dd>{$form.buttons.html}</dd>
        </div>
    </dl>
    </fieldset>
</div>