{* Quest Pre-application:  WorkExperience Information section *}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
{strip}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}List any job (including summer employment) you have held during the past three years.{/ts}</p></td>
</tr>
<tr>
    <td>
        <table  cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
        <tr>
    	    <th rowspan="2" class="grouplabel"><strong>Specific nature of work </strong></th>
            <th rowspan="2" nowrap="nowrap"><strong>Employer</strong></th>
            <th colspan="2" nowrap="nowrap"><strong>Approximate dates of employment</strong></th>
            <th rowspan="2" nowrap="nowrap"><strong>Approximate <br />hours/week </strong></th>
            <th rowspan="2" nowrap="nowrap"><strong>Check if <br />Summer <br />job only </strong></th>
        </tr>
        <tr>
            <td class="fieldlabel" nowrap="nowrap"> &nbsp;&nbsp;&nbsp;Start Date </td>
            <td class="fieldlabel" nowrap="nowrap">&nbsp;&nbsp;&nbsp;End Date </td>
        </tr>
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
        <tr id="id_earnings">
            <td colspan="2" class="grouplabel">{ts}To what use have you put your earnings?{/ts}</td>
            <td colspan="4" class="fieldlabel">{$form.earnings.html}</td>
        </tr>

        <tr id="id_school_work">
            <td colspan="2" class="grouplabel">{ts}During the school year, when do you work?{/ts}</td>
            <td colspan="4">{$form.school_work.html}</td>
        </tr>     
        </table>
    </td>
</tr>
</table>
{/strip}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
{literal}
<script type="text/javascript">
    function show_element(trigger_element_id)
    {
        show('id_earnings', 'table-row');
        show('id_school_work', 'table-row');
        element = document.getElementById(trigger_element_id);
        
        if (element.value=='') {
            hide('id_earnings');
            hide('id_school_work');
        }
    }
</script>
{/literal}
