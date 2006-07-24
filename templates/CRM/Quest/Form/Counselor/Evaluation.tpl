{* Quest Counselor Recommendation: Personal Information section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.essay.adjectives.label}<br />
        {$form.essay.adjectives.html} &nbsp;<br /><br />
        {$form.word_count.adjectives.label} &nbsp;&nbsp;{$form.word_count.adjectives.html}
    </td> 

</tr>
<tr>
    <td class="grouplabel">
        {$form.is_context.label}</td> 
    <td class="grouplabel">
 	{$form.is_context.html}</td> 
</tr>
<tr id="explain_context_row">
    <td class="grouplabel" colspan=2>
        {$form.essay.explain_context.label}<br />
        {$form.essay.explain_context.html} &nbsp;<br /><br />
        {$form.word_count.explain_context.label} &nbsp;&nbsp;{$form.word_count.explain_context.html}
    </td> 

</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.essay.distinguish_performance.label}<br />
        {$form.essay.distinguish_performance.html} &nbsp;<br /><br />
        {$form.word_count.distinguish_performance.label} &nbsp;&nbsp;{$form.word_count.distinguish_performance.html}
    </td> 

</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.essay.like_best.label}<br />
        {$form.essay.like_best.html} &nbsp;<br /><br />
        {$form.word_count.like_best.label} &nbsp;&nbsp;{$form.word_count.like_best.html}
    </td> 

</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.essay.interfere_factors.label}<br />
        {$form.essay.interfere_factors.html} &nbsp;<br /><br />
        {$form.word_count.interfere_factors.label} &nbsp;&nbsp;{$form.word_count.interfere_factors.html}
    </td> 

</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.essay.succeed_ability.label}<br />
        {$form.essay.succeed_ability.html} &nbsp;<br /><br />
        {$form.word_count.succeed_ability.label} &nbsp;&nbsp;{$form.word_count.succeed_ability.html}
    </td> 

</tr>
<tr>
    <td class="grouplabel">
        {$form.is_problem_behavior.label}</td> 
    <td class="grouplabel">
 	{$form.is_problem_behavior.html}</td> 
</tr>
<tr id="explain_problem_row">
    <td class="grouplabel" colspan=2>
        {$form.essay.problem_explain.label}<br />
        {$form.essay.problem_explain.html} &nbsp;<br /><br />
        {$form.word_count.problem_explain.label} &nbsp;&nbsp;{$form.word_count.problem_explain.html}
    </td> 

</tr>
<tr>
    <td class="grouplabel">
        {$form.is_disciplinary_action.label}</td> 
    <td class="grouplabel">
 	{$form.is_disciplinary_action.html}</td> 
</tr>
<tr id="explain_discipline_row">
    <td class="grouplabel" colspan=2>
        {$form.essay.discipline_explain.label}<br />
        {$form.essay.discipline_explain.html} &nbsp;<br /><br />
        {$form.word_count.discipline_explain.label} &nbsp;&nbsp;{$form.word_count.discipline_explain.html}
    </td> 

</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="is_context"
    trigger_value       ="1"
    target_element_id   ="explain_context_row"
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="is_problem_behavior"
    trigger_value       ="1"
    target_element_id   ="explain_problem_row"
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="is_disciplinary_action"
    trigger_value       ="1"
    target_element_id   ="explain_discipline_row"
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
