{* Import Wizard - Step 1 (upload data file) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/common/WizardHeader.tpl"}
 
 <div id="help">
    <p>
    {ts}The Import Wizard allows you to easily upload contact records from other applications into CiviCRM. For example, if your organization has contacts in MS Access&copy; or Excel&copy;, and you want to start using CiviCRM to store these contacts, you can 'import' them here.{/ts}
    </p>
    <p>
    {ts}Files to be imported must be in the 'comma-separated-values' format (CSV). Most applications will allow you to export records in this format. Consult the documentation for your application if you're not sure how to do this. Save this file to your local hard drive (or an accessible drive on your network) - and you are now ready for step 1 (Upload Data).{/ts}
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
            {ts}Check this box if the first row of your file consists of field names (Example: "First Name","Last Name","Email"){/ts}
        </dd> 
        <dt>{$form.contactType.label}</dt><dd>{$form.contactType.html}</dd>
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}Select 'Individual' if each record in your file represents and individual person - even if the file also contains related Organization data (e.g. Employer Name, Employer Address, etc.).{/ts}
        </dd>
        <dt>&nbsp;</dt>
        <dd class="description">{ts}Select 'Organization' or 'Household' if each record in your file represents a contact of that type.{/ts}
        </dd> 
        <dt>{$form.onDuplicate.label}</dt><dd>{$form.onDuplicate.html}</dd>
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}If a contact in the import file appears to be a duplicate of an existing CiviCRM contact...{/ts}
        </dd>
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}<label>Skip:</label> Reports and then Skips duplicate import file rows - leaving the matching record in the database as-is (default).{/ts}
        </dd>
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}<label>Update:</label> Updates database fields with available import data. Fields in the database which are NOT included in the import row are left as-is.{/ts}
        </dd>
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}<label>Fill:</label> Fills in additional contact data only. Database fields which currently have values are left as-is.{/ts}
        </dd>
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}<label>No Duplicate Checking:</label> Insert all valid records without comparing them to existing contact records for possible duplicates.{/ts}
        </dd>
        {include file="CRM/Core/Date.tpl"}
        <dt>&nbsp;</dt>
        <dd class="description">
            {ts}Select the format that is used for date fields in your import data.{/ts}
        </dd>
    </dl>
 </fieldset>
 <div class="spacer"></div>
 </div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
