{* Import Wizard - Step 3 (preview import results prior to actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/common/WizardHeader.tpl"}
 
 <div id="help">
    <p>
    {ts}The information below previews the results of importing your data in CiviCRM. Review the totals to ensure that they represent your expected results.{/ts}         
    </p>
    
    {if $invalidRowCount}
        <p class="error">
        {ts 1=$invalidRowCount 2=$downloadErrorRecordsUrl}CiviCRM has detected invalid data or formatting errors in %1 records. If you continue, these records will be skipped. OR, you can download a file with just these problem records - <a href="%2">Download Errors</a>. Then correct them in the original import file, cancel this import and begin again at step 1.{/ts}
        </p>
    {/if}

    {if $conflictRowCount}
        <p class="error">
        {ts 1=$conflictRowCount 2=$downloadConflictRecordsUrl}CiviCRM has detected %1 records with conflicting email addresses within this data file. If you continue, these records will be skipped. OR, you can download a file with just these problem records - <a href="%2">Download Conflicts</a>. Then correct them in the original import file, cancel this import and begin again at step 1.{/ts}
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
    
    {if $invalidRowCount}
    <tr class="error"><td class="label">{ts}Rows with Errors{/ts}</td>
        <td class="data">{$invalidRowCount}</td>
        <td class="explanation">{ts}Rows with invalid data in one or more fields (for example, invalid email address formatting). These rows will be skipped (not imported).{/ts}
            {if $invalidRowCount}
                <p><a href="{$downloadErrorRecordsUrl}">{ts}Download Errors{/ts}</a></p>
            {/if}
        </td>
    </tr>
    {/if}
    
    {if $conflictRowCount}
    <tr class="error"><td class="label">{ts}Conflicting Rows{/ts}</td>
        <td class="data">{$conflictRowCount}</td>
        <td class="explanation">{ts}Rows with conflicting email addresses within this file. These rows will be skipped (not imported).{/ts}
            {if $conflictRowCount}
                <p><a href="{$downloadConflictRecordsUrl}">{ts}Download Conflicts{/ts}</a></p>
            {/if}
        </td>
    </tr>
    {/if}

    <tr><td class="label">{ts}Valid Rows{/ts}</td>
        <td class="data">{$validRowCount}</td>
        <td class="explanation">{ts}Total rows to be imported.{/ts}</td>
    </tr>
 </table>
  

 {* Table for mapping preview *}
 {include file="CRM/Import/Form/MapTable.tpl}
 
 
 {* Group options *}
 {* New Group *}
<div id="newGroup_show" class="data-group">
    <a href="#" onclick="hide('newGroup_show'); show('newGroup'); return false;">{ts}&raquo; <label> Create a new group from imported records</label>{/ts}{*$form.newGroup.label*}</a>
</div>

<div id="newGroup" class="data-group">
    <a href="#" onclick="hide('newGroup'); show('newGroup_show'); return false;">{ts}&raquo; <label> Create a new group from imported records</label>{/ts}</a>
    <div class="form-item">
        <dl>
	    <dt class="description">{$form.newGroupName.label}</dt><dd>{$form.newGroupName.html}</dd>
	    <dt class="description">{$form.newGroupDesc.label}</dt><dd>{$form.newGroupDesc.html}</dd>
        </dl>
    </div>
</div>
  {* Existing Group *}
{if $form.groups}
<div id="existingGroup_show" class="data-group">
    <a href="#" onclick="hide('existingGroup_show'); show('existingGroup'); return false;">&raquo; {$form.groups.label}</a>
</div>
{/if}

<div id="existingGroup" class="data-group">
    <a href="#" onclick="hide('existingGroup'); show('existingGroup_show'); return false;">&raquo; {$form.groups.label}</a>
    <div class="form-item">
        <dl>
        <dt></dt><dd>{$form.groups.html}</dd>
        </dl>
    </div>
</div>

{* Tag options *}
{* New Tag *}
<div id="newTag_show" class="data-group">
    <a href="#" onclick="hide('newTag_show'); show('newTag'); return false;">{ts}&raquo; <label> Create a new tag and assign it to imported records</label>{/ts}</a>
</div> 
<div id="newTag" class="data-group">
    <a href="#" onclick="hide('newTag'); show('newTag_show'); return false;">{ts}&raquo; <label> Create a new tag and assign it to imported records</label>{/ts}</a>
        <div class="form-item">
        <dl>
	    <dt class="description">{$form.newTagName.label}</dt><dd>{$form.newTagName.html}</dd>
	    <dt class="description">{$form.newTagDesc.label}</dt><dd>{$form.newTagDesc.html}</dd>
        </dl>
    </div>
</div>
{* Existing Tag Imported Contact *}

<div id="tag_show" class="data-group">
    <a href="#" onclick="hide('tag_show'); show('tag'); return false;">&raquo; <label>{ts}Tag imported records{/ts}</label></a>
</div>

<div id="tag" class="data-group">
    <a href="#" onclick="hide('tag'); show('tag_show'); return false;">&raquo; <label>{ts}Tag imported records{/ts}</label></a>
    <dl>
        <dt></dt><dd class="listing-box" style="margin-bottom: 0em; width: 15em;">
       {foreach from=$form.tag item="tag_val"} 
        <div>{$tag_val.html}</div>
        {/foreach}
        </dd>
    </dl>
</div>

<div id="crm-submit-buttons">
   {$form.buttons.html}
</div>

<script type="text/javascript">
hide('newGroup');
hide('existingGroup');
hide('newTag');
hide('tag');
</script>
