{* Quest College Match Application: High School Information section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}We realize our applicants come from a diverse group of secondary schools. Please tell us about your current school by answering the following questions. If you attended a different secondary school prior to yor current school, please enter information about that school as well by clicking <strong>Add another High School</strong> below.{/ts}</p></td>
</tr>
</table>

{section name=rowLoop start=1 loop=$max}
{assign var=i value=$smarty.section.rowLoop.index}
{capture assign=searchUrl}{crmURL p='civicrm/quest/schoolsearch' q="reset=1&schoolIndex=`$i`"}{/capture}
<div id="id_HighSchool_{$i}">
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td class="grouplabel">
        <input type="button" value="Find Your School" class="form-submit" onclick="openSearchPopup('{$searchUrl}'); return false;" title="Find your High School. This link will create a new window or will re-use an already opened one with a school search form." id="btn-school-search">
    </td>
    <td class="grouplabel">
        <strong>Click Find Your School to open a new window with a School Search form. You will be able to search by school name, city, state and/or zip code - and your school information will be automatically filled in for you. IMPORTANT: Please make sure that pop-up windows are NOT blocked for this site.</p>
    </td>
</tr>
<tr>
    {assign var=organization_name value="organization_name_"|cat:$i}
    <td class="grouplabel">{$form.$organization_name.label}</td>
    <td class="fieldlabel">{$form.$organization_name.html}</td>
</tr>
<tr>
    {assign var=custom_1 value="custom_1_"|cat:$i}
    <td class="grouplabel">{$form.$custom_1.label}</td>
    <td class="fieldlabel">
        {$form.$custom_1.html|crmReplace:class:six}<br />
        <a href="javascript:popUp('http://questscholars.stanford.edu/help_popup/ceeb.html')">{ts}{edit}Click here</a> to locate your CEEB school code.{/edit}{/ts}
    </td>
</tr>
<tr>
    {assign var=location value="location_"|cat:$i}
    <td class="grouplabel" rowspan="5"><label>{ts}School Address{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.$location.1.address.street_address.html}<br />
         {ts}{edit}Number and Street (including apartment number){/edit}{/ts}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.$location.1.address.city.html}<br/>{ts}{edit}City{/edit}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.$location.1.address.state_province_id.html}<br/>{ts}{edit}State or Province{/edit}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.$location.1.address.postal_code.html} - {$form.$location.1.address.postal_code_suffix.html}<br />
        {ts}{edit}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/edit}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.$location.1.address.country_id.html}<br />{ts}{edit}Country{/edit}{/ts}</td>
</tr>
<tr>
    {assign var=custom_2 value="custom_2_"|cat:$i}
    <td class="grouplabel">{$form.$custom_2.label}</td>
    <td class="fieldlabel">{$form.$custom_2.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.$location.1.phone.1.phone.label}</td>
    <td class="fieldlabel">{$form.$location.1.phone.1.phone.html}</td>
</tr>
<tr>
    {assign var=date_of_entry value="date_of_entry_"|cat:$i}
    {assign var=date_of_exit value="date_of_exit_"|cat:$i}
    <td class="grouplabel">{$form.$date_of_entry.label}</td>
    <td class="fieldlabel">{$form.$date_of_entry.html} &nbsp;&nbsp; <label>To</label> {$form.$date_of_exit.html} </td>
</tr>
<tr>
    {assign var=custom_3 value="custom_3_"|cat:$i}       
    <td class="grouplabel">{$form.$custom_3.label}</td>
    <td class="fieldlabel"> {$form.$custom_3.html|crmReplace:class:four}</td>
</tr>
{if $i LT ($max-1)}
   {assign var=j value=$i+1}
    <tr>
        <td colspan=2>
        <span id="id_HighSchool_{$j}_show">
            {$highschool.$j.show}<br /> 
            {ts}If you attended another high school prior to the one above, click this link to enter information for your prior school.{/ts}
        </span>
        </td>
    </tr>
{/if}
</table>
</div>
{/section}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{literal}
<script type="text/javascript">
var WindowObjectReference = null; // holds school search pop-up window object

function openSearchPopup(searchUrl)
{
  if(WindowObjectReference == null || WindowObjectReference.closed)
  /* if the pointer to the window object in memory does not exist
     or if such pointer exists but the window was closed */

  {
    WindowObjectReference = window.open(searchUrl, "schoolSearch", "width=640,height=480,resizable=yes,scrollbars=yes,status=yes");
    /* then create it. The new window will be created and
       will be brought on top of any other window. */
  }
  else
  {
    WindowObjectReference.focus();
    /* else the window reference must exist and the window
       is not closed; therefore, we can bring it back on top of any other
       window with the focus() method. There would be no need to re-create
       the window or to reload the referenced resource. */
  };
}
</script>
{/literal}
