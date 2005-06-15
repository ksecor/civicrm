{* Advanced Search Criteria Fieldset *}
<fieldset>
    <legend><span id="searchForm[hide]"><a href="#" onClick="hide('searchForm','searchForm[hide]'); show('searchForm[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a></span>
        {if $context EQ 'smog'}{ts}Find Members of this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
  <div class="form-item">
    {strip}
	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html}
                <div class="description font-italic">
                  {ts}Complete OR partial contact name.{/ts}
                </div>
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>
		<tr>
            <td><label>{ts}Contact Type(s){/ts}</label><br />
                {$form.cb_contact_type.html}
            </td>
            <td><label>{ts}In Group(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.cb_group item="cb_group_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                    {$cb_group_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
            <td><label>{ts}Tag(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.cb_tag item="cb_tag_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$cb_tag_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
		</tr>
    </table>
    <fieldset><legend>{ts}Location Criteria{/ts}</legend>
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
            <td>{$form.postal_code.html}&nbsp;&nbsp;<label>{ts}OR{/ts}</label></td> 
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
                {ts}Ignore any contact locations not marked as primary for this search.{/ts}
                </span>
            </td>
            <td></td>
        </tr>

        <tr>
            <td>
                {$form.activity_type.label} {$form.activity_type.html}
            </td>
            <td>
                {$form.activity_from_date.label} {$form.activity_from_date.html}
            </td>
            <td>
                {$form.activity_to_date.label} {$form.activity_to_date.html}
            </td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="label">{$form.buttons.html}</td>
        </tr>
    </table>
    {/strip}
    </fieldset>
    {include file="CRM/pagerAToZ.tpl" }
  </div>
</fieldset>
