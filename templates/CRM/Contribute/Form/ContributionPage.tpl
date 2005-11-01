<div class="form-item">
    <fieldset><legend>{ts}Contribution Page{/ts}</legend>
    <div id="help">
        <p>
        {ts}Use this form to setup the name, description and more for a customized contribution page.{/ts}
        </p>
    </div>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
    <dt>{$form.intro_text.label}</dt><dd>{$form.intro_text.html}</dd>
    <dt>{$form.thankyou_text.label}</dt><dd>{$form.thankyou_text.html}</dd>
    <dt>{$form.receipt_text.label}</dt><dd>{$form.receipt_text.html}</dd>
    <dt>{$form.cc_receipt.label}</dt><dd>{$form.cc_receipt.html}</dd> 
    <dt>{$form.bcc_receipt.label}</dt><dd>{$form.bcc_receipt.html}</dd> 
    <dt></dt><dd>{$form.is_email_receipt.html} {$form.is_allow_other_amount.label}</dd>

    <dt>{$form.custom_pre_id.label}</dt><dd>{$form.custom_pre_id.html}</dd>
    <dt>{$form.custom_post_id.label}</dt><dd>{$form.custom_post_id.html}</dd>
    <dt>{$form.amount_id.label}</dt><dd>{$form.amount_id.html}</dd>

    <dt></dt><dd>{$form.is_allow_other_amount.html} {$form.is_allow_other_amount.label}</dd>
    <dt>{$form.min_amount.label}</dt><dd>{$form.min_amount.html}</dd> 
    <dt>{$form.max_amount.label}</dt><dd>{$form.max_amount.html}</dd> 
    <dt></dt><dd>{$form.is_credit_card_only.html} {$form.is_credit_card_only.label}</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    {if $action ne 4}
        <div id="crm-submit-buttons">
        <dt></dt><dd>{$form.buttons.html}</dd>
        </div>
    {else}
        <div id="crm-done-button">
        <dt></dt><dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
{if $action eq 2 or $action eq 4} {* Update or View*}
    <p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/contribute' q="action=browse&reset=1"}">&raquo;  {ts}Contribution Pages{/ts}</a>
    </div>
    </p>
{/if}
