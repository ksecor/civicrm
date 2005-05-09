{* Advanced Search Criteria Fieldset *}
<fieldset><legend><span id="searchForm[hide]"><a href="#" onClick="hide('searchForm','searchForm[hide]'); show('searchForm[show]'); return false;">(-)</a> </span>Search Criteria</legend>
  <div class="form-item">
    {strip}
	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html}<br />
                <span class="description font-italic">
                    Full or partial name
                </span>
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>
		<tr>
            <td><label>Contact Type(s)</label><br />
                {$form.cb_contact_type.html}
            </td>
            <td><label>In Group(s)</label><br />
                <div class="listing-box">
                    {foreach from=$form.cb_group item="cb_group_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                    {$cb_group_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
            <td><label>Category(s)</label><br />
                <div class="listing-box">
                    {foreach from=$form.cb_category item="cb_category_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$cb_category_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
		</tr>
    </table>
    <fieldset><legend>Location Criteria</legend>
    <table class="form-layout">
        <tr>
            <td class="label">{$form.street_name.label}</td>
            <td>{$form.street_name.html}</span>
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
            <td>{$form.postal_code.html}&nbsp;&nbsp;<label>OR</label></td> 
            <td class="label">{$form.postal_code_low.label}</span>
            <td>{$form.postal_code_low.html|crmReplace:class:six}
                {$form.postal_code_high.label}
                {$form.postal_code_high.html|crmReplace:class:six}
            </td>
        </tr>
		<tr>
        
            <td class="label">{$form.cb_location_type.label}</td>
            <td colspan="3">
                {$form.cb_location_type.html}
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2">
                {$form.cb_primary_location.html}<br />
                <span class="description font-italic">
                Ignore any contact locations not marked as primary for this search.
                </span>
            </td>
            <td class="label">{$form.buttons.html}</td>
        </tr>
    </table>
    {/strip}
    </fieldset>
  </div>
</fieldset>
