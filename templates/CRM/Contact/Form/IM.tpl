<fieldset>
<table border="0" cellpadding="2" cellspacing="2" width="100%"> 
	<!----------- LOADING IM BLOCK ----------->	
	 <tr>


		 <td class="form-item" width = {$width}>
		 <label>{$form.$lid.im_service_id_1.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.im_service_id_1.html}{$form.$lid.im_screenname_1.html}
		 <div class="description">Select IM service and enter screen-name / user id.</div>
		 </td>
		 
	 </tr>


	{section name = imt start = 2 loop = $imloop}

	{assign var = "imindex" value = "`$smarty.section.imt.index`"}
	{assign var = "im_service_id" value = "im_service_id_`$imindex`"}
	{assign var = "im_screenname" value = "im_screenname_`$imindex`"}
	{assign var = "exim" value = "exim`$imindex`_`$index`"} 	
 	{assign var = "hideim" value = "hideim`$imindex`_`$index`"}

	 <tr><!-- IM 2.-->

		 <td colspan = "2">
			 <table id="expand_IM_{$index}_{$imindex}">
			 <tr>
				 <td>
				 {$form.$exim.html}
				 </td>
			 </tr>
			 </table>
		 </td>
	 </tr>
	 <tr>

		 <td colspan = "2">
		 <table id="IM_{$index}_{$imindex}">
			 <tr>
				 <td class="form-item" width = {$width}>
				 <label>{$form.$lid.$im_service_id.label}</label></td>
				 <td class="form-item">
				 {$form.$lid.$im_service_id.html}{$form.$lid.$im_screenname.html}
				 <div class="description">Select IM service and enter screen-name / user id.</div></td>
			 </tr>
			 <tr>
				 <td colspan="2">
				 {$form.$hideim.html}
				 </td>
			 </tr>
		 </table>
		 </td>
	 </tr>
	{/section}
</table>
</fieldset>
