{* This file provides the HTML for the big add contact form *}
{* It provides the templating for Name, Demographics and Contact notes *}
{* The templating for Location and Communication preferences block has been plugged by including the Location.tpl file *}    

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* Including the javascript source code from the Individual.js and Common.js files *}
 <script type="text/javascript" src="{$config->httpBase}js/Individual.js"></script>
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
	 <table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="#ff9900"><tr><td>
	 <table width="100%" cellpadding="10" cellspacing="0" border="0" bgcolor="#FFFFCC"><tr><td align="center">
	 <span class="error" style="font-size: 13px;">Please correct the errors below.</span>
	 </td></tr></table>
	 </td></tr></table>
	 </p>
	 {/if}

 <div id="name">
 <fieldset><legend>Name and Greeting</legend>
     <div class="form-item">
        <label>First/Last:</label>
        {$form.prefix.html}
        {$form.first_name.html}
        {$form.last_name.html}
        {$form.suffix.html}
     </div>

    <div class="form-item">
        <label>{$form.greeting_type.label}</label>
        {$form.greeting_type.html}
    </div>

    <div class="form-item">
    <label>{$form.job_title.label}</label>
    {$form.job_title.html}
    </div>
 </fieldset>
 </div>
 
{* Plugging the Communication preferences block *} 
 {include file="CRM/Contact/Form/Contact/Comm_prefs.tpl"}
 
{* Plugging the Location block *}
 {include file="CRM/Contact/Form/Location.tpl"}

 {******************************** END THE CORE DIV SECTION **************************************}

 </div> <!--end 'core' section of contact form -->


 <div id = "demographics[show]" class="comment">
    {$form.demographics.show.html}
 </div>

 <div id="demographics">
 <fieldset><legend>Demographics</legend>
    <div class="form-item">
        {$form.gender.label}
	{$form.gender.html}
    </div>
	<div class="form-item">
        {$form.birth_date.label}
		{$form.birth_date.html}
    </div>
	<div class="form-item">
        {$form.is_deceased.html} {$form.is_deceased.label}
    </div>
    <div id="demographics[hide]" class="box">
        {$form.demographics.hide.html}
    </div>
 </fieldset>
 </div>
  

 {******************************** ENDING THE DEMOGRAPHICS SECTION **************************************}

 <div id = "notes[show]" class="comment">
    {$form.notes.show.html}
 </div>

 <div id = "notes">
 <fieldset><legend>Contact Notes</legend>
    <div class="form-item">
        <label>
        {* {$form.address_note.label} *}
        {$form.address_note.html}
        </label>
        <div class = "description">
          Record any descriptive comments about this contact.
          You may add an unlimited number of notes, and view or search on them at any time.
        </div>
    </div>    
	<div id="notes[hide]" class="box">
        {$form.notes.hide.html}
        </div>
 </fieldset>
 </div> <!-- End of "notes" div -->
 
 <div id = "buttons" class="form-submit"> <!-- This class should get automated into form.buttons output -->
    {$form.buttons.html}
 </div>

 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>
