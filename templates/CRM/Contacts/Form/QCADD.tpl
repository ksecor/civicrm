 {$form.javascript}
 <form {$form.attributes} >
 <table border="0" width="100%" cellpadding="2" cellspacing="2" id="crm-quick-create">
 <tr><td>
	 {if $form.hidden}
	 {$form.hidden}
	 {/if}
</td></tr>
	 <tr><td class="form-item">{$form.first_name.label}</td></tr> 
	 <tr><td class="form-item">{$form.first_name.html}</td></tr>	 
	 <tr><td class="form-item">{$form.last_name.label}</td></tr>
	 <tr><td class="form-item">{$form.last_name.html} </td> </tr>
	 <tr><td class="form-item">{$form.phone.label}</td></tr>
	 <tr><td class="form-item">{$form.phone.html}</td></tr>
	 <tr><td class="form-item">{$form.email.label}</td></tr>
	 <tr><td class="form-item">{$form.email.html}</td></tr>
	 <tr><td class="form-item">{$form.buttons.html}</td> </tr>
</table>
	 
</form>
