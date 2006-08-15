{* Quest College Match Application: High School Search Pop-up *}
<div id="school-search">
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{ts}Find Your High School{/ts}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}Find your school by entering school name and city, zip code and/or state. If you do not find your school, click <strong>Close Window</strong> and fill in your school name and address directly on the application form.{/ts}</p></td>
</tr>
<tr>
    <td class="grouplabel">{$form.school_name.label}</td>
    <td class="fieldlabel">{$form.school_name.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.city.label}</td>
    <td class="fieldlabel">{$form.city.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.postal_code.label}</td>
    <td class="fieldlabel">{$form.postal_code.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.state_province_id.label}</td>
    <td class="fieldlabel">{$form.state_province_id.html}</td>
</tr>
<tr><td class="grouplabel" colspan=2>
        <input type="hidden" name="schoolIndex" value="{$schoolIndex}">
        {$form._qf_SchoolSearch_refresh.html}
    </td>
</tr>
</table>
</div>

{if $searchDone } {* Search button clicked *}
    {if $searchCount}
        {if $searchRows} {* we've got rows to display *}
            <fieldset><legend>{ts}School Search Results{/ts}</legend>
            <div class="description">
                {ts}If your school appears below, click on the school name to complete the school information on your application. If not, you can modify your search criteria and try the search again. Otherwise click <strong>Close Window</strong> and enter your school name and address directly on the application form.{/ts}
            </div>
            {strip}
            <table>
            <tr class="columnheader">
            <th>{ts}School Name{/ts}</th>
            <th>{ts}Street{/ts}</th>
            <th>{ts}City{/ts}</th>
            <th>{ts}Postal Code{/ts}</th>
            <th>{ts}State{/ts}</th>
            </tr>
            {foreach from=$searchRows item=row}
            <tr class="{cycle values="odd-row,even-row"}">
                <td><strong><a href="#" class="underline" onclick="setSchool('{$schoolIndex}','{$row.code}','{$row.school_name}','{$row.street_address}','{$row.city}','{$row.state_province_id}','{$row.postal_code}','{$row.country_id}','{$row.school_type}'); return false;">{$row.school_name}</a></strong></td>
                <td>{$row.street_address}</td>
                <td>{$row.city}</td>
                <td>{$row.postal_code}</td>
                <td>{$row.state_province}</td>
            </tr>
            {/foreach}
            </table>
            {/strip}
            </fieldset>
        {else} {* too many results - we're only displaying 50 *}
            {capture assign=infoMessage}{ts}There were too many matching results. Please narrow your search by entering a more complete School Name and/or filling in additional search criteria.{/ts}{/capture}
            {include file="CRM/common/info.tpl"}
        {/if}
    {else} {* no valid matches for search params *}
            {capture assign=infoMessage}{ts 1=$form.school_name.value 2=$form.city.value 3=$stateProvince 4=$form.postal_code.value}No matching results for <ul><li>School Name like: %1</li><li>City like: %2</li><li>Zip Code like: %4</li><li>State like: %3</li></ul>Check your entries, or try fewer search criteria. If you do not find your school, click <strong>Close Window</strong> and fill in your school name and address directly on the application form.{/ts}{/capture}
            {include file="CRM/common/info.tpl"}                
    {/if} {* end if searchCount *}
    <div class="crm-submit-buttons">
        <input type="button" value="Close Window" class="form-submit" onclick="noSchool('{$schoolIndex}'); return false;" title="Close this window and return to the main application form." id="btn-close-window"><br />
    </div>
    <div class="spacer">&nbsp;</div>
{/if} {* end if searchDone *}

{literal}
<script type="text/javascript">
var thisOpener = window.opener; // global variable
function setSchool(schoolIndex,ceeb,sname,addr,city,state,postal,country,stype)
{
    var elem = 'custom_1_' + schoolIndex;
    thisOpener.document.getElementById(elem).value = ceeb;
    var elem = 'organization_name_' + schoolIndex;
    thisOpener.document.getElementById(elem).value = sname;
    var elem = 'location_' + schoolIndex + '_1_address_street_address';
    thisOpener.document.getElementById(elem).value = addr;
    var elem = 'location_' + schoolIndex + '_1_address_city';
    thisOpener.document.getElementById(elem).value = city;
    var elem = 'location_' + schoolIndex + '_1_address_state_province_id';
    thisOpener.document.getElementById(elem).value = state;
    var elem = 'location_' + schoolIndex + '_1_address_postal_code';
    thisOpener.document.getElementById(elem).value = postal;
    var elem = 'location_' + schoolIndex + '_1_address_country_id';
    thisOpener.document.getElementById(elem).value = country;
    var elem = 'custom_2_' + schoolIndex;
    thisOpener.document.getElementById(elem).value = stype;
    thisOpener.focus();
    var elem = 'location_' + schoolIndex + '_1_phone_1_phone';
    thisOpener.document.getElementById(elem).focus();
    window.close();
}
// Closing window without setting school - so set CEEB code to 0.
function noSchool(schoolIndex) {
    var elem = 'custom_1_' + schoolIndex;
    thisOpener.document.getElementById(elem).value = 0;
    var elem = 'organization_name_' + schoolIndex;
    thisOpener.document.getElementById(elem).value = '';
    var elem = 'location_' + schoolIndex + '_1_address_street_address';
    thisOpener.document.getElementById(elem).value = '';
    var elem = 'location_' + schoolIndex + '_1_address_city';
    thisOpener.document.getElementById(elem).value = '';
    var elem = 'location_' + schoolIndex + '_1_address_state_province_id';
    thisOpener.document.getElementById(elem).value = '';
    var elem = 'location_' + schoolIndex + '_1_address_postal_code';
    thisOpener.document.getElementById(elem).value = '';
    var elem = 'location_' + schoolIndex + '_1_address_country_id';
    thisOpener.document.getElementById(elem).value = '';
    var elem = 'custom_2_' + schoolIndex;
    thisOpener.document.getElementById(elem).value = '';
    thisOpener.focus();
    var elem = 'organization_name_' + schoolIndex;
    thisOpener.document.getElementById(elem).focus();
    window.close();
}
</script>
{/literal}