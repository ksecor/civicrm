{* Quest Counselor Recommendation: Academic Record  section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
{strip}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel" width="30%" valign="top">
        <label for="app_class_1">{ts}Please list all of the Advanced Placement and International Baccalaureate classes your school offers.{/ts}</label></td>
    <td>
        <table width="100%"  border="0" class="fieldlabel">
            <tr><td class="optionlist">{$form.ap_class_1.html}</td><td class="optionlist">{$form.ap_class_2.html}</td></tr>
            <tr><td class="optionlist">{$form.ap_class_3.html}</td><td class="optionlist">{$form.ap_class_4.html}</td></tr>
            <tr><td class="optionlist">{$form.ap_class_5.html}</td><td class="optionlist">{$form.ap_class_6.html}</td></tr>
        </table>
    </td>
</tr>
<tr>
    <td class="grouplabel">{$form.gpa_unweighted.label}</td>
    <td class="fieldlabel">{$form.gpa_unweighted.html} Enter GPA in format "X.xx". Do not enter any spaces.</td>
</tr>
<tr>
    <td class="grouplabel">{$form.gpa_weighted.label}</td>
    <td class="fieldlabel">{$form.gpa_weighted.html} Enter GPA in format "X.xx". Do not enter any spaces.</td>
</tr>
<tr>
    <td class="grouplabel">{$form.gpa_includes_id.label}</td>
    <td class="fieldlabel">{$form.gpa_includes_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.gpa_weighted_max.label}</td>
    <td class="fieldlabel">{$form.gpa_weighted_max.html} Enter GPA in format "X.xx". Do not enter any spaces.</td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        <label for="numeric_grade_a">{ts}What are the numerical values that correspond to your alphabetical grading system?{/ts}</label></td>
    <td>
        <table width="100%" border="0" class="fieldlabel">
            <tr><td class="optionlist">A = {$form.numeric_grade_a.html}</td></tr>
            <tr><td class="optionlist">B = {$form.numeric_grade_b.html}</td></tr>
            <tr><td class="optionlist">C = {$form.numeric_grade_c.html}</td></tr>
            <tr><td class="optionlist">D = {$form.numeric_grade_d.html}</td></tr>
        </table>
    </td>
</tr>
<tr>
    <td class="grouplabel">{$form.unweighted_rank.label}</td>
    <td class="fieldlabel">{$form.unweighted_rank.html} {$form.class_num_students.label} {$form.class_num_students.html}</td>
</tr>
<tr>
    <td class="grouplabel">The cumulative rank and student GPA cover a period from</td>
    <td class="fieldlabel">From: {$form.rank_date_low.html} &nbsp; To: {$form.rank_date_high.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.term_type_id.label}</td>
    <td class="fieldlabel">{$form.term_type_id.html}
        <div id="term_type_other">{$form.term_type_other.html}<br />Please describe block system.</div>
    </td>
</tr>
<tr>
    <td class="grouplabel">{$form.share_ranking.label}</td>
    <td class="fieldlabel">{$form.share_ranking.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.course_choice_id.label}</td>
    <td class="fieldlabel">{$form.course_choice_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.college_four_year.label}</td>
    <td class="fieldlabel">{$form.college_four_year.html} %<br />
        Please round up (with .5 or above) or down (if between .0 and .49) to the closest integer.</td>
</tr>
<tr>
    <td class="grouplabel">{$form.college_two_year.label}</td>
    <td class="fieldlabel">{$form.college_two_year.html} %<br />
        Please round up (with .5 or above) or down (if between .0 and .49) to the closest integer.</td>
</tr>
</table>
{/strip}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="term_type_id"
    trigger_value       ="4"
    target_element_id   ="term_type_other"
    target_element_type =""
    field_type          ="select"
    invert              = 0
}
