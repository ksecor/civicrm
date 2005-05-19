{* Import Wizard - Step 3 (preview import results prior to actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

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
 <table id="preview-counts" class="report">
    <tr><td class="label">Total Rows</td>
        <td class="data">{$totalRowCount}</td>
        <td class="explanation">Total rows (contact records) in uploaded file.</td>
    </tr>
    
    <tr><td class="label">Rows with Errors</td>
        <td class="data">{$invalidRowCount}</td>
        <td class="explanation">Rows with invalid email or phone formatting.
            These rows will be skipped (not imported).
            <p>{$downloadErrorRecords}</p>
        </td>
    </tr>
    
    <tr><td class="label">Duplicate Rows</td>
        <td class="data">{$duplicateRowCount}</td>
        <td class="explanation">Rows with duplicate emails within this file.
            These rows will be skipped (not imported).
            <p>{$downloadDuplicateRecords}</p>
        </td>
    </tr>

    <tr><td class="label">Valid Rows</td>
        <td class="data">{$validRowCount}</td>
        <td class="explanation">Total rows to be imported.</td>
    </tr>
 </table>
 <br /> 

 {* Table for mapping preview *}
 {include file="CRM/Import/Form/MapTable.tpl}
 <br />
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 
