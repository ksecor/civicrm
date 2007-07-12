{* Export Wizard - Step 2 *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}

<div id="help">
<p>{ts}<strong>Export PRIMARY contact fields</strong> provides the most commonly used data values. This includes primary address information, preferred phone and email, as well as all custom data.{/ts}</p>
<p>{ts}Click <strong>Select fields for export</strong> and then <strong>Continue</strong> to choose a subset of fields for export. This option allows you to export multiple specific locations (Home, Work, etc.). You can also save your selections as a 'field mapping' so you can use it again later.{/ts}</p>
</div>

<div id="export-type" class="form-item">
 <fieldset>
    <dl>
        <dd>
         {ts count=$totalSelectedContacts plural='%count records selected for export.'}One record selected for export.{/ts}
        </dd> 
        <dd>{$form.exportOption.html}</dd>
    </dl>
 </fieldset>
</div>
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
