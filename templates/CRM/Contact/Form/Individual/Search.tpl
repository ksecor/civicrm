{$form.javascript}
<form {$form.attributes}>
<fieldset> 
<table border="0" width="100%" cellpadding="2" cellspacing="2" layout="fixed">
	<tr>
		<td>
		 {if $form.hidden}
		 {$form.hidden}
		 {/if}
		</td>
	</tr>

	<tr>
		<td class="form-item">
		{$form.domain_id.label}
		</td> 
		<td class="form-item">
		{$form.domain_id.html}
		</td>	 
	</tr>

	<tr>
		<td class="form-item">
		{$form.sort_name.label}
		</td> 
		<td class="form-item">
		{$form.sort_name.html}
		</td>	 
	</tr>
	<tr>
		<td class="form-item">
		{$form.contact_type.label}
		</td>
		<td class="form-item">
		{$form.contact_type.html}
		</td>

	</tr>	
	<tr>
		<td class="form-item">
		<label>{$form.preferred_communication_method.label}</label>
		</td>
		<td class="form-item">
		{$form.preferred_communication_method.html}
		<div class="description">Preferred method of communicating with this individual</div>
		</td>
	</tr>


	 <tr>
		<td class="form-item" colspan = "2">
		{$form.buttons.html}
		</td> 
	</tr>
</table>
</fieldset>	 
</form>
