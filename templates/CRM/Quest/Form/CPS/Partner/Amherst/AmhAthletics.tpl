{* Quest College Match: Partner: Amherst: Athletics section *}
{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle} (Optional)</td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">If you are interested in varsity athletics at Amherst College, please complete this form. Below, please list, in order of importance, those sports in which you plan to compete in college. </td>
</tr>
{include file="CRM/Quest/Form/CPS/Partner/AthleticsGrid.tpl"}
<tr>
    <td class="grouplabel">{$form.height.label}</td>
    <td class="fieldlabel">{$form.height.html|crmReplace:class:four}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.weight.label}</td>
    <td class="fieldlabel">{$form.weight.html|crmReplace:class:four}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.essay.record.label}</td>
    <td class="fieldlabel">{$form.essay.record.html}</td> 
</tr>
<tr>
    <td class="grouplabel">{$form.essay.additional.label}</td>
    <td class="fieldlabel">{$form.essay.additional.html}</td> 
</tr>
</table>

{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="end"}
