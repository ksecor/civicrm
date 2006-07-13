{* Quest Pre-application:  WorkExperience Information section *}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}List any job (including summer employment) you have held during the past three years.{/ts}</p></td>
</tr>
<tr><td>
<table  cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
 <tr>
    	<td rowspan="2" class="grouplabel"><strong>Specific nature of work </strong></td>
        <td rowspan="2" nowrap="nowrap"><strong>Employer</strong></td>
        <td colspan="2" nowrap="nowrap"><strong>Approximate dates of employment</strong></td>
        <td rowspan="2" nowrap="nowrap"><strong>Approximate <br>hours/week </strong></td>
        <td rowspan="2" nowrap="nowrap"><strong>Check if <br>Summer <br>job only </strong></td>
 </tr>
<tr>
        <td class="fieldlabel" nowrap="nowrap"> &nbsp;&nbsp;&nbsp;Start Date </td>
        <td class="fieldlabel" nowrap="nowrap">&nbsp;&nbsp;&nbsp;End Date </td>
 </tr>
 <tr>
 {section name=rowLoop start=1 loop=$maxWork}
   {assign var=i value=$smarty.section.rowLoop.index}
  
   
	{assign var=nature_of_work value="nature_of_work_"|cat:$i}
	{assign var=employer value="employer_"|cat:$i}
    {assign var=start_date value="start_date_"|cat:$i}
    {assign var=end_date value="end_date_"|cat:$i}    	
    {assign var=hrs value="hrs_"|cat:$i}
    {assign var=summer_jobs value="summer_jobs_"|cat:$i}  
     <tr>
       	<td>{$form.$nature_of_work.html} </td>
        <td>{$form.$employer.html}</td>
        <td class="fieldlabel" nowrap="nowrap">{$form.$start_date.html}</td>
        <td class="fieldlabel" nowrap="nowrap">{$form.$end_date.html} </td>
       	<td>{$form.$hrs.html} </td>
    	<td class="fieldlabel" nowrap="nowrap"><div align="center">{$form.$summer_jobs.html}</td>
    </tr>
    {/section}
<tr>
<td colspan="2" class="grouplabel">{ts}To what use have you put your earnings?{/ts}</td>
<td colspan="4" class="fieldlabel">{$form.earnings.html}</td>
</tr>
<td colspan="2" class="grouplabel">{ts}During the school year, when do you work?{/ts}</td>
<td colspan="4">{$form.school_work.html}</td>
        
</td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
