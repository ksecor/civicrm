<div id="location" class="form-item">
    <table class="noborder">
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
        {if $form.postal_code.html}
          <tr>
            <td class="label">{$form.postal_code.label}</td>
            <td>{$form.postal_code.html}&nbsp;&nbsp;<label>{ts}AND{/ts}</label></td> 
            <td class="label">{$form.postal_code_low.label}</td>
            <td>{$form.postal_code_low.html|crmReplace:class:six}</td>
          </tr>
          <tr>
            <td> &nbsp; </td>
            <td> &nbsp; </td>
            <td class="label">{$form.postal_code_high.label}</td>
            <td {$form.postal_code_high.html|crmReplace:class:six}</td>
          </tr>
        {/if}
    	<tr>
            <td class="label">{$form.location_type.label}</td>
            <td>{$form.location_type.html} 
                <div class="description">
                    {ts}Location search uses the PRIMARY location for each contact by default. To search by specific location types (e.g. Home, Work...), check one or more boxes above.{/ts}
                </div> 
            </td>
            <td class="label">{$form.address_name.label}</td><td>{$form.address_name.html|crmReplace:class:medium}</td>
        </tr>
    	<tr>
            <td class="label">{$form.world_region.label}</td>
            <td>{$form.world_region.html}</td>
            <td class="label">{$form.county.label}</td>
 	    <td>{$form.county.html|crmReplace:class:big}</td>
        </tr>
    </table>
</div>

