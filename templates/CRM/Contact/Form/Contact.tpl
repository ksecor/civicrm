{* This is a template file for displaying communication preference block *}

<fieldset><legend>Communication Preferences</legend>
<table cellpadding="2" cellspacing="2">		

	<tr>	
		<td>
		<table border="0" cellpadding="2" cellspacing="2" width="100%">
			<tr>
				<td class="form-item"><label>{$form.privacy.label}</label></td>
				<td>{$form.privacy.html} </td>
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
