{* This file provides the plugin for the im block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 

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

	{* The imt section provides the HTML for the im block *}
	{* The section loops as many times as indicated by the variable $imloop to give as many im blocks *}

	{* @var $imloop Gives the number of im loops to be displayed, assigned in the Location.tpl file*}
	{* @var $smarty.section.imt.index Gives the current index on the section loop *}
	{* @var $imindex Gives the current index on the section loop *}
	{* @var $im_screenname Contains the name of the im text box *}
	{* @var $im_service_id Contains the name of the im select box *}
	{* @var $exim Contains the name of the im expansion link *}
	{* @var $hideim Contains the name of the im hide link *}

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
