{* Quest Pre-application: Sibling Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>

<tr>
    <td rowspan=3 valign=top class="grouplabel" width="30%"><label>{ts}Name{/ts}</label></td>
</tr>    
<tr>
     <td class="fieldlabel">{$form.first_name.html}<br/>
                            {$form.first_name.label}</td>
</tr> 
<tr>
    <td class="fieldlabel"> {$form.last_name.html}<br/>
                            {$form.last_name.label}</td>
</tr> 
<tr>
    <td class="grouplabel">{$form.sibling_relationship_id.label}</td>
    <td class="fieldlabel">{$form.sibling_relationship_id.html}</td>
</tr> 
<tr>
    <td class="grouplabel">{$form.age.label}</td>
    <td class="fieldlabel">{$form.age.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.lived_with_from_age.label}</td>
    <td class="fieldlabel">{$form.lived_with_from_age.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.lived_with_to_age.label}</td>
    <td class="fieldlabel">{$form.lived_with_to_age.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.current_school_level_id.label}</td>
    <td class="fieldlabel">{$form.current_school_level_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.college_name.label}</td>
    <td class="fieldlabel">{$form.college_name.html}</td>
<tr>    
    <td class="grouplabel">{$form.job_occupation.label}</td>
    <td class="fieldlabel">{$form.job_occupation.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.description.label}</td>
    <td class="fieldlabel">{$form.description.html}</td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
