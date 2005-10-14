{* Export Wizard - Step 2 (map incoming data fields) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}

 <div id="help">
    <p>
    {ts}Select the fields which you want to export. All the selects with '- do not export -' will be ignored.{/ts}
    </p>
</div>
 {* Table for mapping data to CRM fields *}
 {include file="CRM/Contact/Form/Task/Export/table.tpl}
 <br />

 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 {$initHideBoxes}
