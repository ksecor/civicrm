<fieldset>
<table border="0" cellpadding="2" cellspacing="2" width="100%"> 
	<!----------- LOADING EMAIL BLOCK--------- -->

	 <tr>

		 <td class="form-item" width = {$width}>
			 <label>{$form.$lid.email_1.label}</label>
		 </td>
		 <td class = "form-item">
			 {$form.$lid.email_1.html}
		 </td>
		
	 </tr>

	{section name = emailt start = 2 loop = $emailloop}

	{assign var = "emindex" value = "`$smarty.section.emailt.index`"}
	{assign var = "email" value = "email_`$emindex`"}
	{assign var = "exem" value = "exem`$emindex`_`$index`"} 	
 	{assign var = "hideem" value = "hideem`$emindex`_`$index`"}

	 <tr><!-- email 2.-->

		 <td colspan = "2">
		 <table id="expand_email_{$index}_{$emindex}" >
			 <tr>
				 <td>
				 {$form.$exem.html}
				 </td>
			 </tr>
		 </table>
		 </td>
	 </tr>

	 <tr>

		 <td colspan = "2">
		 <table id="email_{$index}_{$emindex}">
			 <tr>
				 <td class="form-item" width = {$width}>
				 <label>{$form.$lid.$email.label}</label>
				 </td>
				 <td class = "form-item">
				 {$form.$lid.$email.html}
				 </td>
		 </tr>
		 <tr>

			 <td>
			 {$form.$hideem.html}
			 </td>
		 </tr>
		 </table>
		 </td>
	 </tr>
	{/section}
</table>
</fieldset>
