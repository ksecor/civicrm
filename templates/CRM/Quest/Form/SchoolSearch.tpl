{* Quest College Match Application: High School Search Pop-up *}

<div id="school-search">
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{ts}Find Your High School{/ts}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}Find your current High School by entering school name and city, zip code and/or state. If you do not find your school, click <strong>Add My High School</strong> and fill in your school name and address.{/ts}</p></td>
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
    <td class="grouplabel">{$form.state_province.label}</td>
    <td class="fieldlabel">{$form.state_province.html}</td>
</tr>
<tr><td class="grouplabel" colspan=2>{$form._qf_SchoolSearch_refresh.html}</td>
</table>
</div>

{if $searchDone } {* Search button clicked *}
    {if $searchCount}
        {if $searchRows} {* we've got rows to display *}
            <fieldset><legend>{ts}School Search Results{/ts}</legend>
            <div class="description">
                {ts}If your school appears below, click on the school name to complete the school information on your application. If not, you can modify your search criteria and try the search again. Otherwise click <strong>Add My School</strong> below and enter the school information on the form.{/ts}
            </div>
            {strip}
            <table>
            <tr class="columnheader">
            <th>{ts}School Name{/ts}</th>
            <th>{ts}City{/ts}</th>
            <th>{ts}Postal Code{/ts}</th>
            <th>{ts}State{/ts}</th>
            </tr>
            {foreach from=$searchRows item=row}
            <tr class="{cycle values="odd-row,even-row"}">
                <td>{$row.school_name}</td>
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
            {capture assign=infoMessage}{ts 1=$form.school_name.value 2=$form.city 3=$form.state_province.value 4=$form.postal_code.value}No matching results for <ul><li>School Name like: %1</li><li>City like: %2</li><li>State like: %3</li><li>Zip Code like: %4</li></ul>Check your entries, or try fewer search criteria. If you still can not find you school, then click on <strong>Add Your School</strong> below.{/ts}{/capture}
            {include file="CRM/common/info.tpl"}                
    {/if} {* end if searchCount *}
{/if} {* end if searchDone *}

<div id="school-input">
<fieldset><legend>Add Your School</legend>
<tr>
    <td class="grouplabel">{$form.organization_name.label}</td>
    <td class="fieldlabel">{$form.organization_name.html}</td>
</tr>
<tr>
    <td class="grouplabel" rowspan="5"><label>{ts}School Address{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.location.1.address.street_address.html}<br />
         {ts}{edit}Number and Street (including apartment number){/edit}{/ts}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.location.1.address.city.html}<br/>{ts}{edit}City{/edit}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.location.1.address.state_province_id.html}<br/>{ts}{edit}State or Province{/edit}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.location.1.address.postal_code.html} - {$form.location.1.address.postal_code_suffix.html}<br />
        {ts}{edit}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/edit}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.$location.1.address.country_id.html}<br />{ts}{edit}Country{/edit}{/ts}</td>
</tr>
</table>
</fieldset>
</div>

