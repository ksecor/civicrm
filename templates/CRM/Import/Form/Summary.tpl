{* Import Wizard - Step 4 (summary of import results AFTER actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="help">
    <p>
    <strong>Import has completed successfully.</strong> The information below summarizes the
    results.
    </p>
    
    {if $invalidRowCount }
        <p>
        CiviCRM has detected invalid data and/or formatting errors in {$invalidRowCount} records.
        These records have not been imported. You can <a href = "{crmURL p=`$errorFile`}">download</a> a file with just
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
    
 </div>
    
 {* Summary of Import Results (record counts) *}
 <table id="summary-counts" class="report">
    <tr><td class="label">Total Rows</td>
        <td class="data">{$totalRowCount}</td>
        <td class="explanation">Total rows (contact records) in uploaded file.</td>
    </tr>

    <tr><td class="label">Invalid Rows (skipped)</td>
        <td class="data">{$invalidRowCount}</td>
        <td class="explanation">Rows with invalid data (NOT imported).
            <p>{$downloadErrorRecords}</p>
        </td>
    </tr>
    
    <tr><td class="label">Duplicate Rows (skipped)</td>
        <td class="data">{$duplicateRowCount}</td>
        <td class="explanation">Rows with duplicate email addresses (NOT imported).
            <p>{$downloadDuplicateRecords}</p>
        </td>
    </tr>

    <tr><td class="label">Records Imported</td>
        <td class="data">{$validRowCount}</td>
        <td class="explanation">Rows imported successfully.</td>
    </tr>
 </table>
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 
