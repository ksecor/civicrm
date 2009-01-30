{if $showOnlyDataSourceFormPane}
  {include file=$dataSourceFormTemplateFile}
{else}
  {* Import Wizard - Step 1 (choose data source) *}
  {* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

  {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
  {include file="CRM/common/WizardHeader.tpl"}

  <div id="help">
      {ts}The Import Wizard allows you to easily import contact records from other applications into CiviCRM. For example, if your organization has contacts in MS Access&copy; or Excel&copy;, and you want to start using CiviCRM to store these contacts, you can 'import' them here.{/ts} {help id='choose-data-source-intro'}
  </div>

  <div id="choose-data-source" class="form-item">
    <fieldset>
      <legend>{ts}Choose Data Source{/ts}</legend>
      <table class="form-layout">
        <tr>
            <td class="label">{$form.dataSource.label}</td>
            <td>{$form.dataSource.html} {help id='data-source-selection'}</td>
        </tr>
      </table>
    </fieldset>
  </div>

  {* Data source form pane is injected here when the data source is selected. *}
  <div id="data-source-form-block">
    {if $dataSourceFormTemplateFile}
      {include file=$dataSourceFormTemplateFile}
    {/if}
  </div>

  <div id="common-form-controls" class="form-item">
    <fieldset>
      <legend>{ts}Import Options{/ts}</legend>
      <dl>
        <dt>{$form.contactType.label}</dt><dd>{$form.contactType.html} {help id='contact-type'}</dd>
        <dt>{$form.onDuplicate.label}</dt><dd>{$form.onDuplicate.html} {help id='dupes'}</dd>
        
        {include file="CRM/Core/Date.tpl"}
        <dt>&nbsp;</dt>
        <dd class="description">{ts}Select the format that is used for date fields in your import data.{/ts}</dd>

        {if $geoCode}
          <dt>&nbsp;</dt><dd>{$form.doGeocodeAddress.html} {$form.doGeocodeAddress.label}</dd>
          <dt>&nbsp;</dt>
          <dd class="description">
            {ts}This option is not recommended for large imports. Use the command-line geocoding script instead.{/ts} {docURL page="Batch Geocoding Script"}
          </dd>
        {/if}

        {if $savedMapping}
          <dt>{if $loadedMapping}{ts}Select a Different Field Mapping{/ts}{else}{ts}Load Saved Field Mapping{/ts}{/if}</dt>
          <dd><span>{$form.savedMapping.html}</span></dd>
          <dt>&nbsp;</dt>
          <dd class="description">{ts}Select Saved Mapping or Leave blank to create a new One.{/ts}</dd>
        {/if}
      </dl>
    </fieldset>
  </div>

  <div id="crm-submit-buttons">
    {$form.buttons.html}
  </div>

  {literal}
    <script type="text/javascript">
      cj(document).ready(function() {    
         //build data source form block
         buildDataSourceFormBlock();
      });
      
      function buildDataSourceFormBlock(dataSource)
      {
        var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q=$urlPathVar}"{literal};

        if (!dataSource ) {
          var dataSource = document.getElementById('dataSource').value;
        }

        if ( dataSource ) {
          dataUrl = dataUrl + '&dataSource=' + dataSource;
        } else {
          cj("#data-source-form-block").html( '' );
          return;
        }

        cj("#data-source-form-block").load( dataUrl );

      }
      
    </script>
  {/literal}
{/if}
