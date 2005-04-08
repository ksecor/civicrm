{* Import Wizard - Step 1 (upload data file) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
<form {$form.attributes}>

{* formCommon.tpl handles error msg display, and inserts any required hidden fields. *}
{include file="CRM/formCommon.tpl"}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="upload-file">
 <fieldset><legend>Upload Data File</legend>
    <div class="form-item">
        <span class="labels">{$form.uploadFile.label}</span>
        <span class="fields">
            {$form.uploadFile.html}
        </span>
    </div>
    <div class="form-item">
        {$form.skipColumnHeader.html}
        {$form.skipColumnHeader.label}
    </div>
 </fieldset>
 </div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</form>
