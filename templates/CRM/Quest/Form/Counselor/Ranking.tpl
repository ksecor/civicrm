{* Quest Counselor Recommendation: Student Ranking section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
{strip}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.counselor_years.label}</td>
    <td class="fieldlabel">{$form.counselor_years.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.counselor_capacity.label}</td>
    <td class="fieldlabel">{$form.counselor_capacity.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.counselor_meet.label}</td>
    <td class="fieldlabel">{$form.counselor_meet.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.counselor_basis.label}</td>
    <td class="fieldlabel">{$form.counselor_basis.html}
        <div id="cb_other">{$form.counselor_basis_other.html}</div>
    </td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">Based on your experience as a counselor, please comment on the student's characteristics as compared to other students you have encountered in your career: <span class="marker">*</span> <br />
    <br />
    <table width="100%" border="1">
      <tr>
        <td>&nbsp;</td>
        <td>Unable<br />to comment </td>
        <td>Below<br />Average</td>
        <td>Average</td>
        <td>Above<br />Average</td>
        <td>Very<br />good</td>
        <td>Excellent</td>
        <td>One of the best<br />of my career</td>
      </tr>
      <tr><td>Leadership Potential</td><td>{$form.leadership_id.html}</td></tr>
      <tr><td>Intellectual Curiosity</td><td>{$form.intellectual_id.html}</td></tr>
      <tr><td>Atttitude toward challenges</td><td>{$form.challenge_id.html}</td></tr>
      <tr><td>Emotional Maturity</td><td>{$form.maturity_id.html}</td></tr>
      <tr><td>Work ethic</td><td>{$form.work_ethic_id.html}</td></tr>
      <tr><td>Originality of thought</td><td>{$form.originality_id.html}</td></tr>
      <tr><td>Sense of humor</td><td>{$form.humor_id.html}</td></tr>
      <tr><td>Energy</td><td>{$form.energy_id.html}</td></tr>
      <tr><td>Respect for differences</td><td>{$form.respect_differences_id.html}</td></tr>
      <tr><td>Respect accorded by faculty</td><td>{$form.respect_faculty_id.html}</td></tr>
      <tr><td>Respect accorded by peers</td><td>{$form.respect_peers_id.html}</td></tr> 
    </table>
    </td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">
        {$form.essay.rating_reason.label}<br /><br />
        {$form.essay.rating_reason.html} &nbsp;<br /><br />
        {$form.word_count.rating_reason.label} &nbsp;&nbsp;{$form.word_count.rating_reason.html}</td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">How would you compare this student to his or her entire class: <span class="marker">*</span> <br />
    <br />
    <table width="100%" border="1">
      <tr>
        <td>&nbsp;</td>
        <td>Unable<br />to comment </td>
        <td>Below<br />Average</td>
        <td>Average</td>
        <td>Above<br />Average</td>
        <td>Very<br />good</td>
        <td>Excellent</td>
        <td>One of the best<br />of my career</td>
      </tr>
      <tr><td>Academically</td><td>{$form.compare_academic_id.html}</td></tr>
      <tr><td>Extracurricular accomplishment</td><td>{$form.compare_extracurricular_id.html}</td></tr>
      <tr><td>Personal qualities and character</td><td>{$form.compare_personal_id.html}</td></tr>
      <tr><td>Overall</td><td>{$form.compare_overall_id.html}</td></tr>
    </table>
    </td>
</tr>
<tr>
    <td class="grouplabel">{$form.recommend_student_id.label}</td>
    <td class="fieldlabel">{$form.recommend_student_id.html}</td>
</tr>
</table>
{/strip}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="counselor_basis[5]"
    trigger_value       ="1"
    target_element_id   ="cb_other"
    target_element_type ="block"
    field_type          ="radio"
    invert              = 0
}
