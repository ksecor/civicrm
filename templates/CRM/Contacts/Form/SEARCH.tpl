{$form.javascript}
<form {$form.attributes}>
<fieldset> 
<table border="0" width="100%" cellpadding="2" cellspacing="2" layout="fixed">
 <tr><td>
	 {if $form.hidden}
	 {$form.hidden}
	 {/if}
</td></tr>
	 <tr>
	 <td class="form-item">{$form.name.label}</td> 
	 <td class="form-item">{$form.name.html}</td>	 
	 </tr>
	 <!--tr><td class="form-item">{$form.last_name.label}</td></tr-->
	 <!--tr><td class="form-item">{$form.last_name.html} </td> </tr-->
	 <tr>
	 <td class="form-item">{$form.email.label}</td>
	 <td class="form-item">{$form.email.html}</td>
	 </tr>
	 <tr>
	 <td class="form-item">{$form.birth_date.label}</td>
	 <td class="form-item">{$form.birth_date.html}</td>
	 </tr>
	 <tr>
	 <td class="form-item">{$form.phone.label}</td>
	 <td class="form-item">{$form.phone.html}</td>
	 </tr>
	 <tr>
	 <td class="form-item">{$form.street.label}</td>
	 <td class="form-item">{$form.street.html}</td>
	 </tr>
	 <tr>
	 <td class="form-item">{$form.city.label}</td>
	 <td class="form-item">{$form.city.html}</td>
	 </tr>
	 <tr>
	 <td class="form-item">{$form.state_province_id.label}</td>
	 <td class="form-item">{$form.state_province_id.html}</td>
	 </tr>
	 <tr>
	 <td class="form-item">{$form.country_id.label}</td>
	 <td class="form-item">{$form.country_id.html}</td>
	 </tr>

	 <tr><td></td><td class="form-item">{$form.buttons.html}</td> </tr>
</table>
</fieldset>	 
</form>
