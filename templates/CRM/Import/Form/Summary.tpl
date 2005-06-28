{* Import Wizard - Step 4 (summary of import results AFTER actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="help">
    <p>
    {ts}<strong>Import has completed successfully.</strong> The information below summarizes the results.{/ts}
    </p>
    
    {if $invalidRowCount }
        {ts 1=$invalidRowCount 2=$downloadErrorRecords}
        <p class="error">
        CiviCRM has detected invalid data and/or formatting errors in %1 records. These records have not been imported.
        </p>
        <p class="error">
        You can %2. You may then correct them, and import the new file with the corrected data.
        </p>
        {/ts}
    {/if}

    {if $conflictRowCount}
        {ts 1=$conflictRowCount 2=$downloadConflictRecords}
        <p class="error">
        CiviCRM has detected %1 records with conflicting email addresses within this data file or relative to existing contact records. These records have not been imported.
        CiviCRM does not allow multiple contact records to have the same primary email address.
        </p>
        <p class="error">
        You can %2. You may then review these records to determine if they are actually conflicts, and correct the email addresses for those that are not.
        </p>
        {/ts}
    {/if}

    {if $duplicateRowCount}
        {ts 1=$duplicateRowCount 2=$downloadDuplicateRecords}
        <p class="error">
        CiviCRM has detected %1 records which are duplicates of existing CiviCRM contact records.  These records have not been imported.
        </p>
        <p class="error">
        You can %2.  You may then review these records to determine if they are actually duplicates, and correct the email
        address for those that are not.
        </p>
        {/ts}
    {/if}
 </div>
    
 {* Summary of Import Results (record counts) *}
 <table id="summary-counts" class="report">
    <tr><td class="label">{ts}Total Rows{/ts}</td>
        <td class="data">{$totalRowCount}</td>
        <td class="explanation">{ts}Total rows (contact records) in uploaded file.{/ts}</td>
    </tr>

    <tr{if $invalidRowCount} class="error"{/if}><td class="label">{ts}Invalid Rows (skipped){/ts}</td>
        <td class="data">{$invalidRowCount}</td>
        <td class="explanation">{ts}Rows with invalid data in one or more fields (for example, invalid email address formatting). These rows will be skipped (not imported).{/ts}
            <p>{$downloadErrorRecords}</p>
        </td>
    </tr>
    
    <tr{if $conflictRowCount} class="error"{/if}><td class="label">{ts}Conflicting Rows (skipped){/ts}</td>
        <td class="data">{$conflictRowCount}</td>
        <td class="explanation">{ts}Rows with conflicting email addresses (NOT imported).{/ts}
            <p>{$downloadConflictRecords}</p>
        </td>
    </tr>

    <tr{if $duplicateRowCount} class="error"{/if}><td class="label">{ts}Duplicate Rows (skipped){/ts}</td>
        <td class="data">{$duplicateRowCount}</td>
        <td class="explanation">{ts}Rows which are duplicates of existing CiviCRM contact records (NOT imported).{/ts}
            <p>{$downloadDuplicateRecords}</p>
        </td>
    </tr>
    <tr><td class="label">{ts}Records Imported{/ts}</td>
        <td class="data">{$validRowCount}</td>
        <td class="explanation">{ts}Rows imported successfully.{/ts}</td>
    </tr>
 </table>
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 
