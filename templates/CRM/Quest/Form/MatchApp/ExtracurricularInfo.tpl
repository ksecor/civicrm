{* Quest Pre-application: Extra Curricular Information  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>


 <tr>
    <td colspan=2 class="grouplabel">
     {ts}Extracurricular, Volunteer, and Personal Activities
Please list your principal extracurricular, community, and family activities and hobbies in the order of their interest to you. Include specific events and/or major accomplishments such as musical intruments played, varsity letters earned, etc.{/ts}
    <table cellpadding=0 cellspacing=1 border=2 width="90%" class="app">
       <tr>
          <td>Activity</td>
          <td colspan="5">Grade Level or post-secondary (P.S)</td>
          <td colspan="2">Approximate Time spent</td>
          <td>Positions held, honors won,or letters earned</td>
       </tr> 
       <tr>
          <td></td>
          <td>9</td>
          <td>10</td>
          <td>11</td>
          <td>12</td>
          <td>PS</td>
          <td>Hours per week</td>
          <td>Weeks per year </td>
          <td></td>
        </tr>

         {section name=rowLoop start=1 loop=7}
             {assign var=i value=$smarty.section.rowLoop.index}
             <tr>
            
             {assign var=activity value="activity_"|cat:$i}  
             {assign var=grade_level_1 value="grade_level_1_"|cat:$i}  
             {assign var=grade_level_2 value="grade_level_2_"|cat:$i}  
             {assign var=grade_level_3 value="grade_level_3_"|cat:$i}  
             {assign var=grade_level_4 value="grade_level_4_"|cat:$i}  
             {assign var=grade_level_5 value="grade_level_5_"|cat:$i}  
             {assign var=time_spent_1 value="time_spent_1_"|cat:$i}  
             {assign var=time_spent_2 value="time_spent_2_"|cat:$i}  
             {assign var=positions value="positions_"|cat:$i}  
   
             <td class="fieldlabel">{$form.$activity.html}</td>  
             <td class="fieldlabel">{$form.$grade_level_1.html}</td>
             <td class="fieldlabel">{$form.$grade_level_2.html}</td>
             <td class="fieldlabel">{$form.$grade_level_3.html}</td>   
             <td class="fieldlabel">{$form.$grade_level_4.html}</td>
             <td class="fieldlabel">{$form.$grade_level_5.html}</td>
             <td class="fieldlabel">{$form.$time_spent_1.html}</td>
             <td class="fieldlabel">{$form.$time_spent_2.html}</td>
             <td class="fieldlabel">{$form.$positions.html}</td> 

             </tr>
          {/section} 
    </table>  
    </td>        
    </tr>
<tr>
    <td class="grouplabel">{$form.meaningful_commitment.label}</td>
    <td class="fieldlabel">{$form.meaningful_commitment.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.past_activities.label}</td>
    <td class="fieldlabel">{$form.past_activities.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.hobbies.label}</td>
    <td class="fieldlabel">{$form.hobbies.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.varsity_sports.label}</td>
    <td class="fieldlabel">{$form.varsity_sports.html} {$form.varsity_sports_list.html}</td>
    </td>
</tr>
<tr>
    <td class="grouplabel">{$form.arts.label}</td>
    <td class="fieldlabel">{$form.arts.html}{$form.arts_list.html}</td>
</tr>

</table>


{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
