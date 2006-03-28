{* Quest Pre-application: Parent/Guardian Detail  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</td>
<tr>
    <td rowspan=2 valign=top class="grouplabel" width="30%">
        <label>{ts}Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html}<br />
        {$form.first_name.label}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
        {$form.last_name.label}</td>
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
<tr>
    <td class="grouplabel">{$form.deceased_year_date.label}</td>
    <td class="fieldlabel">{$form.deceased_year_date.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.age.label}</td>
    <td class="fieldlabel">{$form.age.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.lived_with_period_id.label}</td>
    <td class="fieldlabel">{$form.lived_with_period_id.html}&nbsp;&nbsp;&nbsp;
    {$form.lived_with_from_age.label}
    {$form.lived_with_from_age.html}
    {$form.lived_with_to_age.label}
    {$form.lived_with_to_age.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.industry_id.label}</td>
    <td class="fieldlabel">{$form.industry_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.job_organization.label}</td>
    <td class="fieldlabel">{$form.job_organization.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.job_occupation.label}</td>
    <td class="fieldlabel">{$form.job_occupation.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.job_current_years.label}</td>
    <td class="fieldlabel">{$form.job_current_years.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.highest_school_level_id.label}</td>
    <td class="fieldlabel">{$form.highest_school_level_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.college_name.label}</td>
    <td class="fieldlabel">{$form.college_name.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.college_country_id.label}</td>
    <td class="fieldlabel">{$form.college_country_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.college_grad_year.label}</td>
    <td class="fieldlabel">{$form.college_grad_year.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.college_major.label}</td>
    <td class="fieldlabel">{$form.college_major.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.prof_school_name.label}</td>
    <td class="fieldlabel">{$form.prof_school_name.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.prof_school_degree_id.label}</td>
    <td class="fieldlabel">{$form.prof_school_degree_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.prof_grad_year.label}</td>
    <td class="fieldlabel">{$form.prof_grad_year.html}</td>
</tr>

<tr>
    <td class="grouplabel">{$form.description.label}</td>
    <td class="fieldlabel">{$form.description.html}</td>
</tr>
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

{* Include Javascript to hide and display the appropriate blocks as directed by the php code *}
{include file="CRM/common/showHide.tpl"}

{literal}
    <script type="text/javascript">
    var selectedOption; 
    selectedOption = document.getElementById('marital_status_id').options[document.getElementById('marital_status_id').selectedIndex].text;
	if (selectedOption == 'Other') {
	   show('internet-access-other','table-row');
	} else {
	   hide('internet-access-other','table-row');
	}	
	
   	function showTextField() {
        selectedOption = document.getElementById('marital_status_id').options[document.getElementById('marital_status_id').selectedIndex].text;	
		if (selectedOption == 'Other') {
		   show('internet-access-other','table-row');
		} else {
		   hide('internet-access-other','table-row');
		}	
 	}
    </script>  
{/literal}
