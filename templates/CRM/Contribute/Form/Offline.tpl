<div class="form-item">
{if $action & 1024}
    {assign var=contribMode value="TEST"}
{else}
    {assign var=contribMode value="LIVE"}
{/if}
<div id="help">
    {ts 1=$displayName 2=$contribMode}Use this form to submit a new contribution on behalf of %1. <strong>A %2 transaction will be submitted</strong> using the selected payment processor.{/ts}
</div>
<fieldset>
    <table class="form-layout-compressed">
    <tr>
        <td class="font-size12pt right"><strong>{ts}Contributor{/ts}</strong></td><td class="font-size12pt"><strong>{$displayName}</strong></td>
    </tr>
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
        <td class="label nowrap">{$form.contribution_source.label}</td><td>{$form.contribution_source.html}</td>
    </tr>
    {if $config->smtpServer AND $config->smtpServer NEQ 'YOUR SMTP SERVER'}
    <tr><td class="label">{$form.is_email_receipt.label}</td><td>{$form.is_email_receipt.html}<br />
            <span class="description">{ts}Automatically email a receipt for this contribution?{/ts}</td></tr>
    {/if}
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
        <tr><td colspan="2" class="description">{ts}Enter the name as shown on the credit or debit card, and the billing address for this card.{/ts}</td></tr>
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

<div id="adiitionalInfo">
    {include file="CRM/Contribute/Form/AdditionalInfo.tpl"}
</div>
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
</fieldset>
</div>
