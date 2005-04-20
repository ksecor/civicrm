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
		<div><label>In Group (s)</label></div>
		<div class="listing-box">
			{foreach from=$form.cb_group item="cb_group_val"}
			<div class="{cycle values="odd-row,even-row"}">
			{$cb_group_val.html}
			</div>
			{/foreach}
		</div>
		</div>
	 
		<div class="three-col3">
		<span><label>In Category (s)</label></sapn>
		<div class="listing-box">
			{foreach from=$form.cb_category item="cb_category_val"} 
			<div class="{cycle values="odd-row,even-row"}">
			{$cb_category_val.html}
			</div>
			{/foreach}
		</div>
		</div>
	</div>
</div>
<div class="form-item">	
	<p>
	<div class="horizontal-position two-col1">
	<div>
	<span class="labels">{$form.sort_name.label}</span><span="fields">{$form.sort_name.html}</span>
	</div>
	<div>
	<span class="fields description font-italic">
	Individual, Organization or Household Name
	</span>
	</div>
	</div>	
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
	
<fieldset><legend>Save Search</legend>
	<p>
	<div class="horizontal-position">
		<span class="two-col1">
		{*$form.cb_ss.label*} {$form.cb_ss.html}
		</span>	
		<div class="spacer"></div>
	</div>
	</p>
	
	<p>
	<div class="horizontal-position">
		<div class="two-col1">
		<span class="labels">{$form.ss_name.label}</span>
		<span class="fields">{$form.ss_name.html}</span>
		</div>
		<div class="spacer"></div>
	</div>
	</p>

	<p>
	<div class="horizontal-position">
		<div class="two-col1">
		<span class="labels">{$form.ss_description.label}</span>
		<span class="fields">{$form.ss_description.html}</span>
		</div>
		<div class="spacer"></div>
	</div>
	</p>
</fieldset>

	<div>
	<span class="three-col3">
	<span class="float-right">{$form.buttons.html}</span>
	</span>
	</div>
	</p>
</div>
<div class="spacer"></div>

{if $qill}
<p>
Fetching {$qill}
<p>
{/if}

{if $rowsEmpty}

    {* No matches for search criteria *}
    <div class="messages status">
        <img src="crm/i/Inform.gif" alt="status"> &nbsp;
        No matches were found for your browse criteria.
        <ul>
        <li>check your spelling
        <li>try a different spelling or use fewer letters</li>
        <li>if you are searching within a Group or Category, try 'any group' or 'any category'</li>
        <li>add a <a href="crm/contact/addI?c_type=Individual&reset=1">New Individual</a>,
        <a href="crm/contact/addO?c_type=Organization&reset=1">Organization</a> or
        <a href="crm/contact/addH?c_type=Household&reset=1">Household</a></li>
        </ul>
    </div>

{else}
<div>
	<p>
	{include file="CRM/Contact/Selector.tpl"}
	</p>
</div>
<div class="spacer"></div>
{/if}
</div>
</form>
