<div id="location_show" class="data-group">
<a href="#" onclick="hide('location_show'); show('location'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
<label>{ts}Location{/ts}</label>
</div>
<div id="location">
  <fieldset><legend><a href="#" onclick="hide('location'); show('location_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Location{/ts}</legend>
    <table class="form-layout">
        <tr>
            <td class="label">{$form.street_address.label}</td>
            <td>{$form.street_address.html}</td>
            <td class="label">{$form.city.label}</td>
            <td>{$form.city.html}</td>
        </tr>
        <tr>
            <td class="label">{$form.state_province.label}</td>
            <td>{$form.state_province.html|crmReplace:class:big}</td>
            <td class="label">{$form.country.label}</td>
            <td>{$form.country.html|crmReplace:class:big}</td>
        </tr>
        <tr>
            <td class="label">{$form.postal_code.label}</td>
            <td>{$form.postal_code.html}&nbsp;&nbsp;<label>{ts}OR{/ts}</label></td> 
            <td class="label">{$form.postal_code_low.label}</td>
            <td>{$form.postal_code_low.html|crmReplace:class:six}
                {$form.postal_code_high.label}
                {$form.postal_code_high.html|crmReplace:class:six}
            </td>
        </tr>
		<tr>
            <td class="label">{$form.location_type.label}</td>
            <td>{$form.location_type.html} 
                <div class="description">
                    {ts}Location search uses the PRIMARY location for each contact by default. To search by specific location types (e.g. Home, Work...), check one or more boxes above.{/ts}
                </div> 
            </td>
            <td class="label">{$form.location_name.label}</td><td>{$form.location_name.html|crmReplace:class:medium}</td>
        </tr>
    </table>
    </fieldset>
</div>

