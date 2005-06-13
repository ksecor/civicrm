{* Import Wizard - Step 4 (summary of import results AFTER actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="help">
    <p>
    {ts}<strong>Import has completed successfully.</strong> The information below summarizes the
    results.{/ts}
    </p>
    
    {if $invalidRowCount }
        <p>
        {capture assign=crmURL}{crmURL p=`$errorFile`}{/capture}
        {ts 1=$invalidRowCount 2=$crmURL 3=$downloadErrorRecords}CiviCRM has detected invalid data and/or formatting errors in %1 records. These records have not been imported. You can <a href = "%2">download</a> a file with just these problem records - %3. You may then correct them and import the new file with the corrected data.{/ts}
        </p>
    {/if}

    {if $duplicateRowCount}
        <p>
        {ts 1=$duplicateRowCount}CiviCRM has detected %1 records with duplicate email addresses within this data file or relative to existing contact records. These records have not been imported. CiviCRM does not allow multiple contact records to have the same email address.{/ts}
        </p>
        <p>
        {ts 1=$downloadErrorRecords}You can download a file with just these problem records - %1. You may then review these records to determine if they are actually duplicates, and correct the email addresses for those that are not.{/ts}
        </p>
    {/if}
    
 </div>
    
 {* Summary of Import Results (record counts) *}
 <table id="summary-counts" class="report">
    <tr><td class="label">{ts}Total Rows{/ts}</td>
        <td class="data">{$totalRowCount}</td>
        <td class="explanation">{ts}Total rows (contact records) in uploaded file.{/ts}</td>
    </tr>

    <tr><td class="label">{ts}Invalid Rows (skipped){/ts}</td>
        <td class="data">{$invalidRowCount}</td>
        <td class="explanation">{ts}Rows with invalid data (NOT imported).{/ts}
            <p>{$downloadErrorRecords}</p>
        </td>
    </tr>
    
    <tr><td class="label">{ts}Duplicate Rows (skipped){/ts}</td>
        <td class="data">{$duplicateRowCount}</td>
        <td class="explanation">{ts}Rows with duplicate email addresses (NOT imported).{/ts}
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
 
