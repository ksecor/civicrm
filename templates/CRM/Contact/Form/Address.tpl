{* This file provides the plugin for the address block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 
 
<fieldset>
<div class="form-item">
    {$form.$lid.street_address.label}
    {$form.$lid.street_address.html}
    <div class="description">Street number, street name, apartment/unit/suite - OR P.O. box</div>
</div>

<div class="form-item">
    {$form.$lid.supplemental_address_1.label}
    {$form.$lid.supplemental_address_1.html}
    <div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
</div>

<div class="form-item">
    {$form.$lid.city.label}
    {$form.$lid.city.html}
</div>

<div class="form-item">
    {$form.$lid.state_province_id.label}
    {$form.$lid.state_province_id.html}
</div>

<div class="form-item">
    {$form.$lid.postal_code.label}
    {$form.$lid.postal_code.html}
</div>
		 
<div class="form-item">
    {$form.$lid.country_id.label}
    {$form.$lid.country_id.html}
</div>
</fieldset>
