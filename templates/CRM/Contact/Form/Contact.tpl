{* This file provides the plugin for the communication preferences in all the three types of contact *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

<fieldset><legend>Communication Preferences</legend>
<table cellpadding="2" cellspacing="2">		

	<tr>	
		<td>
		<table border="0" cellpadding="2" cellspacing="2" width="100%">
			<tr>
				<td class="form-item"><label>{$form.do_not_phone.label}</label></td>
				<td>{$form.do_not_phone.html} 
	                               		      {$form.do_not_email.html} 
                                       		      {$form.do_not_mail.html}</td>
			</tr>
			<tr>
				<td class="form-item"><label>{$form.preferred_communication_method.label}</label></td>
				<td class="form-item">{$form.preferred_communication_method.html}
				<div class="description">Preferred method of communicating with this individual</div></td>
			</tr>
			</table>
			
		</td>

	</tr> 
</table>
</fieldset>
