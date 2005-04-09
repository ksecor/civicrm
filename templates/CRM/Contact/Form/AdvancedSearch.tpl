{debug}
<form {$form.attributes}>
{$form.hidden}
<div class="bottom-light-coloured">
<div class="form-item">
	<div class="horizontal-position">
		<div class="three-col1">
		<span class="font-size12pt">{$form.cb_contact_type.label}</span>	
		<span class="fields">{$form.cb_contact_type.html}</span>
		</div>
	  
		<div class="three-col2">
		<span>{$form.cb_group.label}</span>
		<div class="listing-box">
		{foreach from=$form.cb_group item="cb_group_val" key="cb_group_key"}
		<div {cycle values="odd-row,even-row"}>
			{if $cb_group_val.type eq checkbox}
			<div>{$cb_group_val.html}<br /></div>
			{/if}
		</div>	
		{/foreach}
		<!--span><p class="listing-box">{$form.cb_group.html}</p></span-->
		</div>
		</div>
	 
		<div class="three-col3">
		<span>{$form.cb_category.label}</sapn>
		<div class="listing-box">
		<div {cycle values="odd-row,even-row"}>
			{foreach from=$form.cb_category item="cb_category_val" key="cb_category_key"}
			{if $cb_category_val.type eq checkbox}
			<span>{$cb_category_val.html}<br /></span>
			{/if}
			{/foreach}
		</div>
		<!--span><p class="listing-box">{$form.cb_category.html}</p></span-->
		</div>
		</div>
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
	<span class="three-col1">
	<span class="font-size12pt"><label>Located In</label></span>	
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
		<span class="fields">{$form.state_province.html|crmReplace:class:big}</span>
	</span>
	<span class="two-col2">
		<span class="labels">{$form.country.label}</span>
		<span class="fields">{$form.country.html|crmReplace:class:big}</span>
	</span>
	<div class="spacer"></div>
	</div>
	</p>

	<p>
	<div class="horizontal-position">
	<span class="two-col1">
		<span class="labels">{$form.postal_code.label}</span>
		<span class="fields">{$form.postal_code.html} &nbsp; <label>OR</label></span> 
	</span>
	<span class="two-col2">
		<span>{$form.postal_code_low.label}</span>
		<span>{$form.postal_code_low.html|crmReplace:class:six}</span>
		<span>{$form.postal_code_high.label}</span>
		<span>{$form.postal_code_high.html|crmReplace:class:six}</span>
	</span>
	<div class="spacer"></div>
	</div>
	</p>

	<p>
	<div class="horizontal-position">
		<span>{$form.cb_location_type.label}</span>
		<span>{$form.cb_location_type.html}</span>
		<div class="spacer"></div>
	</div>
	</p>

	<p>
	<div class="horizontal-position">
		<span class="two-col1">
		<span>{$form.cb_primary_location.html}</span>
		</span>
		<div class="spacer"></div>
	</div>
	<div class="horizontal-position">
		<span class="two-col1">	
		<span class="description font-italic">
		Ignore any contact locations not marked as primary for this search.
		</span>
		</span>
		<div class="spacer"></div>
	</div>
	
	</p>

	<p>	
	<div>
	<span class="three-col3">
	<span class="float-right">{$form.buttons.html}</span>
	</span>
	</div>
	</p>
</div>
<div class="spacer"></div>
</div>

     <p>
       {include file="CRM/Contact/Selector.tpl"}
     </p>


</form>
