{* Quest Pre-application: Household Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="4" id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>
<tr>
  <td colspan="4" class="grouplabel">
    <br /><p>{ts}In this section, our goal is to better understand your living situation over the past five years. Please answer the following regarding your current, primary household. If you live in two separate homes on a regular basis, please list the one where you spend most of your time.{/ts}</p>
  </td>
</tr>
<tr>
    <td colspan="3" class="grouplabel">{$form.member_count_1.label}</td>
    <td class="fieldlabel">{$form.member_count_1.html}</td>
</tr>
<tr>
    <td colspan="4" class="grouplabel">
    {ts}Please list the primary caregiver(s) (parents, legal guardians, etc.) in this household{/ts}: *
</td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}Family Member{/ts}</label></td>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
    <td class="fieldlabel"></td>
</tr>
<tr>
    <td class="fieldlabel">{$form.relationship_id_1_1.html}</td>
    <td class="fieldlabel">{$form.first_name_1_1.html}</td>
    <td class="fieldlabel">{$form.last_name_1_1.html}</td>
    <td class="fieldlabel"></td>
</tr>
<tr>
    <td class="fieldlabel">{$form.relationship_id_1_2.html}</td>
    <td class="fieldlabel">{$form.first_name_1_2.html}</td>
    <td class="fieldlabel" colspan="2">{$form.last_name_1_2.html}</td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">{$form.years_lived_id_1.label}</td>
    <td class="fieldlabel">{$form.years_lived_id_1.html}</td>
    <td class="fieldlabel"></td>
</tr>
<tr>
  <td colspan="4" class="grouplabel">
    <p>
    {ts}If you lived at the above residence for less than five years, please answer the questions below regarding your previous (primary) household.{/ts}
    </p>
  </td>
</tr>
<tr>
    <td colspan="3" class="grouplabel">{$form.member_count_2.label}</td>
    <td class="fieldlabel">{$form.member_count_2.html}</td>
</tr>
<tr>
    <td colspan="4" class="grouplabel">
    {ts}Please list the primary caregiver(s) (parents, legal guardians, etc.) in this household{/ts}: *
    </td>
</tr>
    <td class="grouplabel"><label>{ts}Family Member{/ts}</label></td>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Same person as in current household above{/ts}</label></td>
</tr>
<tr>
    <td class="fieldlabel">{$form.relationship_id_2_1.html}</td>
    <td class="fieldlabel">{$form.first_name_2_1.html}</td>
    <td class="fieldlabel">{$form.last_name_2_1.html}</td>
    <td class="fieldlabel">{$form.same_2_1.html}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.relationship_id_2_2.html}</td>
    <td class="fieldlabel">{$form.first_name_2_2.html}</td>
    <td class="fieldlabel">{$form.last_name_2_2.html}</td>
    <td class="fieldlabel">{$form.same_2_2.html}</td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">{$form.years_lived_id_2.label}</td>
    <td class="fieldlabel">{$form.years_lived_id_2.html}</td>
    <td class="fieldlabel"></td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">{$form.description.label}</td>
    <td colspan="2" class="fieldlabel">{$form.description.html}</td>    
</tr>
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

{literal}
    <script type="text/javascript">

      var selectedSamePerson = new Array(2); 
      var field = new Array(3);

	field[0] = "first_name_";
	field[1] = "last_name_";
	field[2] = "relationship_id_";

   	function copyNames() {
	     for (var i = 0; i < selectedSamePerson.length; i++) {		
		selectedSamePerson[i] = document.getElementsByName("same_2_"+(i+1))[0].checked;
		   if (selectedSamePerson[i]) {
		      for (var j = 0; j < field.length; j++) {		
			  document.getElementById(field[j]+"2_"+(i+1)).value =
				document.getElementById(field[j]+"1_"+(i+1)).value;
		      }	
		   } else {
		      for (var j = 0; j < field.length; j++) {		
			  document.getElementById(field[j]+"2_"+(i+1)).value = null;
		      }	
		   }
	     }
 	}
    </script>  
{/literal}
