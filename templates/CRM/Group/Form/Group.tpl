{* Group Wizard - Step 1 (add group form)*}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
<form {$form.attributes}>

{* formCommon.tpl handles error msg display, and inserts any required hidden fields. *}
{include file="CRM/formCommon.tpl"}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="upload-file">
 <fieldset><legend>New Group</legend>
    <div class="form-item">
        {$form.title.label}
	{$form.title.html}
    </div>
    <div class="form-item">
        {$form.description.label}
        {$form.description.html}
    </div>
    <div class="form-item">
        {$form.group_type.label}
        {$form.group_type.html}
    </div>
 </fieldset>
 </div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</form>
