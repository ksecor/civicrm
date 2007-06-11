<div class="form-item">
    <table class="form-layout-compressed">
    <tr>
        <td class="label nowrap">{$form.payment_processor_id.label}</td><td>{$form.payment_processor_id.html}</td>
    </tr>
    <tr>
        <td class="label nowrap">{$form.contribution_type_id.label}</td><td>{$form.contribution_type_id.html}</td>
    </tr>
    <tr>
        <td class="label nowrap">{$form.total_amount.label}</td><td>{$form.total_amount.html}</td>
    </tr>
    <tr>
	{assign var=n value=email-$bltID}
        <td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td>
    </tr>
    <tr>
        <td class="label nowrap">{$form.source.label}</td><td>{$form.source.html}</td>
    </tr>
        <tr><td class="label">{$form.is_email_receipt.label}</td><td>{$form.is_email_receipt.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Email a receipt for this contribution to {$email}?{/ts}</td></tr>
    </table>

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
        <tr><td colspan="2" class="description">{ts}Enter the name as shown on your credit or debit card, and the billing address for this card.{/ts}</td></tr>
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

<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>

</div>