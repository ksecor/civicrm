{* Export Wizard - Step 3 (map export data fields) *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl"}

<div id="help">
<p>
{ts}Select the fields which you want included for this export, in the order you want them included. Rows marked  '- do not export -' will be ignored. Click <strong>Select more fields...</strong> if you want to export more fields than are presented in the table below.{/ts}
</p>
{if $savedMapping}
<p>{ts}Click 'Load Saved Field Mapping' to retrieve an export setup that you have previously saved.{/ts}</p>
{/if}
<p>If you think you may be using the same export setup in the future, check 'Save this field mapping'
at the bottom of the page before continuing. You will then be able to reuse this setup the next time you
need it with a single click.</p>
</div>

{* Table for mapping data to CRM fields *}
{include file="CRM/Contact/Form/Task/Export/table.tpl"}
<br />

 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 {$initHideBoxes}
