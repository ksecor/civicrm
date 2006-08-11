{* Quest Pre-application: Extra Curricular Information  section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel">
     {ts}<strong>Extracurricular, Volunteer, and Personal Activities</strong><br />
Please list your principal extracurricular, community, and family activities and hobbies in the order of their interest to you. Include specific events and/or major accomplishments such as musical intruments played, varsity letters earned, etc.{/ts}
    <table cellpadding=0 cellspacing=1 border=2 width="90%" class="app">
       <tr>
          <td><strong>Activity</td>
          <td colspan="5"><strong>Grade Level or post-secondary (P.S)</strong></td>
          <td colspan="2"><strong>Approximate Time spent</strong></td>
          <td><strong>Positions held, honors won,or letters earned</strong></td>
       </tr> 
       <tr>
          <td></td>
          <td>9</td>
          <td>10</td>
          <td>11</td>
          <td>12</td>
          <td>PS</td>
          <td>Hours per week</td>
          <td>Weeks per year</td>
          <td></td>
        </tr>

         {section name=rowLoop start=1 loop=8}
             {assign var=i value=$smarty.section.rowLoop.index}
             <tr>
            
             {assign var=activity value="activity_"|cat:$i}
             <td class="fieldlabel">{$form.$activity.html}</td>  
             {section name=columnLoop start=1 loop=6}
                {assign var=j value=$smarty.section.columnLoop.index}
                {assign var=gl value="grade_level_"|cat:$j|cat:"_"|cat:$i}
                <td class="fieldlabel">{$form.$gl.html}</td>
             {/section}
             {section name=columnLoop start=1 loop=3}
                {assign var=j value=$smarty.section.columnLoop.index}
                {assign var=ts value="time_spent_"|cat:$j|cat:"_"|cat:$i}
                <td class="fieldlabel">{$form.$ts.html|crmReplace:class:four}</td>
             {/section}

             {assign var=positions value="positions_"|cat:$i}  
             <td class="fieldlabel">{$form.$positions.html|crmReplace:class:twelve}</td> 

             </tr>
          {/section} 
    </table>  
    </td>        
</tr>
<tr>
    <td class="grouplabel" width="30%">{$form.essay.meaningful_commitment.label}</td>
    <td class="fieldlabel" width="70%">
        {$form.essay.meaningful_commitment.html}
        <br /><br />
        {$form.word_count.meaningful_commitment.label} &nbsp;&nbsp;{$form.word_count.meaningful_commitment.html}
    </td>
</tr>
<tr>
    <td class="grouplabel" width="30%">{$form.essay.past_activities.label}</td>
    <td class="fieldlabel" width="70%">
        {$form.essay.past_activities.html}
        <br /><br />
        {$form.word_count.past_activities.label} &nbsp;&nbsp;{$form.word_count.past_activities.html}
    </td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td class="grouplabel" colspan=2 >
    <strong>What are your hobbies?</strong><br />
    {ts}We encourage you to reply to this question in sentence form, rather than as a list, if you feel this would allow you to better express your interests.{/ts}
    </td>
</tr>
<tr>
    <td class="fieldlabel" colspan=2 >{$form.essay.hobbies.html}</td>
</tr>    
<tr>
    <td class="grouplabel"> {ts}Are you interested in participating in either of the following in college?{/ts}</td>
    <td class="grouplabel">
	{$form.varsity_sports.html}{$form.varsity_sports.label}	{$form.varsity_sports_list.html}<br/>
	{$form.arts.html}{$form.arts.label}{$form.arts_list.html}
    </td>
    
</tr>
</table>

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="varsity_sports"
    trigger_value       ="1"
    target_element_id   ="varsity_sports_list"
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="arts"
    trigger_value       ="1"
    target_element_id   ="arts_list"
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
