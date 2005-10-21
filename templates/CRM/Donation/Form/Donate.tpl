{* add/update/view donationpage *}

<div class="form-item">
    <fieldset><legend>{ts}Donation Page{/ts}</legend>
    <dl>
    <dt>{$form.amount.label}</dt><dd>{$form.amount.html}</dd>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.email.label}</dt><dd>{$form.email.html}</dd>

        <div id="crm-submit-buttons">
        <dt></dt><dd>{$form.buttons.html}</dd>
        </div>
    </dl>
    </fieldset>
</div>