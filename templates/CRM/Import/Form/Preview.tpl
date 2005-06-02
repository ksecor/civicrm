{* Import Wizard - Step 3 (preview import results prior to actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="help">
    <p>
    {ts}The information below previews the results of importing your data in CiviCRM.
    Review the totals to ensure that they represent your expected results.{/ts}         
    </p>
    
    {if $invalidRowCount}
        <p>
        {ts 1=$invalidRowCount 2=$downloadErrorRecords}CiviCRM has detected email and/or phone formatting errors in %1 records.
        If you continue, these records will be skipped. OR, you can download a file with just
        these problem records - %2. Then correct them in the original
        import file, cancel this import and begin again at step 1.{/ts}
        </p>
    {/if}

    {if $duplicateRowCount}
        <p>
        {ts 1=$duplicateRowCount 2=$downloadErrorRecords}CiviCRM has detected %1 records with duplicate email addresses within
        this data file. If you continue, these records will be skipped. OR, you can download a file with just
        these problem records - %2. Then correct them in the original
        import file, cancel this import and begin again at step 1.{/ts}
        </p>
    {/if}
    

    <p>{ts}Click 'Import Now' if you are ready to proceed.{/ts}</p>
 </div>
    
 {* Summary Preview (record counts) *}
 <table id="preview-counts" class="report">
    <tr><td class="label">{ts}Total Rows{/ts}</td>
        <td class="data">{$totalRowCount}</td>
        <td class="explanation">{ts}Total rows (contact records) in uploaded file.{/ts}</td>
    </tr>
    
    <tr><td class="label">{ts}Rows with Errors{/ts}</td>
        <td class="data">{$invalidRowCount}</td>
        <td class="explanation">{ts}Rows with invalid email or phone formatting.
            These rows will be skipped (not imported).{/ts}
            <p>{$downloadErrorRecords}</p>
        </td>
    </tr>
    
    <tr><td class="label">{ts}Duplicate Rows{/ts}</td>
        <td class="data">{$duplicateRowCount}</td>
        <td class="explanation">{ts}Rows with duplicate emails within this file.
            These rows will be skipped (not imported).{/ts}
            <p>{$downloadDuplicateRecords}</p>
        </td>
    </tr>

    <tr><td class="label">{ts}Valid Rows{/ts}</td>
        <td class="data">{$validRowCount}</td>
        <td class="explanation">{ts}Total rows to be imported.{/ts}</td>
    </tr>
 </table>
 <br /> 

 {* Table for mapping preview *}
 {include file="CRM/Import/Form/MapTable.tpl}
 <br />
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 
