{* Import Wizard - Step 1 (upload data file) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
<form {$form.attributes}>

{* formCommon.tpl handles error msg display, and inserts any required hidden fields. *}
{include file="CRM/formCommon.tpl"}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="upload-file" class="form-item">
 <fieldset><legend>Upload Data File</legend>
    <dl>
       <dt>{$form.uploadFile.label}</dt><dd>{$form.uploadFile.html}</dd>
       <dt></dt><dd>{$form.skipColumnHeader.html} {$form.skipColumnHeader.label}</dd>
    </dl>
 </fieldset>
 </div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</form>
