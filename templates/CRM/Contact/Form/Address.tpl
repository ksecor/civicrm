{* This file provides the plugin for the Address block in the Location block *}

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var location.$index Contains the current location id, and assigned in the Location.tpl file *}
 
<fieldset><legend>{ts}Mailing Address{/ts}</legend>
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.street_address.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.street_address.html}
    <div class="description font-italic">Street number, street name, apartment/unit/suite - OR P.O. box</div>
    </span>
</div>

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.supplemental_address_1.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.supplemental_address_1.html}
    <div class="description font-italic">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
    </span>
</div>

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.supplemental_address_2.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.supplemental_address_2.html}
    <div class="description font-italic">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
    </span>
</div>

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.city.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.city.html}
    </span>
</div>

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.state_province_id.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.state_province_id.html}
    </span>
</div>

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.postal_code.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.postal_code.html}
    </span>
</div>
		 
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.country_id.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.country_id.html}
    </span>
</div>
<!-- Spacer div forces fieldset to contain floated elements -->
<div class="spacer"></div>
</fieldset>
