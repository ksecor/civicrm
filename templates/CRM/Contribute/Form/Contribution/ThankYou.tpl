{if $action eq 1024}
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"}
{/if}
<div class="form-item">
    <div id="thankyou_text">
        <p>
        {$thankyou_text}
        </p>
    </div>
    <div id="help">
        <p>{ts}Your contribution has been processed successfully. Please print this page for your
        records.{/ts}</p>
        {if $is_email_receipt}
            <p>{ts}An email receipt for this contribution has also been sent to {$email}{/ts}</p>
        {/if}
    </div>
    <div class="header-dark">
        {ts}Contribution Information{/ts}
    </div>
    <div class="display-block">
        {ts}Amount{/ts}: <strong>${$amount|string_format:"%01.2f"}</strong><br />
        {ts}Date{/ts}: <strong>{$receive_date}</strong><br />
        {ts}Transaction #{/ts}: {$trxn_id}
    </div>

    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$name}</strong><br />
        {$street_address}<br />
        {$city} {$state_province}, {$postal_code} &nbsp; {$country}
    </div>
    <div class="display-block">
        {$email}
    </div>

    {if $contributeMode eq 'direct'}
    <div class="header-dark">
        {ts}Credit or Debit Card Information{/ts}
    </div>
    <div class="display-block">
        {$credit_card_type}<br />
        {$credit_card_number}<br />
        {ts}Expires{/ts}: {$credit_card_exp_date}
    </div>
    {/if}

    <div id="thankyou_footer">
        <p>
        {$thankyou_footer}
        </p>
    </div>
</div>