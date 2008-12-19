{if $showOnlyDataSourceFormPane}
  {include file=$dataSourceFormTemplateFile}
{else}
  {* Import Wizard - Step 1 (choose data source) *}
  {* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

  {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
  {include file="CRM/common/WizardHeader.tpl"}
  {capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}

  <div id="help">
    <p>
      {ts}The Import Wizard allows you to easily import contact records from other applications into CiviCRM. For example, if your organization has contacts in MS Access&copy; or Excel&copy;, and you want to start using CiviCRM to store these contacts, you can 'import' them here.{/ts} {help id='choose-data-source-intro'}
    </p>
  </div>

  <div id="choose-data-source" class="form-item">
    <fieldset>
      <legend>{ts}Choose Data Source{/ts}</legend>
      <dl>
        <dt>{$form.dataSource.label}</dt><dd>{$form.dataSource.html}</dd>
      </dl>
    </fieldset>
    <div class="spacer"></div>
  </div>

  {* Data source form pane is injected here when the data source is selected. *}
  <div id="data-source-form-block">
    {if $dataSourceFormTemplateFile}
      {include file=$dataSourceFormTemplateFile}
    {/if}
  </div>

  <div id="common-form-controls" style="display: none;">
    <fieldset>
      <legend>{ts}Import Options{/ts}</legend>
      <dl>
        <dt>{$form.contactType.label}</dt><dd>{$form.contactType.html} {help id='contact-type'}</dd>
        <dt>{$form.onDuplicate.label}</dt><dd>{$form.onDuplicate.html} {help id='dupes'}</dd>
        
        {include file="CRM/Core/Date.tpl"}
        <dt>&nbsp;</dt>
        <dd class="description">{ts}Select the format that is used for date fields in your import data.{/ts}</dd>

        {if $form.doGeocodeAddress.html}
          <dt>&nbsp;</dt><dd>{$form.doGeocodeAddress.html} {$form.doGeocodeAddress.label}</dd>
          <dt>&nbsp;</dt>
          <dd class="description">
            {ts 1="http://wiki.civicrm.org/confluence//x/YDY" 2=$docURLTitle}This option is not recommended for large imports. Use the command-line geocoding script instead (<a href='%1' target='_blank' title='%2'>read more...</a>).{/ts}
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

    <div class="spacer"></div>
  </div>

  <div id="crm-submit-buttons">
    {$form.buttons.html}
  </div>

  {literal}
    <script type="text/javascript">
      //build data source form block
      buildDataSourceFormBlock();

      function buildDataSourceFormBlock(dataSource)
      {
        var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q=$urlPathVar}"{literal};

        if (!dataSource) {
          var dataSource = document.getElementById('dataSource').value;
        }

        if (dataSource) {
          dataUrl = dataUrl + '&amp;dataSource=' + dataSource;
        } else {
          dojo.byId('data-source-form-block').innerHTML = '';
          return;
        }

        var result = dojo.xhrGet({
          url:      dataUrl,
          handleAs: "text",
          sync:     true,
          timeout:  5000, //time in milliseconds
          handle:   function(response, ioArgs) {
            if (response instanceof Error) {
              if (response.dojoType == "cancel") {
                // The request was cancelled by some other Javascript code.
                console.debug("Request cancelled.");
              } else if (response.dojoType == "timeout") {
                // The request took over 5 seconds to complete.
                console.debug("Request timed out.");
              } else {
                // Some other error happened.
                console.error(response);
              }
            } else {
              // on success
              dojo.byId('data-source-form-block').innerHTML = response;
              // this executes any javascript in the injected block
              executeInnerHTML('data-source-form-block');
              // display common form controls now
              var commonFormControls = document.getElementById('common-form-controls');
              commonFormControls.style.display = '';
            }
          }
        });
      }
    </script>
  {/literal}
{/if}
