<div id="location" class="form-item">
    <table class="form-layout">
	<tr>
        <td>
		{$form.location_type.label}<br />
        {$form.location_type.html} 
        <div class="description" >
            {ts}Location search uses the PRIMARY location for each contact by default.{/ts}<br /> 
            {ts}To search by specific location types (e.g. Home, Work...), check one or more boxes above.{/ts}
        </div> 
        </td>
        <td colspan="2">{$form.street_address.label}<br />
            {$form.street_address.html}<br />
            {$form.city.label}<br />
            {$form.city.html}
  	    </td>	   
    </tr>
           
    <tr>
        <td>
        {if $form.postal_code.html}
		<table class="inner-table">
		   <tr>
			<td>
				{$form.postal_code.label}<br />
                {$form.postal_code.html}
			</td>
			<td>
				&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
				<label>{ts}OR{/ts}</label>
				&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
			</td>
			<td><label>{ts}Postal Code{/ts}</label>
				{$form.postal_code_low.label|replace:'-':'<br />'}
		                &nbsp;&nbsp;{$form.postal_code_low.html|crmReplace:class:six}
                {$form.postal_code_high.label}
                		&nbsp;&nbsp;{$form.postal_code_high.html|crmReplace:class:six}
			</td>
		    </tr>
		</table>
        {/if}&nbsp;
        </td>
        <td>{$form.state_province.label}<br />
             {$form.state_province.html|crmReplace:class:big}&nbsp;
        </td>
        <td>{$form.country.label}<br />
            {$form.country.html|crmReplace:class:big}&nbsp;
        </td>
    </tr>
          	
    <tr>
	    <td>{$form.world_region.label}<br />
            {$form.world_region.html}&nbsp;
	    </td>  
	    <td>{$form.county.label}<br />
 	        {$form.county.html|crmReplace:class:big}&nbsp;
	    </td>
        <td colspan="2">{$form.address_name.label}<br />
            {$form.address_name.html|crmReplace:class:medium}
	    </td>
	 </tr>
    </table>
</div>

