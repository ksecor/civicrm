{* Import Wizard - Step 1 (upload data file) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="help">
    <p>
    {ts}The Import Wizard allows you to easily upload contact records from other applications into
    CiviCRM. For example, if your organization has contacts in MS Access&copy; or Excel&copy;,
    and you want to start using CiviCRM to store these contacts, you can 'import' them here.{/ts}
    </p>
    <p>
    {ts}Files to be imported must be in the 'comma-separated-values' format (CSV). Most applications
    will allow you to export records in this format. Consult the documentation for your
    application if you're not sure how to do this. Save this file to your local hard drive (or
    an accessible drive on your network) - and you are now ready for step 1 (Upload Data).{/ts}
    </p>
 </div>    

 <div id="upload-file" class="form-item">
 <fieldset><legend>{ts}Upload Data File{/ts}</legend>
    <dl>
       <dt>{$form.uploadFile.label}</dt><dd>{$form.uploadFile.html}</dd>
       <dt> </dt>
          <dd class="description">{ts}File format must be comma-separated-values (CSV).{/ts}</dd>
       <dt>{$form.skipColumnHeader.html}</dt><dd>{$form.skipColumnHeader.label}</dd>
       <dt> </dt>
          <dd class="description">
            {ts}Check this box if the first row of your file consists of field names (Example: "First Name","Last Name","Email"){/ts}
          </dd>  
    </dl>
 </fieldset>
 </div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
