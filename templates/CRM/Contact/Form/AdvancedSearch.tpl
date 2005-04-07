{debug}
<form {$form.attributes}>
{$form.hidden}

<fieldset>
<div class="form-item">
	
	<div>
	
		<span class="float-col1">
		<span>{$form.cb_contact_type.label}</span>
		<span class="fields">{$form.cb_contact_type.html}</span>
		</span>
	  
		<span class="float-col2">
		<span>{$form.cb_group.label}</span>
		<span class="fields">{$form.cb_group.html}</span>
		</span>
	 
		<span class="float-col3">
		<span>{$form.cb_category.label}</sapn>
		<span class="fields">{$form.cb_category.html}</span>
		</span>
	 
	</div>
	
</div>
<div class="form-item">	
	<p>
	<div>
	<span class="horizontal-position">{$form.last_name.label}{$form.last_name.html}</span>
	<span class="horizontal-position">{$form.first_name.label}{$form.first_name.html}</span>
	</div>
	<span class="horizontal-position"><label class="description"><i>Last name, organization or household name </i></label></span>
	</p>
</div>
</fieldset>

<fieldset>
<div class="form-item">
	<div>
	<span class="horizontal-position">{$form.street_name.label}{$form.street_name.html}</span>
	<span class="horizontal-position">{$form.city.label}{$form.city.html}</span>
	</div>

<p>{$form.state_province.label}
<p>{$form.state_province.html}

<p>{$form.country.label}
<p>{$form.country.html}

<p>{$form.postal_code.label}
<p>{$form.postal_code.html}
<p>{$form.postal_code_low.label}
<p>{$form.postal_code_low.html}
<p>{$form.postal_code_high.label}
<p>{$form.postal_code_high.html}

<p>{$form.cb_location_type.label}
<p>{$form.cb_location_type.html}

<p>{$form.cb_primary_location.label}
<p>{$form.cb_primary_location.html}

<p>{$form.submit.label}
<p>{$form.submit.html}


</div>
</fieldset>
</form>
