{if $action eq 1024}
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"}
{/if}

<div class="form-item">
    <div id="help">
        <p>
        {ts}Please verify Contribution Amount, Billing Name and Address and Credit Card information carefully. Click <strong>Go Back</strong> if you need to make changes. To complete your contribution, click the <strong>Make Contribution</strong> button below.{/ts}
        </p> 
    </div>
    
    <div class="header-dark">
        {ts}Contribution Amount{/ts}
    </div>
    <div class="display-block">
        <strong>${$amount|string_format:"%01.2f"}</strong>
    </div>

    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$name}</strong><br />
        {$address|nl2br}
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
        {ts}Expires{/ts}: {$credit_card_exp_date}<br />
    </div>
    {/if}

    <div class="messages status">
        <p>
        {ts}Your contribution will not be completed until you click the <strong>Make Contribution</strong> button. Please click the button one time only.</strong>{/ts}
        </p>
    </div>
    <div id="crm-submit-buttons">
        {$form.buttons.html}
    </div>
</div>
