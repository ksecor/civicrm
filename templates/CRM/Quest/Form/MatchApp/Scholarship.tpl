{* Quest Pre-application: Scholarship Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
     <td class="grouplabel"> {$form.internet_access_id.label} </td>
     <td class="fieldlabel">{$form.internet_access_id.html}</td>
</tr> 
<tr id="internet_access_other">
     <td class="grouplabel">&nbsp;</td>
     <td class="fieldlabel">{$form.internet_access_other.html}<br />
        {ts}{edit}Describe your primary internet access method.{/edit}{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_home_computer.label}</td>
    <td class="fieldlabel"> {$form.is_home_computer.html} </td>
</tr> 
<tr>
    <td class="grouplabel"> {$form.is_home_internet.label} </td>
    <td class="fieldlabel"> {$form.is_home_internet.html} </td>
</tr> 
<tr>
    <td class="grouplabel"> {$form.fed_lunch_id.label}</td>
    <td class="fieldlabel"> {$form.fed_lunch_id.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_take_SAT_ACT.label}</td>
    <td class="fieldlabel"> {$form.is_take_SAT_ACT.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.study_method_id.label}</td>
    <td class="fieldlabel"> {$form.study_method_id.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.financial_aid_applicant.label}</td>
    <td class="fieldlabel"> {$form.financial_aid_applicant.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.register_standarized_tests.label}</td>
    <td class="fieldlabel"> {$form.register_standarized_tests.html} </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.displacement.label}</td>
    <td class="fieldlabel"> {$form.displacement.html} </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.heard_about_qb_id.label}</td>
    <td class="fieldlabel"> {$form.heard_about_qb_id.html} </td>
</tr>

<tr>
    <td class="grouplabel"> {ts}Please enter contact information for current juniors you think would be strong applicants for next year.{/ts}</td>
    <td class="fieldlabel" nowrap> 
            {$form.sophomores_name_1.label}&nbsp;{$form.sophomores_name_1.html}&nbsp;&nbsp;&nbsp;{$form.sophomores_email_1.label}&nbsp;{$form.sophomores_email_1.html}&nbsp;<br/>
            {$form.sophomores_name_2.label}&nbsp;{$form.sophomores_name_2.html}&nbsp;&nbsp;&nbsp;{$form.sophomores_email_2.label}&nbsp;{$form.sophomores_email_2.html}&nbsp;<br/>
            {$form.sophomores_name_3.label}&nbsp;{$form.sophomores_name_3.html}&nbsp;&nbsp;&nbsp;{$form.sophomores_email_3.label}&nbsp;{$form.sophomores_email_3.html}&nbsp;<br/>
      {ts}We have found that fellow students are the best source of information in identifying deserving applicants<BR>for next year's application cycle. Enter 3 fellow student's full name and email address.{/ts}
    </td>
</tr>


<tr>

{ts}For any of your relatives who are alumni/ae at any of our partner colleges/universities, please list their names, relationship to you and years of graduation, if known:{/ts}
    <td class="fieldlabel" nowrap> 
       

<tr>
    <td class="grouplabel"> {$form.partner_institute_1.label}</td>
    <td class="grouplabel"> {$form.last_name_1.label}</td>
    <td class="grouplabel"> {$form.first_name_1.label}</td>
    <td class="grouplabel"> {$form.class_year_1.label}</td>
    <td class="grouplabel"> {$form.relationship_1.label}</td>
</tr>
    for(i=1;i<=6;i++) {*
   
    {$form.partner_institute_1.html}
    {$form.last_name_1.html}
    {$form.first_name_1.html}
    {$form.class_year_1.html}
    {$form.relationship_1.label}



    *}




<tr>
    <td class="grouplabel"> {$form.partner_institute_1.label}</td>
    <td class="grouplabel"> {$form.last_name_1.label}</td>
    <td class="grouplabel"> {$form.first_name_1.label}</td>
    <td class="grouplabel"> {$form.class_year_1.label}</td>
    <td class="grouplabel"> {$form.relationship_1.label}</td>
</tr>
    for(i=1;i<=6;i++) {*
   
    {$form.partner_institute_1.html}
    {$form.last_name_1.html}>
    {$form.first_name_1.html}
    {$form.class_year_1.html}
    {$form.relationship_1.label}

*}

    

    </td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}


{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="internet_access_id"
    trigger_value       ="23"
    target_element_id   ="internet_access_other" 
    target_element_type =""
    field_type          ="select"
    invert              = 0
}
