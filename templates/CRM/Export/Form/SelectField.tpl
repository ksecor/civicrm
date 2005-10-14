{* Export Wizard - Step 1 *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="upload-file" class="form-item">
 <fieldset><legend>{ts}Export Contacts{/ts}</legend>
    <dl>
       <dd class="description">
         {ts count=$totalSelectedContacts plural='%count records selected for export.'}One record selected for export.{/ts}
       </dd> 
       <dd>{$form.exportOption.html}</dd>
    </dl>
 </fieldset>
 </div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
