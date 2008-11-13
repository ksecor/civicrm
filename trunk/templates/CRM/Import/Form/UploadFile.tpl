{* Import Wizard - Step 1 (upload data file) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/common/WizardHeader.tpl"}
 {capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
 
 <div id="help">
    <p>
    {ts}The Import Wizard allows you to easily upload contact records from other applications into CiviCRM. For example, if your organization has contacts in MS Access&copy; or Excel&copy;, and you want to start using CiviCRM to store these contacts, you can 'import' them here.{/ts} {help id='upload-intro'}
    </p>
 </div>    

 <div id="upload-file" class="form-item">
 <fieldset><legend>{ts}Upload Data File{/ts}</legend>
    <dl>
        <dt>{$form.uploadFile.label}</dt><dd>{$form.uploadFile.html}</dd>
        <dt>&nbsp;</dt>
        <dd class="description">{ts}File format must be comma-separated-values (CSV). File must be UTF8 encoded if it contains special characters (e.g. accented letters, etc.).{/ts}</dd>
        <dt>&nbsp;</dt>
	    <dd>{ts 1=$uploadSize}Maximum Upload File Size: %1 MB{/ts}</dd>
        <dt> </dt><dd>{$form.skipColumnHeader.html} {$form.skipColumnHeader.label}</dd>
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}Check this box if the first row of your file consists of field names (Example: 'First Name','Last Name','Email'){/ts}
        </dd> 
        <dt>{$form.contactType.label}</dt><dd>{$form.contactType.html} {help id='contact-type'}</dd>
        <dt>{$form.onDuplicate.label}</dt><dd>{$form.onDuplicate.html} {help id='dupes'}</dd>
{if $form.doGeocodeAddress.html}
        <dt> </dt><dd>{$form.doGeocodeAddress.html} {$form.doGeocodeAddress.label}</dd>
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts 1="http://wiki.civicrm.org/confluence//x/YDY" 2=$docURLTitle}This option is not recommended for large imports. Use the command-line geocoding script instead (<a href='%1' target='_blank' title='%2'>read more...</a>).{/ts}
        </dd> 
{/if}
        {include file="CRM/Core/Date.tpl"}
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}Select the format that is used for date fields in your import data.{/ts}
        </dd>
{if $savedMapping}
      <dt>{if $loadedMapping}{ts}Select a Different Field Mapping{/ts}{else}{ts}Load Saved Field Mapping{/ts}{/if}</dt>
       <dd> <span>{$form.savedMapping.html}</span> </dd>
      <dt>&nbsp;</dt>
       <dd class="description">{ts}Select Saved Mapping or Leave blank to create a new One.{/ts}</dd>
{/if}
    </dl>
 </fieldset>
 <div class="spacer"></div>
 </div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
