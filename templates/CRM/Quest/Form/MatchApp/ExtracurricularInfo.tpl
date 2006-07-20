{* Quest Pre-application: Extra Curricular Information  section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

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
                <td class="fieldlabel">{$form.$ts.html}</td>
             {/section}

             {assign var=positions value="positions_"|cat:$i}  
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
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td class="grouplabel" colspan=2 >
    <strong>What are your hobbies? </strong><br>
    {$form.hobbies.label}
    </td>
</tr>
<tr>
    <td class="fieldlabel" colspan=2 >{$form.hobbies.html}</td>
</tr>    
<tr>
    <td class="grouplabel"> {ts}Are you interested in participating in any of the following in college?{/ts}</td>
    <td class="grouplabel">
	{$form.varsity_sports.html}{$form.varsity_sports.label}	{$form.varsity_sports_list.html}<br/>
	{$form.arts.html}{$form.arts.label}{$form.arts_list.html}
    </td>
    
</tr>
</table>


{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{literal}
    <script type="text/javascript">
	if (document.getElementById("varsity_sports_list").value) {
	  document.getElementsByName("varsity_sports")[0].checked = true;
	}
	if (document.getElementById("arts_list").value) {
	  document.getElementsByName("arts")[0].checked = true;
	}
	if (document.getElementsByName("varsity_sports")[0].checked) {
	  show("varsity_sports_list");
	} else {
          hide("varsity_sports_list");
	}
	if (document.getElementsByName("arts")[0].checked) {
	  show("arts_list");
	} else {
          hide("arts_list");
	}

   	function showTextField() {
		if (document.getElementsByName("varsity_sports")[0].checked) {
		  show("varsity_sports_list");
		} else {
	          hide("varsity_sports_list");
		  document.getElementById("varsity_sports_list").value = null;
		}
		if (document.getElementsByName("arts")[0].checked) {
		  show("arts_list");
		} else {
	          hide("arts_list");
		  document.getElementById("arts_list").value = null;
		}
	}
    </script>  
{/literal}
