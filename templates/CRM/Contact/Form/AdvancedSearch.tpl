{*debug*}
<form {$form.attributes}>
{$form.hidden}

<div class="bottom-light-coloured">
<div class="form-item">
	<div>
		<span class="three-col1">
		<span>{$form.cb_contact_type.label}</span>
		<span class="fields">{$form.cb_contact_type.html}</span>
		</span>
	  
		<span class="three-col2">
		<span>{$form.cb_group.label}</span>
		<span class="fields">{$form.cb_group.html}</span>
		</span>
	 
		<span class="three-col3">
		<span>{$form.cb_category.label}</sapn>
		<span class="fields">{$form.cb_category.html}</span>
		</span>
	</div>
</div>
<div class="form-item">	
	<p>
	<div class="horizontal-position">
	<span class="two-col1">
	<span class="labels">{$form.last_name.label}</span><span="fields">{$form.last_name.html}</span>
	</span>
	<span class="two-col2">
	<span class="labels">{$form.first_name.label}</span><span="fields">{$form.first_name.html}</span>
	</span>
	</div>
	<span class="two-col1">
		<span class="fields description font-italic">
		Last name, organization or household name
		</span>
	</span>
	</p>
</div>
<div class="spacer"></div>
</div>

<div class="top-light-coloured">
<div class="form-item">
	<div class="horizontal-position">
	<span class="two-col1">
		<span class="labels">
		Located In
		</span>
	</span>
	<div class="spacer"></div>
	</div>

	<p>
	<div class="horizontal-position">
	<span class="two-col1">
		<span class="labels">{$form.street_name.label}</span>
		<span class="fields">{$form.street_name.html}</span>
	</span>
	<span class="two-col2">
		<span class="labels">{$form.city.label}</span>
		<span="fields">{$form.city.html}</span>
	</span>
	<div class="spacer"></div>
        </div>
	</p>

	<p>
	<div class="horizontal-position">
	<span class="two-col1">
		<span class="labels">{$form.state_province.label}</span>
		<span class="fields">{$form.state_province.html}</span>
	</span>
	<span class="two-col2">
		<span class="labels">{$form.country.label}</span>
		<span class="fields">{$form.country.html}</span>
	</span>
	<div class="spacer"></div>
	</div>
	</p>

	<p>
	<div>
	<span class="three-col1">
		<span class="labels">{$form.postal_code.label}</span>
		<span class="fields">{$form.postal_code.html}</span> OR
	</span>
	<span class="three-col2">
		<span class="labels">{$form.postal_code_low.label}</span>
		<span class="fields">{$form.postal_code_low.html}</span>
	</span>
	<span class="three-col3">
		<span class="labels">{$form.postal_code_high.label}</span>
		<span class="fields">{$form.postal_code_high.html}</span>
	</span>
	<div class="spacer"></div>
	</div>
	</p>

	<p>
	<div>
	<span class="horizontal-position">{$form.cb_location_type.label}</span>
	<span>{$form.cb_location_type.html}</span>
	<div class="spacer"></div>
	</div>
	</p>

	<p>
	<div class="horizontal-position">
	<!--span class="labels">{$form.cb_primary_location.label}</span-->
	<span>{$form.cb_primary_location.html}</span>
	<div>
		<span class="description font-italic">
		Ignore any contact locations not marked as primary for this search.
		</span>
	</div>
	<div class="spacer"></div>
	</div>
	</p>

	<p>	
	<div>
	<span class="float-right">{$form.buttons.html}</span>
	</div>
	</p>
</div>
<div class="spacer"></div>
</div>
</form>
