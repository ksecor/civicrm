{* Quest Pre-application: Academic Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</td>
<tr>
    <td class="fieldlabel">{$form.gpa.label}</td>
    <td>{$form.gpa.html} <br/> Please give your GPA on an unweighted, 4.0 scale</td>
</tr> 
<tr>
    <td class="fieldlabel">{$form.is_class_ranking.label}</td>
    <td>{$form.is_class_ranking.html}</td>
</tr> 
<tr>
    <td class="fieldlabel">{$form.class_rank.label}</td>
    <td>{$form.class_rank.html}  {$form.class_num_students.html}<br/>Your rank   &nbsp;&nbsp;&nbsp;Total number students in your class</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.class_rank_percent.label}</td>
    <td>{$form.class_rank_percent.html}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.gpa_explanation.label}</td>
    <td>{$form.gpa_explanation.html}<br/> If there were any extenuating circumstances that affected your GPA, please describe them here.</td>
</tr>
<tr>
    <td colspan=2 id="category">{ts}Academic Honors{/ts}</td>
<tr>
<tr>
    <td colspan=2>{ts}Describe any honors you have been awarded since you entered high school.{/ts}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.description_1.label}</td>
    <td>{$form.description_1.html}<br/>honor</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.award_date_1.label}</td>
    <td>{$form.award_date_1.html}</td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

