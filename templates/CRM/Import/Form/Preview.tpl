{* Import Wizard - Step 3 (preview import results prior to actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
<form {$form.attributes}>

{* formCommon.tpl handles error msg display, and inserts any required hidden fields. *}
{include file="CRM/formCommon.tpl"}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="help">
    <p>
    The information below previews the results of importing your data in CiviCRM.
    Review the totals to ensure that they represent your expected results.         
    </p>
    
    {if $invalidRowCount}
        <p>
        CiviCRM has detected email and/or phone formatting errors in {$invalidRowCount} records.
        If you continue, these records will be skipped. OR, you can download a file with just
        these problem records - {$downloadErrorRecords}. Then correct them in the original
        import file, cancel this import and begin again at step 1.
        </p>
    {/if}

    {if $duplicateRowCount}
        <p>
        CiviCRM has detected {$duplicateRowCount} records with duplicate email addresses within
        this data file. If you continue, these records will be skipped. OR, you can download a file with just
        these problem records - {$downloadErrorRecords}. Then correct them in the original
        import file, cancel this import and begin again at step 1.
        </p>
    {/if}
    

    <p>Click 'Import Now' if you are ready to proceed.</p>
 </div>
    
 {* Summary Preview (record counts) *}
 <div id="preview-counts">
    <div class="form-item odd-row">
        <span class="labels">Total Rows</span>
        <span class="fields">{$totalRowCount} - Total number of rows (contact data records) in this import file.</span>
    </div>
    <div class="form-item even-row">
        <span class="labels">Rows with Errors</span>
        <span class="fields">
            {$invalidRowCount} - Total number of rows with invalid email address or phone formatting.
            These rows will be skipped (not imported).
            <p>{$downloadErrorRecords}</p>
        </span>
    </div>
    <div class="form-item odd-row">
        <span class="labels">Duplicate Rows</span>
        <span class="fields">
            {$duplicateRowCount} - Total number of rows with duplicate email addresses within this file.
            These rows will be skipped (not imported).
            <p>{$downloadDuplicateRecords}</p>
        </span>
    </div>
    <div class="form-item even-row">
        <span class="labels">Valid Rows</span>
        <span class="fields">{$validRowCount} - Total number of rows without formatting errors.</span>
    </div>

 </div>

 {* Table for mapping preview *}
 {include file="CRM/Import/Form/MapTable.tpl}
 
  <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</form>
