 {$form.javascript}
 <form {$form.attributes} >

 <table border="0" width="178" cellpadding="2" cellspacing="2" id="crm-quick-create">
	 <tr><td class="form-item">{$form.firstname.label}</td></tr> 
	 <tr><td class="form-item">{$form.firstname.html}</td></tr>	 
	 <tr><td class="form-item">{$form.lastname.label}</td></tr>
	 <tr><td class="form-item">{$form.lastname.html} </td> </tr>
	 <tr><td class="form-item">{$form.phone.label}</td></tr>
	 <tr><td class="form-item">{$form.phone.html}</td></tr>
	 <tr><td class="form-item">{$form.email.label}</td></tr>
	 <tr><td class="form-item">{$form.email.html}</td></tr>
	 <tr><td class="form-item">{$form.buttons.html}</td> </tr>
</table>
</form>
