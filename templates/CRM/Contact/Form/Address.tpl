{* This file provides the plugin for the address block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 
 
<fieldset>
<table border="0" cellpadding="2" cellspacing="2" width="100%"
	 <tr>

		 <td class="form-item" width = {$width}>
		 <label>{$form.$lid.street_address.label}</label>
		</td>
		 <td class="form-item">
		 {$form.$lid.street_address.html}<!--br/-->
		 <div class="description">Street number, street name, apartment/unit/suite - OR P.O. box</div>
		 </td>
	 </tr>
	 <tr>

		 <td class="form-item" width = {$width}>
		 <label>{$form.$lid.supplemental_address_1.label}</label>
		</td>
		 <td class="form-item">
		 {$form.$lid.supplemental_address_1.html}<!--br/-->

		 <div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
		 </td>
	 </tr>
	 <tr>

		 <td class="form-item" width = {$width}>
		 <label>{$form.$lid.city.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.city.html}<!--br/-->
		 </td>
	 </tr>
	 <tr>

		 <td class="form-item" width = {$width}>
		 <label>{$form.$lid.state_province_id.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.state_province_id.html}
		 </td>
	 </tr>
	 <tr>

		 <td class="form-item" width = {$width}>
		 <label>{$form.$lid.postal_code.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.postal_code.html}<!--br/-->
		 </td>
	 </tr>
	 <tr>

		 <td class="form-item" width = {$width}>
		 <label>{$form.$lid.country_id.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.country_id.html}
		 </td>
	 </tr>
</table>
</fieldset>
