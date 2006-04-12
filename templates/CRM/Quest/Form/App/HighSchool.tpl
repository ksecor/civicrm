{* Quest Pre-application: High School Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

{section name=rowLoop start=1 loop=$max}
{assign var=i value=$smarty.section.rowLoop.index}

<div id="HighSchool_{$i}">
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
{if $i EQ 1}
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}We realize our applicants come from a diverse group of secondary schools. Please tell us about your particular school by answering the following questions.{/ts}</p></td>
</tr>
{/if}
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
        <a href="javascript:popUp('http://questscholars.stanford.edu/help_popup/ceeb.html')">{ts}{hlp}Click here</a> to locate your CEEB school code.{/hlp}{/ts}
    </td>
</tr>
<tr>
    {assign var=date_of_entry value="date_of_entry_"|cat:$i}
    {assign var=date_of_exit value="date_of_exit_"|cat:$i}
    <td class="grouplabel">{$form.$date_of_entry.label}</td>
    <td class="fieldlabel">{$form.$date_of_entry.html} &nbsp;&nbsp; <label>To</label> {$form.$date_of_exit.html} </td>
</tr>
<tr>
    {assign var=location value="location_"|cat:$i}
    <td class="grouplabel" rowspan="5"><label>{ts}School Address{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.$location.1.address.street_address.html}<br />
         {ts}{hlp}Number and Street (including apartment number){/hlp}{/ts}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.$location.1.address.city.html}<br/>{ts}{hlp}City{/hlp}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.$location.1.address.state_province_id.html}<br/>{ts}{hlp}State or Province{/hlp}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.$location.1.address.postal_code.html} - {$form.$location.1.address.postal_code_suffix.html}<br />
        {ts}{hlp}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/hlp}{/ts}</td>
</tr>
<tr>
   <td class="fieldlabel">{$form.$location.1.address.country_id.html}<br />{ts}{hlp}Country{/hlp}{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.$location.1.phone.1.phone.label}</td>
    <td class="fieldlabel">{$form.$location.1.phone.1.phone.html}</td>
</tr>
<tr>
    {assign var=custom_2 value="custom_2_"|cat:$i}
    <td class="grouplabel">{$form.$custom_2.label}</td>
    <td class="fieldlabel">{$form.$custom_2.html}</td>
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
        <span id="HighSchool_{$j}[show]">
            {$highschool.$j.show}<br /> 
            {ts}If you attended another high school prior to the one above, click this link to enter information for your prior school.{/ts}
        </span>
        </td>
    </tr>
{/if}
</table>
</div>
{/section}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

