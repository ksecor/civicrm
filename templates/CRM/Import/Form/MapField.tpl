{* Import Wizard - Step 2 (map incoming data fields) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
<form {$form.attributes}>

{* formCommon.tpl handles error msg display, and inserts any required hidden fields. *}
{include file="CRM/formCommon.tpl"}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 {* Table for mapping data to CRM fields *}
 {include file="CRM/Import/Form/MapTable.tpl}
 <br />

 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</form>
