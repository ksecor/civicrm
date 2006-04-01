{* Quest Pre-application: Sibling Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
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
    <td class="grouplabel">{$form.birth_date.label} <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.birth_date.html}
     <div class="description"> 
        {include file="CRM/common/calendar/desc.tpl"}
     </div>
        {include file="CRM/common/calendar/body.tpl" dateVar=birth_date startDate=1905 endDate=currentYear}
    </td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}How long have you lived with this person?{/ts}</label></td>
    <td>
        <table border="0">
         <tr><td class="grouplabel" colspan=2>{$form.all_life.label} {$form.all_life.html}</td></tr>
        <tr><td class="grouplabel"><label>{$form.lived_with_from_age.label}</label></td><td width="80%">{$form.lived_with_from_age.html}</td></tr>
        <tr><td class="grouplabel"><label>{$form.lived_with_to_age.label}</label></td><td>{$form.lived_with_to_age.html}</td></tr>
        </table>
    </td>
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
    <td class="fieldlabel">
        {$form.description.html}<br />
        {ts}If important information regarding your relationship with this sibling is not captured above, please enter it here.{/ts}
    </td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
