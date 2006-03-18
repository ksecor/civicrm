{* Quest Pre-application: Scholarship Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>
<tr>
     <td class="fieldlabel"> {$form.internet_access.label} </td>
     <td>{$form.internet_access.html}</td>
</tr> 
<tr>
    <td class="fieldlabel"> {$form.is_home_computer.label}</td>
    <td> {$form.is_home_computer.html} </td>
</tr> 
<tr>
    <td class="fieldlabel"> {$form.is_home_internet.label} </td>
    <td> {$form.is_home_internet.html} </td>
</tr> 
<tr>
    <td class="fieldlabel"> {$form.is_take_SAT_ACT.label}</td>
    <td> {$form.is_take_SAT_ACT.html} </td>
</tr>
<tr>
    <td class="fieldlabel"> {$form.study_method_id.label}</td>
    <td>{$form.study_method_id.html}</td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

