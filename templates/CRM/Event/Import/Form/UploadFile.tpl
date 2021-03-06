{* Event Import Wizard - Step 1 (upload data file) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/common/WizardHeader.tpl"}
  
 <div id="help">
    <p>
    {ts}The Event Import Wizard allows you to easily upload event participation data such as event registrations from other applications into CiviCRM.{/ts}
    </p>
    <p>
    {ts}Files to be imported must be in the 'comma-separated-values' format (CSV) and must contain data needed to match the participant data to an existing contact in your CiviCRM database.{/ts} {help id='upload'}
    </p>
 </div>    

 <div id="upload-file" class="form-item">
 <fieldset>
    <dl>
        <dt>{$form.uploadFile.label}</dt><dd>{$form.uploadFile.html}</dd>
        <dt>&nbsp;</dt>
        <dd class="description">{ts}File format must be comma-separated-values (CSV).{/ts}</dd>
        <dt>&nbsp;</dt>
	    <dd>{ts 1=$uploadSize}Maximum Upload File Size: %1 MB{/ts}</dd>
        <dt>&nbsp;</dt><dd>{$form.skipColumnHeader.html} {$form.skipColumnHeader.label}</dd>
        <dt>&nbsp;</dt>		
        <dd class="description">
            {ts}Check this box if the first row of your file consists of field names (Example: "Contact ID", "Participant Role").{/ts}
        </dd>
        <dt class="">{$form.contactType.label}</dt><dd>{$form.contactType.html}<br />
            <span class="description">
            {ts}Select 'Individual' if you are importing event participation data for individual persons.{/ts}
            {ts}Select 'Organization' or 'Household' if you are importing event participation data for contacts of that type.{/ts}
            </span>
        </dd>
        <dt>{$form.onDuplicate.label}</dt><dd>{$form.onDuplicate.html} {help id="id-onDuplicate"}</dd> 
        {include file="CRM/Core/Date.tpl"}  
{if $savedMapping}
      <dt>{if $loadedMapping}{ts}Select a Different Field Mapping{/ts}{else}{ts}Load Saved Field Mapping{/ts}{/if}</dt>
      <dd><span>{$form.savedMapping.html}</span> </dd>
      <dt>&nbsp;</dt>
      <dd class="description">{ts}Select Saved Mapping, or leave blank to create a new mapping.{/ts}</dd>
{/if}
    </dl>
    <div class="spacer"></div>
 </fieldset>
 </div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
