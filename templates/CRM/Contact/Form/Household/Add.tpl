{* This file provides the HTML for the big add contact form *}
{* It provides the templating for Organization Name block and Contact notes *}
{* The templating for Location and Communication preferences block has been plugged by including the Location.tpl file *}    

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* Including the javascript source code from the Individual.js and Common.js files *}
 <script type="text/javascript" src="{$config->httpBase}js/ORG.js"></script>
 <script type="text/javascript" src="{$config->httpBase}js/Common.js"></script>

{* Including the custom javascript validations added by the HTML_QuickForm for all client validations in addRules *} 
 {$form.javascript}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
 <form {$form.attributes}>

	{* $form.hidden serves as a place holder for all the hidden elements defined in the quick form*}

	 {if $form.hidden}
	 {$form.hidden}{/if}

	{* This block checks if there are errors in the form and generates the HTML for display*}
	{* $form.errors is set normally when the form is relayed from the server after processing *}

	 {if count($form.errors) gt 0}
     <div class="messages error">
        Please correct the following errors in the form fields below:
        <ul id="errorList">
        {foreach from=$form.errors key=name item=errorMsg}
            <li>{$name} {$errorMsg}</li>
        {/foreach}
        </ul>
    </div>
    {/if}

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

    <div class="form-item">
        <span class="labels"><label>{$form.primary_contact_id.label}</label></span>
        <span class="fields">
	    {$form.primary_contact_id.html}	
        </span>
    </div>

    <!-- Spacer div forces fieldset to contain floated elements -->
    <div class="spacer"></div>
 </fieldset>
 </div>
 
{* Plugging the Communication preferences block *} 
 {include file="CRM/Contact/Form/Contact/Comm_prefs.tpl"}
 
{* Plugging the Location block *}
 {include file="CRM/Contact/Form/Location.tpl"}

 <div id = "notes[show]" class="show-section">
    {$notes.show}
 </div>

 <div id = "notes">
 <fieldset><legend>Contact Notes</legend>
    <div class="form-item">
        {$form.address_note.html}
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
 </div> <!-- End of "notes" div -->
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>

 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>
