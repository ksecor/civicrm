{* Quest Counselor Recommendation: Personal Information section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.word_1.label}<br />
        {$form.word_1.html} &nbsp;{$form.word_2.html}&nbsp;{$form.word_3.html}
     </td> 
</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.essay.strength.label}<br />
        {$form.essay.strength.html} 
     </td> 
</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.essay.weakness.label}<br />
        {$form.essay.weakness.html}
     </td> 
</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.success_factor.label}<br/><br/>
	<table>
	   <tr>
	    <td class="optionlist">{$form.success_factor.1.html}</td>
	    <td class="optionlist">{$form.success_factor.2.html}</td>
           </tr>
	   <tr>
	    <td class="optionlist">{$form.success_factor.3.html}</td>
	    <td class="optionlist">{$form.success_factor.4.html}</td>
	   </tr>
           <tr>
	    <td class="optionlist">{$form.success_factor.5.html}</td>
	    <td class="optionlist">{$form.success_factor.6.html}</td>
	   </tr>
	</table>
        <br/>{$form.essay.success_explain.html} 
    </td> 
</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.is_obstacles.label}<br />
        {$form.is_obstacles.html} 
     </td> 
</tr>
<tr id = "obstacle_explain">
    <td class="grouplabel" colspan=2>
        {$form.essay.obstacle_explain.label}<br />
        {$form.essay.obstacle_explain.html} &nbsp;<br /><br />
     </td> 
</tr>
<tr>
    <td class="grouplabel" colspan=2>
        {$form.is_interfere.label}<br />
        {$form.is_interfere.html} 
     </td> 
<tr id ="interfere_explain">
    <td class="grouplabel" colspan=2>
        {$form.essay.interfere_explain.label}<br />
        {$form.essay.interfere_explain.html} &nbsp;<br /><br />
     </td> 
</tr>
</table>

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="is_obstacles"
    trigger_value       ="1"
    target_element_id   ="obstacle_explain"
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="is_interfere"
    trigger_value       ="1"
    target_element_id   ="interfere_explain"
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
