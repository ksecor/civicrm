{* Quest Counselor Recommendation: Personal Information section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td rowspan=4 valign=top class="grouplabel" width="30%">
        <label for="first_name">{ts}Legal Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html}<br />
        {edit}{$form.first_name.label}{/edit}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.middle_name.html}<br />
        {edit}{$form.middle_name.label}{/edit}</td>
</tr> 
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
	{edit}{$form.last_name.label}{/edit}</td>
</tr> 
<tr>
    <td class="fieldlabel">
        {$form.suffix_id.html}<br />
        {edit}{$form.suffix_id.label}{/edit}</td>
</tr> 
<tr>
    <td class="grouplabel">
        <label>{ts}Telephone{/ts} <span class="marker">*</span></td>
    <td class="fieldlabel">
        {$form.location.1.phone.1.phone.html}<br />
        {ts}{edit}Area Code and Number. Include extension, if applicable. Include country code, if not US or Canada.{/edit}{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.location.1.email.1.email.label}<br />
    <td class="fieldlabel">
        {$form.location.1.email.1.email.html}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.department.label}</td>
    <td class="fieldlabel">
        {$form.department.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.recommender_relationship_id.label}</td>
    <td class="fieldlabel">
        {$form.recommender_relationship_id.html}</td>
</tr>
<tr id="rec_rel_other">
    <td class="grouplabel">
        {ts}Please specify.{/ts}</td>
    <td class="fieldlabel">
        {$form.recommender_relationship_other.html}</td>
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="recommender_relationship_id"
    trigger_value       ="6"
    target_element_id   ="rec_rel_other"
    target_element_type ="table-row"
    field_type          ="select"
    invert              = 0
}
