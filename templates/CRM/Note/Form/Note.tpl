{* This file provides the HTML for the big add note form *}

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{debug}

{* Including the custom javascript validations added by the HTML_QuickForm for all client validations in addRules *} 
 {$form.javascript}
{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
 <form {$form.attributes}>

	{* $form.hidden serves as a place holder for all the hidden elements defined in the quick form*}

	 {if $form.hidden}
	   {$form.hidden}
	 {/if}

	{* This block checks if there are errors in the form and generates the HTML for display*}
	{* $form.errors is set normally when the form is relayed from the server after processing *}

	 {if count($form.errors) gt 0}
     <div class="messages error">
        Please correct the following errors in the form fields below:
        <ul id="errorList">
        {foreach from=$form.errors key=name item=error}
	   {if is_array($error)}
              <li>{$error.label} {$error.message}</li>
           {else}
              <li>{$error}</li>
           {/if}
        {/foreach}
        </ul>
    </div>
    {/if}


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
