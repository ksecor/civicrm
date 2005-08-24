{* This file provides the plugin for the Address block in the Location block *}

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var location.$index Contains the current location id, and assigned in the Location.tpl file *}

<script type="text/javascript" src="{crmURL p='civicrm/server/stateCountry' q="set=1&path=civicrm/server/stateCountry"}"></script>
<script type="text/javascript" src="{$config->resourceBase}js/StateCountry.js"></script>
 
<fieldset><legend>{ts}Address{/ts}</legend>
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.street_address.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.street_address.html}
    <div class="description font-italic">{ts}Street number, street name, apartment/unit/suite - OR P.O. box{/ts}</div>
    </span>
</div>

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.supplemental_address_1.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.supplemental_address_1.html}
    <div class="description font-italic">{ts}Supplemental address info, e.g. c/o, department name, building name, etc.{/ts}</div>
    </span>
</div>

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.supplemental_address_2.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.supplemental_address_2.html}
    <div class="description font-italic">{ts}Supplemental address info, e.g. c/o, department name, building name, etc.{/ts}</div>
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
    {$form.location.$index.address.state.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.state.html}
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
    {$form.location.$index.address.postal_code.html}- {$form.location.$index.address.postal_code_suffix.html}
    <div class="description font-italic">{ts}Enter optional 'add-on' code after the dash ('plus 4' code for U.S. addresses).{/ts}</div>
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

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.geo_code_1.label},
    {$form.location.$index.address.geo_code_2.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.geo_code_1.html},
    {$form.location.$index.address.geo_code_2.html}
    </span>
</div>
<!-- Spacer div forces fieldset to contain floated elements -->
<div class="spacer"></div>
</fieldset>

