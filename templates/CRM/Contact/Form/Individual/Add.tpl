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

 {$form.mdyx.html}

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

 <div id="core">
 <fieldset><legend>Name and Greeting</legend>
     <div class="form-item">
        <label>First/Last:</label>
        {$form.prefix.html}
        {$form.first_name.html|crmInsert:size:15}
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
 
{* Plugging the Communication preferences block *} 
 {include file="CRM/Contact/Form/Contact/Comm_prefs.tpl"}
 
{* Plugging the Location block *}
{* @var locloop Total number of Location loops *}
{* @var phoneloop Total number of phone loops *}
{* @var phoneloop Total number of email loops *}
{* @var phoneloop Total number of instant messenger loops *}
 {include file="CRM/Contact/Form/Location.tpl" locloop = 4 phoneloop = 4 emailloop = 4 imloop = 4} 

 {******************************** END THE CORE DIV SECTION **************************************}

 </div> <!--end 'core' section of contact form -->


 <div id = "expand_demographics" class="comment">
    {$form.exdemo.html}
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
    <div class="box">
        {$form.hidedemo.html}
    </div>
 </fieldset>
 </div>
  

 {******************************** ENDING THE DEMOGRAPHICS SECTION **************************************}

 <div id = "expand_notes" class="comment">
    {$form.exnotes.html}
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
	<div class="box">
        {$form.hidenotes.html}
    <div>
 </fieldset>
 </div> <!-- End of "notes" div -->
 
 <div id = "buttons" class="form-submit"> <!-- This class should get automated into form.buttons output -->
    {$form.buttons.html}
 </div>

{* A critical javascript placeholder which provides the form object and name dynamically, The script is formed in the php file *}
 {$form.my_script.label}
 </form>


{* Calling the on_load_execute function in the javascript included through the Individual.js source file *} 
 {literal}
 <script type="text/javascript">
 on_load_execute(frm.name);
 </script>
 {/literal}

{* Calling the on_error_execute function in the javascript included through the Individual.js source file *}  
{* This function is invoked if there are errors when the form is relayed from the server *}
{if count($form.errors) gt 0}
 {literal}
 <script type="text/javascript">
 on_error_execute(frm.name);
 </script>
 {/literal}
 {/if}



 {*{if count($form.errors) gt 0}
 {literal}
 <script type="text/javascript">
 document.forms[frm.name].elements['display_set_fields'].label = "true";
 </script>
 {/literal}
 {/if}
 
 {literal}
 <script type="text/javascript">
 on_load_execute(frm.name);
 if (document.forms[frm.name].elements['display_set_fields'].label == "true") {
 on_error_execute(frm.name);
 }
 </script>
 {/literal}
*}
