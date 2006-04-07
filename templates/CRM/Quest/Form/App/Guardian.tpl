{* Quest Pre-application: Parent/Guardian Detail  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td rowspan=2 valign=top class="grouplabel" width="30%">
        <label>{ts}Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html}<br />
        {hlp}{$form.first_name.label}{/hlp}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
        {hlp}{$form.last_name.label}{/hlp}</td>
</tr> 
<tr>
    <td class="grouplabel">{$form.marital_status_id.label}</td>
    <td class="fieldlabel">{$form.marital_status_id.html}</td>
</tr>
<tr id="separated-year">
    <td class="grouplabel">{$form.separated_year.label}</td>
    <td class="fieldlabel">{$form.separated_year.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.is_deceased.label}</td>
    <td class="fieldlabel">{$form.is_deceased.html}</td>
</tr>
<tr id="deceased_year_date">
    <td class="grouplabel">{$form.deceased_year_date.label}</td>
    <td class="fieldlabel">{$form.deceased_year_date.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.birth_date.label} <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.birth_date.html}
{hlp}
     <div class="description"> 
        {include file="CRM/common/calendar/desc.tpl"}
     </div>
        {include file="CRM/common/calendar/body.tpl" dateVar=birth_date startDate=1905 endDate=currentYear}
{/hlp}
    </td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}How long have you lived with this person?{/ts}</label></td>
    <td>
        <table border="0">
          <tr><td class="grouplabel" colspan=2>{$form.all_life.label} {$form.all_life.html}</td></tr>
          <tr id="lived_with_from_age"><td class="grouplabel"><label>{$form.lived_with_from_age.label}</label></td><td width="80%">{$form.lived_with_from_age.html}</td></tr>
          <tr id="lived_with_to_age"><td class="grouplabel"><label>{$form.lived_with_to_age.label}</label></td><td  width="80%">{$form.lived_with_to_age.html}</td></tr>
        </table>
    </td>
</tr>
<tr>
    <td class="grouplabel">{$form.industry_id.label}</td>
    <td class="fieldlabel">{$form.industry_id.html}</td>
</tr>
<tr id="job_organization">
    <td class="grouplabel">{$form.job_organization.label}</td>
    <td class="fieldlabel">{$form.job_organization.html}</td>
</tr>
<tr id="job_occupation">
    <td class="grouplabel">{$form.job_occupation.label}</td>
    <td class="fieldlabel">{$form.job_occupation.html}</td>
</tr>
<tr id="job_current_years">
    <td class="grouplabel">{$form.job_current_years.label}</td>
    <td class="fieldlabel">{$form.job_current_years.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.highest_school_level_id.label}</td>
    <td class="fieldlabel">{$form.highest_school_level_id.html}</td>
</tr>
<tr id="college_name">
    <td class="grouplabel">{$form.college_name.label}</td>
    <td class="fieldlabel">{$form.college_name.html}</td>
</tr>
<tr id="college_country">
    <td class="grouplabel">{$form.college_country_id.label}</td>
    <td class="fieldlabel">{$form.college_country_id.html}</td>
</tr>
<tr id="college_grad_year">
    <td class="grouplabel">{$form.college_grad_year.label}</td>
    <td class="fieldlabel">{$form.college_grad_year.html}</td>
</tr>
<tr id="college_major">
    <td class="grouplabel">{$form.college_major.label}</td>
    <td class="fieldlabel">{$form.college_major.html}</td>
</tr>
<tr id="prof_school_name">
    <td class="grouplabel">{$form.prof_school_name.label}</td>
    <td class="fieldlabel">{$form.prof_school_name.html}</td>
</tr>
<tr id="prof_school_degree">
    <td class="grouplabel">{$form.prof_school_degree_id.label}</td>
    <td class="fieldlabel">{$form.prof_school_degree_id.html}</td>
</tr>
<tr id="prof_grad_year">
    <td class="grouplabel">{$form.prof_grad_year.label}</td>
    <td class="fieldlabel">{$form.prof_grad_year.html}</td>
</tr>

<tr>
    <td class="grouplabel">{$form.description.label}</td>
    <td class="fieldlabel">{$form.description.html}</td>
</tr>
</table>

{* Include Javascript to show/hide fields based on value of other fields. *}
{* Marital status field *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="marital_status_id"
    trigger_value       ="42,43,44"
    target_element_id   ="separated-year" 
    target_element_type =""
    field_type          ="select"
    invert              = 0
}
{* Deceased field *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_deceased"
    trigger_value       ="1"
    target_element_id   ="deceased_year_date" 
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
{* Industry field *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="industry_id"
    trigger_value       ="47"
    target_element_id   ="job_organization|job_occupation|job_current_years" 
    target_element_type =""
    field_type          ="select"
    invert              = 1
}

{* Highest school completed field. College values, then grad school value. *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="highest_school_level_id"
    trigger_value       ="118|119|120|121|122"
    target_element_id   ="college_name|college_country|college_grad_year|college_major" 
    target_element_type =""
    field_type          ="select"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="highest_school_level_id"
    trigger_value       ="122"
    target_element_id   ="prof_school_name|prof_school_degree|prof_grad_year" 
    target_element_type =""
    field_type          ="select"
    invert              = 0
}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
