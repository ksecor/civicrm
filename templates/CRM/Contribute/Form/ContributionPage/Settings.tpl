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
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
    <dt>{$form.intro_text.label}</dt><dd>{$form.intro_text.html}</dd>
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
