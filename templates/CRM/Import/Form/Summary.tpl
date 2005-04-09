{* Import Wizard - Step 4 (summary of import results AFTER actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
<form {$form.attributes}>

{* formCommon.tpl handles error msg display, and inserts any required hidden fields. *}
{include file="CRM/formCommon.tpl"}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="help">
    <p>
    <strong>Import has completed successfully.</strong> The information below summarizes the
    results.
    </p>
    
    {if $invalidRowCount}
        <p>
        CiviCRM has detected invalid data and/or formatting errors in {$invalidRowCount} records.
        These records have not been imported. You can download a file with just
        these problem records - {$downloadErrorRecords}. You may then correct them and import
        the new file with the corrected data.
        </p>
    {/if}

    {if $duplicateRowCount}
        <p>
        CiviCRM has detected {$duplicateRowCount} records with duplicate email addresses within
        this data file or relative to existing contact records. These records have not been
        imported. CiviCRM does not allow multiple contact records to have the same email address.
        </p>
        <p>
        You can download a file with just these problem records - {$downloadErrorRecords}.
        You may then review these records to determine if they are actually duplicates, and
        correct the email addresses for those that are not.
        </p>
    {/if}
    

    <p>Click 'Import Now' if you are ready to proceed.</p>
 </div>
    
 {* Summary of Import Results (record counts) *}
 <div id="result-counts">
    <div class="form-item odd-row">
        <span class="labels">Total Rows</span>
        <span class="fields">{$totalRowCount} - Total number of rows in the import file.</span>
    </div>
    <div class="form-item odd-row">
        <span class="labels">Invalid Rows Skipped</span>
        <span class="fields">
            {$invalidRowCount} - Total number of rows with invalid data.
            These rows were not imported.
            <p>{$downloadErrorRecords}</p>
        </span>
    </div>
    <div class="form-item odd-row">
        <span class="labels">Duplicate Rows Skipped</span>
        <span class="fields">
            {$duplicateRowCount} - Total number of rows with duplicate email addresses.
            These rows were not imported.
            <p>{$downloadDuplicateRecords}</p>
        </span>
    </div>
    <div class="form-item even-row">
        <span class="labels">New Contact Records</span>
        <span class="fields">{$validRowCount} - Total number of rows which were imported successfully.</span>
    </div>

 </div>
 
  <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</form>
