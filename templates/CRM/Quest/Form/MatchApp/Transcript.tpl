{* Quest Pre-application: Transcript Information section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">{ts}Grading System{/ts}<br /><br />
        {$form.is_alternate_grading.label}</td>
    <td class="fieldlabel" width="50%">{$form.is_alternate_grading.html}</td>
</tr> 
<tr id="alternate_grading_explanation">
    <td class="grouplabel">{$form.alternate_grading_explanation.label}</td>
    <td class="fieldlabel">{$form.alternate_grading_explanation.html}</td>
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_alternate_grading"
    trigger_value       ="1"
    target_element_id   ="alternate_grading_explanation" 
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
