{* This file provides the HTML for the big add contact form *}
{* It provides the templating for Name, Demographics and Contact notes *}
{* The templating for Location and Communication preferences block has been plugged by including the Location.tpl file *}    

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* Including the javascript source code from the Individual.js and Common.js files *}
 <script type="text/javascript" src="{$config->httpBase}js/Individual.js"></script>
 <script type="text/javascript" src="{$config->httpBase}js/Common.js"></script>

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

{if $contact_type eq 'Individual'}
 <div id="name">
 <fieldset><legend>Name and Greeting</legend>
    <!-- <div class="spacer"></div> -->
    <div class="form-item">
        <span class="labels"><label>First/Last:</label></span>
        <span class="fields">
            {$form.prefix.html}
            {$form.first_name.html}
            {$form.last_name.html}
            {$form.suffix.html}
        </span>
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
 </fieldset>
 </div>
{elseif $contact_type eq 'Household'}
<div id="name">
 <fieldset><legend>Household</legend>
    <!-- <div class="spacer"></div> -->

    <div class="form-item">
        <span class="labels"><label>{$form.household_name.label}</label></span>
        <span class="fields">
            {$form.household_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels"><label>{$form.nick_name.label}</label></span>
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
 <fieldset><legend>Organization</legend>
    <!-- <div class="spacer"></div> -->
    <div class="form-item">
        <span class="labels"><label>{$form.organization_name.label}</label></span>
        <span class="fields">
            {$form.organization_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels"><label>{$form.legal_name.label}</label></span>
        <span class="fields">
            {$form.legal_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels"><label>{$form.nick_name.label}</label></span>
        <span class="fields">
            {$form.nick_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels"><label>{$form.sic_code.label}</label></span>
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
 <div id = "demographics[show]" class="show-section">
    {$demographics.show}
 </div>

 <div id="demographics">
 <fieldset><legend>Demographics</legend>
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
    <div id="demographics[hide]" class="hide-section">
        {$demographics.hide}
    </div>
 </fieldset>
 </div>
{/if}  

 {******************************** ENDING THE DEMOGRAPHICS SECTION **************************************}



 <div id = "notes[show]" class="show-section">
    {$notes.show}
 </div>

 <div id = "notes">
 <fieldset><legend>Contact Notes</legend>
    <div class="form-item">
        {if $mode eq 1}
        {$form.note.html}
        {else}
	   <table border=0>
	   {foreach from=$note item=note key=noteKey }
	     <tr><td>{$note.note|truncate:150:"...":true}</td><td width="100">{$note.modified_date|date_format:"%B %e, %Y"}</td>
	     {if $noteKey neq 0}
	       <td width="90"><a href="#">View</a> | <a href="#">Edit</a></td> 
	     {/if}
	     </tr>  
	   {/foreach}
	   </table>


	     <br><a href="#">New Note</a> 
	      {if $noteKey neq 0 and $total_note gt 2 }
	     | <a href="#">Browse all notes</a>
	      {/if}
        {/if}  
        <div class = "description">
          Record any descriptive comments about this contact.
          You may add an unlimited number of notes, and view or search on them at any time.
        </div>
    </div>
    <!-- Spacer div contains floated elements -->
    <div class="spacer"></div>
	<div id="notes[hide]" class="hide-section">
        {$notes.hide}
    </div>
 </fieldset>
 </div>


 <!-- End of "notes" div -->
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>

 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>
