{* This file provides the plugin for the address block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 
 
<fieldset>
<div class="form-item">
    {$form.location.$index.address.street_address.label}
    {$form.location.$index.address.street_address.html}
    <div class="description">Street number, street name, apartment/unit/suite - OR P.O. box</div>
</div>

<div class="form-item">
    {$form.location.$index.address.supplemental_address_1.label}
    {$form.location.$index.address.supplemental_address_1.html}
    <div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
</div>

<div class="form-item">
    {$form.location.$index.address.supplemental_address_2.label}
    {$form.location.$index.address.supplemental_address_2.html}
    <div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
</div>

<div class="form-item">
    {$form.location.$index.address.city.label}
    {$form.location.$index.address.city.html}
</div>

<div class="form-item">
    {$form.location.$index.address.state_province_id.label}
    {$form.location.$index.address.state_province_id.html}
</div>

<div class="form-item">
    {$form.location.$index.address.postal_code.label}
    {$form.location.$index.address.postal_code.html}
</div>
		 
<div class="form-item">
    {$form.location.$index.address.country_id.label}
    {$form.location.$index.address.country_id.html}
</div>
</fieldset>
