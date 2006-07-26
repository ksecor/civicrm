{* Quest College Match: Partner: Bowdoin: Athletics section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle} (Optional)</td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">If you are interested in varsity athletics at Bowdoin College, please complete this form. Below, please list, in order of importance, those sports in which you plan to compete in college. </td>
</tr>
{include file="CRM/Quest/Form/MatchApp/Partner/AthleticsGrid.tpl"}
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
    <td class="grouplabel">{$form.essay.other.label}</td>
    <td class="fieldlabel">{$form.essay.other.html}</td> 
</tr>
<tr>
    <td class="grouplabel">{$form.essay.additional.label}</td>
    <td class="fieldlabel">{$form.essay.additional.html}</td> 
</tr>
<tr><td colspan="2" class="grouplabel">&nbsp;</td></tr>
<tr >
    <td colspan="2" class="grouplabel">Bowdoin's coaches encourage you to contact them directly by phone at (207) 725-3326 or use the following Web directory to e-mail them: <a href="http://www.bowdoin.edu/athletics/contact_name.shtml">www.bowdoin.edu/athletics/contact_name.shtml</a>.</td>
</tr></table>

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
