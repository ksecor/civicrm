{* This file provides the HTML for the big add contact form *}
{* It provides the templating for Name, Demographics and Contact notes *}
{* The templating for Location and Communication preferences block has been plugged by including the Location.tpl file *}    

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>
 
 <div class="crm-submit-buttons">
    {$form.buttons.html}
 </div>

{if $contact_type eq 'Individual'}
 <div id="name">
 <fieldset><legend>{ts}Name and Greeting{/ts}</legend>
    <div class="form-item">
        {*<span class="labels"><label>{ts}First/Last{/ts}</label></span>
        <span class="fields">
            {$form.prefix.html}
            {$form.first_name.html}
            {$form.last_name.html}
            {$form.suffix.html}
        </span>*}
	
	<table class="form-layout">
    <tr>
		<td>{$form.prefix.label}</td>
		<td>{$form.first_name.label}</td>
		<td>{$form.middle_name.label}</td>
		<td>{$form.last_name.label}</td>
		<td>{$form.suffix.label}</td>
	</tr>
	<tr>
		<td>{$form.prefix.html}</td>
		<td>{$form.first_name.html}</td>
		<td>{$form.middle_name.html}</td>
		<td>{$form.last_name.html}</td>
		<td>{$form.suffix.html}</td>
	</tr>
    </table>
    </div>
    
    <div class="form-item">
        <span class="labels">
        {$form.greeting_type.label}
        </span>
        <span class="fields">
        {$form.greeting_type.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels">
        {$form.job_title.label}
        </span>
        <span class="fields">
        {$form.job_title.html}
        </span>
    </div>
    <!-- Spacer div forces fieldset to contain floated elements -->
    <div class="spacer"></div>
    {$form._qf_Edit_refresh_dedupe.html}
    <div class="spacer"></div>
 </fieldset>
 </div>
{elseif $contact_type eq 'Household'}
<div id="name">
 <fieldset><legend>{ts}Household{/ts}</legend>
    <div class="form-item">
        <span class="labels">{$form.household_name.label}</span>
        <span class="fields">
            {$form.household_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels">{$form.nick_name.label}</span>
        <span class="fields">
            {$form.nick_name.html}
        </span>
    </div>

    <!-- Spacer div forces fieldset to contain floated elements -->
    <div class="spacer"></div>
 </fieldset>
 </div>
{elseif $contact_type eq 'Organization'}
<div id="name">
 <fieldset><legend>{ts}Organization{/ts}</legend>
    <div class="form-item">
        <span class="labels">{$form.organization_name.label}</span>
        <span class="fields">
            {$form.organization_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels">{$form.legal_name.label}</span>
        <span class="fields">
            {$form.legal_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels">{$form.nick_name.label}</span>
        <span class="fields">
            {$form.nick_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels">{$form.sic_code.label}</span>
        <span class="fields">
            {$form.sic_code.html}
        </span>
    </div>
</fieldset>
{/if}

{* Plugging the Communication preferences block *} 
 {include file="CRM/Contact/Form/CommPrefs.tpl"}
 
{* Plugging the Location block *}
 {include file="CRM/Contact/Form/Location.tpl"}

{if $contact_type eq 'Individual'}
 <div id = "demographics[show]" class="data-group label">
    {$demographics.show}{ts}Demographics{/ts}
 </div>

 <div id="demographics">
 <fieldset><legend>{$demographics.hide}{ts}Demographics{/ts}</legend>
    <div class="form-item">
        <span class="labels">
        {$form.gender.label}
        </span>
        <span class="fields">
        {$form.gender.html}
        </span>
    </div>
	<div class="form-item">
        <span class="labels">
        {$form.birth_date.label}
        </span>
        <span class="fields">
		{$form.birth_date.html}
        </span>
    </div>
	<div class="form-item">
        {$form.is_deceased.html}
        {$form.is_deceased.label}
    </div>
  </fieldset>
 </div>
{/if}  

 {******************************** ENDING THE DEMOGRAPHICS SECTION **************************************}

 {* Notes block only included for Add Contact (since it navigates from Edit form...) *}
 {if $action eq 1}
     <div id = "notes[show]" class="data-group">
        {$notes.show}<label>{ts}Notes{/ts}</label>
     </div>

     <div id = "notes">
         <fieldset><legend>{$notes.hide}{ts}Contact Notes{/ts}</legend>
            <div class="form-item">
                {$form.note.html}
            </div>
         </fieldset>
     </div>
{/if}
 <!-- End of "notes" div -->
 
 <div class="crm-submit-buttons">
    {$form.buttons.html}
 </div>

 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>
