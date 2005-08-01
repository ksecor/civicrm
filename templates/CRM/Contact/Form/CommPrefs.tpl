{* This file provides the plugin for the communication preferences in all the three types of contact *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

<div id="commPrefs">
<fieldset><legend>{ts}Communication Preferences{/ts}</legend>
	<table class="form-layout">
    <tr>
		<td>{$form.privacy.label}</td>
        <td>{$form.privacy.html}</td>
    </tr>
    <tr>
        <td>{$form.preferred_communication_method.label}</td>
        <td>
            {$form.preferred_communication_method.html}
            <div class="description font-italic">
                {ts}Select the preferred method of communicating with this contact.{/ts}
            </div>
        </td>
    </tr>
    </table>
</fieldset>
</div>
