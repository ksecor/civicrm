{* This file provides the plugin for the communication preferences in all the three types of contact *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

<div id="commPrefs">
<fieldset><legend>Communication Preferences</legend>
<div class="form-item">
    <span class="labels">
    <label>Privacy:</label>
    </span>
    <span class="fields">
    {$form.privacy.html}
    </span>
</div>

<div class="form-item">
    <span class="labels">
    <label>
    {$form.preferred_communication_method.label}
    </span>
    <span class="fields">
    {$form.preferred_communication_method.html}
    </label>
    </span>
    <div class="description">Preferred method of communicating with this individual</div>
</div>
<!-- Spacer div forces fieldset to contain floated elements -->
<div class="spacer"></div>
</fieldset>
</div>