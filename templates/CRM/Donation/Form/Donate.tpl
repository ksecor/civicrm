{* add/update/view donationpage *}

<div class="form-item">
    <fieldset><legend>{ts}Donation Page{/ts}</legend>
    <dl>
    <dt>{$form.amount.label}</dt><dd>{$form.amount.html}</dd>
    <dt>{$form.email.label}</dt><dd>{$form.email.html}</dd>
    <dt></dt><dd>{$form._qf_Donate_next_express.html}</dd>
    <dt>{$form.first_name.label}</dt><dd>{$form.first_name.html}</dd>
    <dt>{$form.middle_name.label}</dt><dd>{$form.middle_name.html}</dd>
    <dt>{$form.last_name.label}</dt><dd>{$form.last_name.html}</dd>
    <dt>{$form.street1.label}</dt><dd>{$form.street1.html}</dd>
    <dt>{$form.city.label}</dt><dd>{$form.city.html}</dd>
    <dt>{$form.state_province.label}</dt><dd>{$form.state_province.html}</dd>
    <dt>{$form.postal_code.label}</dt><dd>{$form.postal_code.html}</dd>
    <dt>{$form.country_id.label}</dt><dd>{$form.country_id.html}</dd>
    <dt>{$form.credit_card_number.label}</dt><dd>{$form.credit_card_number.html}</dd>
    <dt>{$form.cvv2.label}</dt><dd>{$form.cvv2.html}</dd>
    <dt>{$form.credit_card_type.label}</dt><dd>{$form.credit_card_type.html}</dd>
    <dt>{$form.credit_card_exp_date.label}</dt><dd>{$form.credit_card_exp_date.html}</dd>

    <div id="crm-submit-buttons">
      <dt></dt><dd>{$form.buttons.html}</dd>
    </div>

    </dl>
    </fieldset>
</div>