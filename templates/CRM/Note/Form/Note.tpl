{* Including the custom javascript validations added by the HTML_QuickForm for all client validations in addRules *} 
{$form.javascript}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
 <form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

 <div id = "notes">
 <fieldset><legend>Contact Notes</legend>
    <div class="form-item">
        {$form.note.html}
        <div class = "description">
          Record any descriptive comments about this contact.
          You may add an unlimited number of notes, and view or search on them at any time.
        </div>
    </div>
 </fieldset>
 </div>


 <!-- End of "notes" div -->
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
