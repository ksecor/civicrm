{literal}
<style type="text/css">
.heading1{
	font: 26px "Lucida Sans",Arial,Helvetica,Sans-Serif;
	color: #999999;
	margin: 0 0 0 0;
}

.text1{
	font: 14px Lucida Sans,Arial,Helvetica,Sans-Serif;
	color: #73bcdf;
	margin: 0 0 0 0;
}
	
.text2{
	font: 14px Lucida Sans,Arial,Helvetica,Sans-Serif;
	color: #054685;
	margin: 0 0 0 0;
}

.text3{
	font: 14px Lucida Sans,Arial,Helvetica,Sans-Serif;
	color: #a5adb4;
	margin: 0 0 0 0;
}

.text4 a:link{
	font: 15px Lucida Sans,Arial,Helvetica,Sans-Serif;
	color: #054685;
	margin: 0 0 0 0;
	text-decoration:none;
}
	
.text4 a:hover{
	font: 15px Lucida Sans,Arial,Helvetica,Sans-Serif;
	color: #72bcdf;
	margin: 0 0 0 0;
	text-decoration: underline overline;
}	

.text4 a:active{
	font: 15px Lucida Sans,Arial,Helvetica,Sans-Serif;
	color: #054685;
	margin: 0 0 0 0;
	text-decoration:none;
}

.text4 a:visited{
	font: 15px Lucida Sans,Arial,Helvetica,Sans-Serif;
	color: #054685;
	margin: 0 0 0 0;
	text-decoration:none;
}
</style>
{/literal}

<fieldset>
  <legend>Search Crieria</legent>
<div id="searchForm">
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
    <tr>
        <td colspan="3" align="center">
            <span class="heading1">Student Search<br /><br /></span>
        </td>
    </tr>
    
    {* Name, Gender, Email-All People Searches *}
    <tr id="id-first-name">
        <td><span class="text1"><strong>Basic Info</strong></span></td>
        <td class="label text3">{$form.first_name.label}</span></td>
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
    <tr id="id-org-name"><td></td> 
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
                <tr id="id-custom-{$customID}"><td><span class="text1"><strong>School Info</strong></span></td>
                {assign var="first" value=0}
            {else}
                <tr id="id-custom-{$customID}"><td></td> 
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
</div>
</fieldset>

{literal} 
<script type="text/javascript">
function showHideZ (element) {
    alert('Clicked');
}
</script>
{/literal}

{if $rows}
Search Count: <b>{$rowCount}</b>
<table>
<tr><th>Contact ID</th><th>Sort Name</th></tr>
{foreach from=$rows key=id item=row}
<tr><td>{$row.contact_id}</td><td>{$row.sort_name}</td></tr>
{/foreach}
</table>
{/if}

