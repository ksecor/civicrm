{* This file provides the plugin for the communication preferences in all the three types of contact *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

<fieldset><legend>Communication Preferences</legend>
<div class="form-item">
    <label>Privacy:</label>
    {$form.privacy.html}
</div>

<div class="form-item">
    <label>
    {$form.preferred_communication_method.label}
    {$form.preferred_communication_method.html}
    </label>
    <div class="description">Preferred method of communicating with this individual</div>
</div>
</fieldset>
