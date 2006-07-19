{* Quest Pre-application: Academic Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.gpa_id.label}</td>
    <td class="fieldlabel">{$form.gpa_id.html} <br/>{ts} {edit}Please give your GPA on an unweighted, 4.0 scale{/edit}. View instructions for <A HREF="http://www.asfdn.org/content.cfm?page=scholarships_programs_GPA2" TARGET="_blank">calculating an unweighted score</A>{/ts}</td>
</tr> 
<tr>
    <td class="grouplabel">{$form.is_class_ranking.label}</td>
    <td class="fieldlabel">{$form.is_class_ranking.html}</td>
</tr> 
<tr id="class_rank">
    <td class="grouplabel">{$form.class_rank.label}</td>
    <td class="fieldlabel">{$form.class_rank.html}  {$form.class_num_students.html}<br/>{ts}{edit}Your rank   &nbsp;&nbsp;&nbsp;Total number students in your class{/edit}{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.class_rank_percent_id.label}</td>
    <td class="fieldlabel">{$form.class_rank_percent_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.gpa_explanation.label}</td>
    <td class="fieldlabel">{$form.gpa_explanation.html}</td>
</tr>
<tr>
    <td colspan=2 id="category">{ts}{edit}Academic Honors{/edit}{/ts}</td>
<tr>
<tr>
    <td colspan=2><p class="preapp-instruction">{ts}Describe the top 6 honors you have been awarded since you entered high school.{/ts}</p></td>
</tr>
<tr><td colspan=2>

{section name=rowLoop start=1 loop=$maxHonors}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="honor_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
     <tr>
        {assign var=description value="description_"|cat:$i}
        <td class="grouplabel">{$form.$description.label}</td>
        <td class="fieldlabel" width="75%"> {$form.$description.html}<br />{ts}{edit}Honor title or description{/edit}{/ts}</td>
    </tr>
    <tr>
        {assign var=award_date value="award_date_"|cat:$i}
        <td class="grouplabel">{$form.$award_date.label}</td>
        <td class="fieldlabel">
            {$form.$award_date.html}
            {if $i LT $maxHonors}
                {assign var=j value=$i+1}
                <br /><span id="honor_{$j}[show]">{$honor.$j.show}</span>
            {/if}
        </td>
    </tr>
    </table>
    </div>
{/section}
</td></tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_class_ranking"
    trigger_value       ="1"
    target_element_id   ="class_rank" 
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
