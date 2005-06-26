{* Import Wizard - Step 2 (map incoming data fields) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}

 <div id="help">
    <p>
    {ts}Review the values shown below from the first 2 rows of your import file and select
    the matching CiviCRM database fields from the drop-down lists in the right-hand column.
    Select '-do not import' for any columns in the import file that you want ignored.   
    {/ts}
    </p>
</div>
 {* Table for mapping data to CRM fields *}
 {include file="CRM/Import/Form/MapTable.tpl}
 <br />

 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
