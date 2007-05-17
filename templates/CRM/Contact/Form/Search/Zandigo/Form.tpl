<div id="searchForm_show" class="form-item">
  <a href="#" onclick="hide('searchForm_show'); show('searchForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
  <label>{ts}Edit Search Criteria{/ts}</label>
</div>
<div class="spacer"></div> 
<div id="searchForm">
<fieldset>
<legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>Search Criteria</legend>
<div class="form-item">
<table class="form-layout">
    <tr>
    <td><span class="text2">Search For</span></td> 
    {foreach from=$customFields item=details key=customID}
        {if $details.loc == 'top'}
            {assign var="customField" value="custom_"|cat:$customID}
            <td align='left' class="nowrap">{$form.$customField.label}<br/>{$form.$customField.html}</td> 
        {/if}
    {/foreach}
    </tr>
    <tr id="id-people-title">
        <td colspan="3" align="center">
            <span class="heading1">People Search<br /><br /></span>
        </td>
    </tr>
    <tr id="id-org-title">
        <td colspan="3" align="center">
            <span class="heading1">Organization Search<br /><br /></span>
        </td>
    </tr>
    
    {* Name, Gender, Email-All People Searches *}
    <tr id="id-first-name">
        <td class="text1"><strong>Basic Info</strong></td>
        <td class="label text3">{$form.first_name.label}</td>
        <td class="nowrap">{$form.first_name.html}</td>
    </tr>
    <tr id="id-middle-name"><td></td> 
        <td class="label text3">{$form.middle_name.label}</td>
        <td class="nowrap">{$form.middle_name.html}</td>
    </tr>
    <tr id="id-last-name"><td></td> 
        <td class="label text3">{$form.last_name.label}</td>
        <td class="nowrap">{$form.last_name.html}</td>
    </tr>
    <tr id="id-gender"><td></td> 
        <td class="label text3">{$form.gender.label}</td>
        <td class="nowrap">{$form.gender.html}</td>
    </tr>
    <tr id="id-email"><td></td> 
        <td class="label text3">{$form.email.label}</td>
        <td class="nowrap">{$form.email.html}</td>
    </tr>
    
    {* Name Field-Organization Searches *}
    <tr id="id-org-name">
        <td><span class="text1"><strong>Basic Info</strong></span></td>
        <td class="label text3">{$form.organization_name.label}</td>
        <td class="nowrap">{$form.organization_name.html}</td>
    </tr>

    {* Location Fields-All Searches *}
    <tr id="id-city"><td></td> 
        <td class="label text3">{$form.city.label}</td>
        <td class="nowrap">{$form.city.html}</td>
    </tr>
    <tr id="id-state"><td></td> 
        <td class="label text3">{$form.state_province.label}</td> 
        <td class="nowrap">{$form.state_province.html}</td>
    </tr>
    <tr id="id-postal"><td></td> 
        <td class="label text3">{$form.postal_code.label}</td>
        <td class="nowrap">{$form.postal_code.html}</td>
    </tr>
    <tr id="id-country"><td></td> 
        <td class="label text3">{$form.country.label}</td> 
        <td>{$form.country.html}</td>
    </tr>

    {* High School Info Fields-Students and Guidance Counselors *}
    {assign var="first" value=1}
    {foreach from=$customFields item=details key=customID}
        {if $details.loc == 'bottom'}
            {if $first}
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr id="id-custom_{$customID}"><td><span class="text1"><strong>School Info</strong></span></td>
                {assign var="first" value=0}
            {else}
                <tr id="id-custom_{$customID}"><td></td> 
            {/if}           
            {assign var="customField" value="custom_"|cat:$customID}
            <td class="label text3">{$form.$customField.label}</td> 
            <td>{$form.$customField.html}</td>
            </tr>
        {/if}
    {/foreach}

    <tr>
        <td colspan="2"></td>
        <td>{$form.buttons.html}</td>
    </tr>
</table>
</div>
</fieldset>
</div>
