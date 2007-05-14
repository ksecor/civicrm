{assign var="showBlocks" value="'searchForm'"}
{assign var="hideBlocks" value="'searchForm_show','searchForm_hide'"}

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

{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlocks" value="'searchForm_show'"}
    {assign var="hideBlocks" value="'searchForm'"}

    <table align="left" width="630">
      <tr>
        <td align="left" width="33%">{$form.toggleSelect.html}&nbsp;<span class="text3">Select All</span></td>
        <td align="center" width="33%"><span class="text3">Results x-y of {$rowCount}</span></td>
        <td align="right" width="33%"><span class="text3">Page <strong>1</strong> 2 3 4 next</span></td>
      </tr>
      <tr height="35">
        <td colspan="3" align="center"><span class="text2">Add to Database </span></td>
      </tr>
    </table>
    <div class="spacer"></div>

    <table class="form-layout-compressed">
    {foreach from=$rows key=id item=row}
        <tr>
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td>
            <td><img src="{$row.image_URL}" width="20" height="20" alt="{$row.display_name}" /></td>
            <td>
                Name:<br />
                Status:<br />
                {if $row.custom_91}Organization<br />{/if}
                {if $row.custom_95 or $row.custom_96 or $row.custom_97}Location:{/if}
            </td>
            <td>
                <strong>{$row.display_name}</strong><br />
                {$row.custom_89}<br/>
                {if $row.custom_91}{$row.custom_91}<br />{/if}
                {if $row.custom_95 or $row.custom_96 or $row.custom_97}
                    {$row.custom_95}, {$row.custom_96}, {$row.custom_97}
                {/if}
            </td>
        </tr>
    {/foreach}
    </table>
{/if}

{include file="CRM/common/showHide.tpl"}

{literal} 
<script type="text/javascript">
form = document.Zandigo;
setFields( );

// Called by onClick of searchFor radio
function showHideZ ( elem ) {
    // unset other radio field 
    var unsetFld = '';
    if ( elem.name == 'custom_89' ) {
        unsetFld = 'custom_90';
    }
    if ( elem.name == 'custom_90' ) {
        unsetFld = 'custom_89';
    }
    unselectRadio( unsetFld, form.name );
    setFields();
}

function setFields ( ) {
    var searchFor = '';
    for( i=0; i < form.elements.length; i++) {
        if (form.elements[i].type == 'radio' && form.elements[i].checked == true) {
            // which radio button is checked
            searchFor = form.elements[i].value; 
            
            // is this a student or counselor (type 1), other person (type 2), or organization search
            var sType = searchType( searchFor );
             
            // show and hide flds 
            var hideRows = new Array();
            var showRows = new Array();
            switch (sType) 	{
                case 1 :
                    showRows = ['id-people-title','id-first-name','id-middle-name','id-last-name','id-gender','id-email','id-custom_91','id-custom_92','id-custom_93','id-custom_94','id-custom_95','id-custom_96','id-custom_97'];
                    hideRows = ['id-org-title','id-org-name'];
                    break;
                case 2 :
                    showRows = ['id-people-title','id-first-name','id-middle-name' ,'id-last-name', 'id-gender', 'id-email'];
                    hideRows = ['id-org-title','id-org-name','id-custom_91','id-custom_92','id-custom_93','id-custom_94','id-custom_95','id-custom_96','id-custom_97'];
                    break;
                case 3 :
                    showRows = ['id-org-title','id-org-name'];
                    hideRows = ['id-people-title','id-first-name','id-middle-name' ,'id-last-name', 'id-gender', 'id-email','id-custom_91','id-custom_92','id-custom_93','id-custom_94','id-custom_95','id-custom_96','id-custom_97'];
                    break;
            }
            for( j=0; j < hideRows.length; j++ ){
                hide( hideRows[j], 'table-row' );
            }
            for( j=0; j < showRows.length; j++ ){
                show( showRows[j], 'table-row' );
            }
            return;
        }
    }
}

function searchType ( searchFor ) {
    var sType1 = ['Student', 'Guidance Counselor'];
    for( i=0; i < sType1.length; i++) {
        if ( sType1[i] == searchFor ) {
            return 1;
        }
    }
    var sType2 = ['Admissions Officer', 'Parent', 'Non Profit Director', 'College Access Director'];
    for( i=0; i < sType2.length; i++) {
        if ( sType2[i] == searchFor ) {
            return 2;
        }
    }
    var sType3 = ['High School', 'College', 'Organization', 'College Access Program'];
    for( i=0; i < sType3.length; i++) {
        if ( sType3[i] == searchFor ) {
            return 3;
        }
    }

}

</script>
{/literal}

