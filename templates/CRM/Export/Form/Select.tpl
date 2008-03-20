{* Export Wizard - Step 2 *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}

<div id="help">
<p>{ts}<strong>Export PRIMARY contact fields</strong> provides the most commonly used data values. This includes primary address information, preferred phone and email.{/ts}</p>
<p>{ts}Click <strong>Select fields for export</strong> and then <strong>Continue</strong> to choose a subset of fields for export. This option allows you to export multiple specific locations (Home, Work, etc.) as well as custom data. You can also save your selections as a 'field mapping' so you can use it again later.{/ts}</p>
</div>

<fieldset>
  <div id="export-type" class="form-item">
    <dl>
        <dd>
         {ts count=$totalSelectedContacts plural='%count records selected for export.'}One record selected for export.{/ts}
        </dd> 
        <dd>{$form.exportOption.html}</dd>
    </dl>
  </div>
  <div id="map">
     <dl>
        <dd>{$form.mapping.label} &nbsp; {$form.mapping.html}</dd>
     </dl>
  </div>
</fieldset>

<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>

{literal}
  <script type="text/javascript">
     function showMappingOption( )
     {
	var element = document.getElementsByName("exportOption");

	if ( element[1].checked ) { 
	  show('map');
        } else {
	  hide('map');
	}
     } 
   showMappingOption( );
  </script>
{/literal}
