{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/WizardHeader.tpl}
 
<div class="form-item">
    <fieldset><legend>{ts}Contribution Page{/ts}</legend>
    <div id="help">
        <p>
        {ts}Use this form to setup the name, description and more for a customized contribution page.{/ts}
        </p>
    </div>
    <dl>
    <dt>{$form.thankyou_text.label}</dt><dd>{$form.thankyou_text.html}</dd>
    <dt></dt><dd>{$form.is_email_receipt.html} {$form.is_email_receipt.label}</dd>
    <dt>{$form.receipt_text.label}</dt><dd>{$form.receipt_text.html}</dd>
    <dt>{$form.cc_receipt.label}</dt><dd>{$form.cc_receipt.html}</dd> 
    <dt>{$form.bcc_receipt.label}</dt><dd>{$form.bcc_receipt.html}</dd> 

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
