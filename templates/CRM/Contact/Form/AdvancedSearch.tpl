<form {$form.attributes}>
{$form.hidden}

<fieldset>
<div class="form-item">
	
	<div>
	
		<span class="float-col1">
		<span>{$form.contact_type.label}</span>
		<span class="fields">{$form.contact_type.html}</span>
		</span>
	  
		<span class="float-col2">
		<span>{$form.group_id.label}</span>
		<span class="fields">{$form.group_id.html}</span>
		</span>
	 
		<span class="float-col3">
		<span>{$form.category_id.label}</sapn>
		<span class="fields">{$form.category_id.html}</span>
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
