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

	<div>
	<span></span>
	</div>
</div>
</fieldset>
</form>
