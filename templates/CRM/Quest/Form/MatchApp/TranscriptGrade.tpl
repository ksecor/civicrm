{* Quest Application: Transcript Sections (Grades 9 - 12 and Summer School *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.term_system_id.label}</td>
    <td class="fieldlabel">{$form.term_system_id.html} <br/>{ts} {edit}Select the term type(s) used by the school(s) you attended for 9th grade. If the terms are unfamiliar, use the number of grades you received for a yearlong course as your guide. If you are on the block system, select the one that corresponds to the number of final grades you received per course.{/edit}{/ts}</td>
</tr> 
<tr>
   <td class="grouplabel">Academic Subjects</td>
   <td class="fieldlabel">{ts 1=$wizard.currentStepTitle}Enter %1 courses and your grades.{/ts}
<br />
Honors Status Key (if applicable, leave blank if none):
<br />
HL = Honors Level; CL = College Level; AP = Advanced Placement; IB = International Baccalaureate
</td>
</tr>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
<th>Subject</th><th>Course Title</th><th>Credits</th><th>Honors Status</th>
{if $grade eq 'Summer'}
<th>Year</th>
<th>Grade</th>
{elseif $grade ne 'Twelve'}
<th>1st Grade</th>
<th>2nd Grade</th>
<th>3rd Grade</th>
<th>4th Grade</th>
{else}
<th>Grade</th>
{/if}
</tr>
{section name=rowLoop start=1 loop=10}
    {assign var=i value=$smarty.section.rowLoop.index}
     <tr>
        {assign var=as value="academic_subject_id_"|cat:$i}
        <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
	{assign var=as value="course_title_"|cat:$i}
        <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
	{assign var=as value="academic_credit_"|cat:$i}
        <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
	{assign var=as value="academic_honor_status_id_"|cat:$i}
        <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
        {if $grade eq 'Summer'}
  	  {assign var=as value="summer_year_"|cat:$i}
	  <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_1"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
        {elseif $grade ne 'Twelve'}
  	  {assign var=as value="grade_"|cat:$i|cat:"_1"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_2"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_3"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_4"}
	  <td class="fieldlabel" width="15%"> {$form.$as.html}</td>
        {else}
	  <td class="fieldlabel" width="15%">In Progress</td>
        {/if}	
    </tr>
{/section}
</table>
</div>
</td></tr>
</table>

