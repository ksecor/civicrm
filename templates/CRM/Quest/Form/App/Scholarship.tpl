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
        {ts}{hlp}Describe your primary internet access method.{/hlp}{/ts}
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
    <td class="fieldlabel"> {$form.is_take_SAT_ACT.html} </td>
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
    <td class="grouplabel"> {$form.award_ranking_1_id.label}</td>
    <td class="fieldlabel"> {$form.award_ranking_1_id.html}<br/>
    {$form.award_ranking_2_id.html}<br/>
    {$form.award_ranking_3_id.html}<br/>
    {ts}{hlp}Rank the top 3 awards you are interested in receiving, if you are awarded the scholarship.{/hlp}{/ts} 
    </td>
</tr>
<tr>
    <td class="grouplabel"> {ts}List 3 sophmores that would be ideal candidates for the QuestBridge 2007 application.{/ts}</td>
    <td class="fieldlabel"> {$form.sophomores_name_1.label} &nbsp;{$form.sophomores_name_1.html} &nbsp;
			    {$form.sophomores_email_1.label} &nbsp;{$form.sophomores_email_1.html} <br/>
			    {$form.sophomores_name_2.label} &nbsp;{$form.sophomores_name_2.html} &nbsp;
			    {$form.sophomores_email_2.label} &nbsp;{$form.sophomores_email_2.html} <br/>
			    {$form.sophomores_name_3.label} &nbsp;{$form.sophomores_name_3.html} &nbsp;
			    {$form.sophomores_email_3.label} &nbsp;{$form.sophomores_email_3.html} <br/>
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
