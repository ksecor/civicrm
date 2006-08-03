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
   <td width="205" rowspan="2" valign="top"  style="border-bottom:0;" class="grouplabel"><strong>Academic Subjects</strong></td>
   <td height="56" colspan="7" valign="top"  style="border-bottom:0;" align="center" class="grouplabel">
    <p><strong>{ts 1=$wizard.currentStepTitle}Enter %1 courses and your grades.{/ts}</strong>
    <br />
    <div align="left"><p><strong>{ts}Honors Status Key (if applicable, leave blank if none):{/ts}<br />
      HL = Honors Level; CL = College Level; AP = Advanced Placement; IB = International Baccalaureate</strong></p>
    </div>
   </td>
</tr>

<tr>
    <td class="grouplabel"><strong>Course Title</strong></td>
    <td class="fieldlabel">Credits</td>
    <td class="fieldlabel">Honors<br />Status</th>
    {if $grade eq 'Summer'}
        <td class="fieldlabel">Year</td>
        <td class="fieldlabel">Grade</td>
    {elseif $grade ne 'Twelve'}
        <td class="fieldlabel">1st&nbsp;Term<br />Grade</td>
        <td class="fieldlabel">2nd&nbsp;Term<br />Grade</td>
        <td class="fieldlabel">3rd&nbsp;Term<br />Grade</td>
        <td class="fieldlabel">4th&nbsp;Term<br />Grade</td>
    {else}
        <td class="fieldlabel">Grade</td>
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
        <td class="fieldlabel">{$form.$as.html}</td>
        {assign var=as value="grade_"|cat:$i|cat:"_1"}
        <td class="fieldlabel" width="15%">{$form.$as.html|crmReplace:class:four}</td>
    {elseif $grade ne 'Twelve'}
  	  {assign var=as value="grade_"|cat:$i|cat:"_1"}
	  <td class="fieldlabel" width="15%">{$form.$as.html|crmReplace:class:four}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_2"}
	  <td class="fieldlabel" width="15%">{$form.$as.html|crmReplace:class:four}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_3"}
	  <td class="fieldlabel" width="15%">{$form.$as.html|crmReplace:class:four}</td>
  	  {assign var=as value="grade_"|cat:$i|cat:"_4"}
	  <td class="fieldlabel" width="15%">{$form.$as.html|crmReplace:class:four}</td>
    {else}
	  <td class="fieldlabel" width="15%">{ts}In Progress{/ts}</td>
    {/if}	
    </tr>
{/section}
</table>
<div class="horizontal-center">
<table width="670" border="1" cellpadding="0" cellspacing="1" class="app">
  <tr>
    <td colspan="5" id="category">Academic Subject Guide</td>
  </tr>
  <tr valign="top">
    <td width="25%"><strong>History/Social Science</strong><br />
        <small>U.S. History; Civics, American Government; World History, Cultures and Geography; European History</small> <br />
    </td>
    <td width="221" colspan="2"><strong>English (Language of Instruction)</strong><br />
        <small>Composition, Literature (American, English, World, etc.)</small></td>
    <td width="25%"><strong>Mathematics</strong><br />
          <small>Algebra, Geometry, Advanced Algebra, Trigonometry, Pre-Calculus, Integrated Math, Calculus, Statistics, Math Analysis</small></span></td>
    <td width="25%"><strong>Laboratory Science</strong><br />
          <small>Biology, Chemistry, Physics, Integrated Science with Lab, Marine Biology, Physiology, Anatomy, etc.</small></span></td>
  </tr>
  <tr valign="top">
    <td><strong>Language other than English</strong><br />
        <small>French, German, Spanish, Latin, Mandarin Chinese, Japanese, etc.</small></td>
    <td colspan="2"><strong>Visual and Performing Arts</strong><br />

          <small>Dance, Drama/Theater, Music, Visual Arts</small></span></td>
    <td><strong>College Preparatory Academic Electives</strong><br />
          <small>Social Science, Anthropology, Economics, Psychology, Sociology, Computer Science, etc. </small></span></td>
    <td><strong>Electives</strong><br />
          <small>Physical Education, Home Economics, Auto Shop, Wood-working, Engineering, Welding, etc.</small></span></td>
  </tr>
</table>
</div>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
