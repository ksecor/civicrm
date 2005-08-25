{* Import Wizard - Step 4 (summary of import results AFTER actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="help">
    <p>
    {ts}<strong>Import has completed successfully.</strong> The information below summarizes the results.{/ts}
    </p>
    
    {if $invalidRowCount }
        <p class="error">
        {ts 1=$invalidRowCount}CiviCRM has detected invalid data and/or formatting errors in %1 records. These records have not been imported.{/ts}
        </p>
        <p class="error">
        {ts 1=$downloadErrorRecordsUrl}You can <a href="%1">Download Errors</a>. You may then correct them, and import the new file with the corrected data.{/ts}
        </p>
    {/if}

    {if $conflictRowCount}
        <p class="error">
        {ts 1=$conflictRowCount}CiviCRM has detected %1 records with conflicting email addresses within this data file or relative to existing contact records. These records have not been imported. CiviCRM does not allow multiple contact records to have the same primary email address.{/ts}
        </p>
        <p class="error">
        {ts 1=$downloadConflictRecordsUrl}You can <a href="%1">Download Conflicts</a>. You may then review these records to determine if they are actually conflicts, and correct the email addresses for those that are not.{/ts}
        </p>
    {/if}

    {if $duplicateRowCount}
        <p {if $dupeError}class="error"{/if}>
        {ts 1=$duplicateRowCount}CiviCRM has detected %1 records which are duplicates of existing CiviCRM contact records.  {/ts}{$dupeActionString}
        </p>
        <p {if $dupeError}class="error"{/if}>
        {ts 1=$downloadDuplicateRecordsUrl}You can <a href="%1">Download Duplicates</a>. You may then review these records to determine if they are actually duplicates, and correct the email address for those that are not.{/ts}
        </p>
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
            {if $invalidRowCount}
                <p><a href="{$downloadErrorRecordsUrl}">{ts}Download Errors{/ts}</a></p>
            {/if}
        </td>
    </tr>
    
    <tr{if $conflictRowCount} class="error"{/if}><td class="label">{ts}Conflicting Rows (skipped){/ts}</td>
        <td class="data">{$conflictRowCount}</td>
        <td class="explanation">{ts}Rows with conflicting email addresses (NOT imported).{/ts}
            {if $conflictRowCount}
                <p><a href="{$downloadConflictRecordsUrl}">{ts}Download Conflicts{/ts}</a></p>
            {/if}
        </td>
    </tr>

    <tr{if $duplicateRowCount && $dupeError} class="error"{/if}><td class="label">{ts}Duplicate Rows{/ts}</td>
        <td class="data">{$duplicateRowCount}</td>
        <td class="explanation">{ts}Rows which are duplicates of existing CiviCRM contact records.  {/ts}{$dupeActionString}
            {if $duplicateRowCount}
                <p><a href="{$downloadDuplicateRecordsUrl}">{ts}Download Duplicates{/ts}</a></p>
            {/if}
        </td>
    </tr>
    <tr><td class="label">{ts}Records Imported{/ts}</td>
        <td class="data">{$validRowCount}</td>
        <td class="explanation">{ts}Rows imported successfully.{/ts}</td>
    </tr>

    {if $groupAdditions}
    <tr><td class="label">{ts}Import to Groups{/ts}</td>
        <td colspan="2" class="explanation">
            {foreach from="$groupAdditions" item="group"}
                <label>{$group.name}</label>:
                {if $group.new}
                    {ts count=$group.added plural='%count contacts added to this new group.'}%count contact added to this new group.{/ts}
                {else}
                    {ts count=$group.added plural='%count contacts added to this existing group.'}%count contact added to this existing group.{/ts}
                {/if}
                {if $group.notAdded}{ts count=$group.notAdded plural='%count contacts NOT added (already in this group).'}%count contact NOT added (already in this group).{/ts}{/if}<br>
            {/foreach}
        </td>
    </tr>
    {/if}

 </table>
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 
