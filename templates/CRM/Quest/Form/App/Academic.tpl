{* Quest Pre-application: Academic Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.gpa_id.label}</td>
    <td class="fieldlabel">{$form.gpa_id.html} <br/> Please give your GPA on an unweighted, 4.0 scale</td>
</tr> 
<tr>
    <td class="grouplabel">{$form.is_class_ranking.label}</td>
    <td class="fieldlabel">{$form.is_class_ranking.html}</td>
</tr> 
<tr id="class_rank">
    <td class="grouplabel">{$form.class_rank.label}</td>
    <td class="fieldlabel">{$form.class_rank.html}  {$form.class_num_students.html}<br/>Your rank   &nbsp;&nbsp;&nbsp;Total number students in your class</td>
</tr>
<tr>
    <td class="grouplabel">{$form.class_rank_percent_id.label}</td>
    <td class="fieldlabel">{$form.class_rank_percent_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.gpa_explanation.label}</td>
    <td class="fieldlabel">{$form.gpa_explanation.html}<br/> If there were any extenuating circumstances that affected your GPA, please describe them here.</td>
</tr>
<tr>
    <td colspan=2 id="category">{ts}Academic Honors{/ts}</td>
<tr>
<tr>
    <td colspan=2><p class="preapp-instruction">{ts}Describe any honors you have been awarded since you entered high school.{/ts}</p></td>
</tr>
<tr><td colspan=2>
{assign var=maxHonors value=7}
{section name=rowLoop start=1 loop=$maxHonors}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="honor_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
     <tr>
        {assign var=description value="description_"|cat:$i}
        <td class="grouplabel">{$form.$description.label}</td>
        <td class="fieldlabel" width="75%"> {$form.$description.html}<br />{ts}Honor title or description{/ts}</td>
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

