{* Quest Application: Transcript Sections (Grades 9 - 12 and Summer School *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="8" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.term_system_id.label}</td>
    <td class="fieldlabel" colspan="7">{$form.term_system_id.html} <br/>{ts 1=$wizard.currentStepTitle} {edit}Select the term type(s) used by the school(s) you attended for %1. If the terms are unfamiliar, use the number of grades you received for a yearlong course as your guide. If you are on the block system, select the one that corresponds to the number of final grades you received per course.{/edit}{/ts}</td>
</tr> 
<tr>
   <td width="205" rowspan="2" valign="top"  style="border-bottom:0;"><strong>Academic Subjects</strong></td>
   <td height="56" colspan="7" valign="top"  style="border-bottom:0;" align="center">
    <p><strong>{ts 1=$wizard.currentStepTitle}Enter %1 courses and your grades.{/ts}</strong>
    <br />
    <div align="left"><p><strong>{ts}Honors Status Key (if applicable, leave blank if none):{/ts}<br />
      HL = Honors Level; CL = College Level; AP = Advanced Placement; IB = International Baccalaureate</strong></p>
    </div>
   </td>
</tr>

<tr>
<td class="grouplabel">Course Title</td><td>Credits</td><td>Honors<br />Status</th>
{if $grade eq 'Summer'}
<td>Year</td>
<td>Grade</td>
{elseif $grade ne 'Twelve'}
<td>1st Term<br />Grade</td>
<td>2nd Term<br />Grade</td>
<td>3rd Term<br />Grade</td>
<td>4th Term<br />Grade</td>
{else}
<td>Grade</td>
{/if}
</tr>
{section name=rowLoop start=1 loop=10}
    {assign var=i value=$smarty.section.rowLoop.index}
     <tr>
        {assign var=as value="academic_subject_id_"|cat:$i}
        <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
	{assign var=as value="course_title_"|cat:$i}
        <td class="fieldlabel" width="15%"> {$form.$as.html|crmReplace:class:medium}</td>
	{assign var=as value="academic_credit_"|cat:$i}
        <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
	{assign var=as value="academic_honor_status_id_"|cat:$i}
        <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
        {if $grade eq 'Summer'}
  	  {assign var=as value="summer_year_"|cat:$i}
	  <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_1"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html|crmReplace:class:two}</td>
        {elseif $grade ne 'Twelve'}
  	  {assign var=as value="grade_"|cat:$i|cat:"_1"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html|crmReplace:class:two}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_2"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html|crmReplace:class:two}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_3"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html|crmReplace:class:two}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_4"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html|crmReplace:class:two}</td>
        {else}
	  <td class="fieldlabel" width="15%">In Progress</td>
        {/if}	
    </tr>
{/section}
</table>
</div>
</td></tr>
</table>

