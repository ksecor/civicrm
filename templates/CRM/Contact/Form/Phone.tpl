<fieldset>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	 <!------------ LOADING PHONE BLOCK ------------->
	 <tr>

		 <td class="form-item" width = {$width} >
		 <label>{$form.$lid.phone_1.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.phone_type_1.html}{$form.$lid.phone_1.html}
		 </td>

	 </tr>


	{section name = phonet start = 2 loop = $phoneloop}

	{assign var = "phindex" value = "`$smarty.section.phonet.index`"}
	{assign var = "phone" value = "phone_`$phindex`"}
	{assign var = "phone_type" value = "phone_type_`$phindex`"}
	{assign var = "exph" value = "exph`$phindex`_`$index`"} 	
 	{assign var = "hideph" value = "hideph`$phindex`_`$index`"}

	 <tr><!-- Second phone block.-->

		 <td colspan = "2">
			 <table id="expand_phone_{$index}_{$phindex}">
			 <tr>
				 <td>
				 {$form.$exph.html}
				 </td>
			 </tr>
		 </table>
		 </td> 
		
	 </tr>
	 <tr>

		 <td colspan = "2">	

		 <table id="phone_{$index}_{$phindex}">
			 <tr>
				 <td class="form-item" width = {$width}>
				 <label>{$form.$lid.$phone.label}</label>
				 </td>
				 <td class="form-item">
				 {$form.$lid.$phone_type.html}{$form.$lid.$phone.html}
				 </td>
			 </tr>	

			 <tr>
				 <td colspan="2">
				 {$form.$hideph.html}
				 </td>
			 </tr>
		 </table>
		 </td> 
		
	 </tr>

	{/section}
</table>
</fielsdet>
