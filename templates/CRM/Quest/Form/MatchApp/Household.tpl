{* Quest Pre-application: Household Information section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=4 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td colspan="4" class="grouplabel">
    <p class="preapp-instruction">{ts}In this section, our goal is to better understand your living situation over the past five years. Please answer the following regarding your current, primary household. If you live in two separate homes on a regular basis, please list the one where you spend more of your time.{/ts}</p>
  </td>
</tr>
<tr>
    <td colspan="3" class="grouplabel">{$form.member_count_1.label}</td>
    <td class="fieldlabel">{$form.member_count_1.html}</td>
</tr>
<tr>
    <td colspan="4" class="grouplabel">
    <strong>{ts}Please list the primary caregiver(s) (parents, legal guardians, etc.) in this household{/ts}: <span class="marker">*</span></strong>
</td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}Family Member{/ts}</label></td>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel" colspan="2"><label>{ts}Last Name{/ts}</label></td>
</tr>
<tr>
    <td class="fieldlabel">{$form.relationship_id_1_1.html}</td>
    <td class="fieldlabel">{$form.first_name_1_1.html}</td>
    <td class="fieldlabel" colspan="2">{$form.last_name_1_1.html}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.relationship_id_1_2.html}</td>
    <td class="fieldlabel">{$form.first_name_1_2.html}</td>
    <td class="fieldlabel" colspan="2">{$form.last_name_1_2.html}</td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">{$form.years_lived_id_1.label}</td>
    <td class="fieldlabel" colspan="2">{$form.years_lived_id_1.html}</td>
</tr>
</table>
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
  <td colspan="4" class="grouplabel">
    <p class="preapp-instruction">
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
    <strong>{ts}Please list the primary caregiver(s) (parents, legal guardians, etc.) in this household{/ts}:</strong>
    </td>
</tr>
    <td class="grouplabel"><label>{ts}Family Member{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Same person as in current household above{/ts}</label></td>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
</tr>
<tr>
    <td class="fieldlabel">{$form.relationship_id_2_1.html}</td>
    <td class="fieldlabel">{$form.same_2_1.html}</td>
    <td class="fieldlabel">{$form.first_name_2_1.html}</td>
    <td class="fieldlabel">{$form.last_name_2_1.html}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.relationship_id_2_2.html}</td>
    <td class="fieldlabel">{$form.same_2_2.html}</td>
    <td class="fieldlabel">{$form.first_name_2_2.html}</td>
    <td class="fieldlabel">{$form.last_name_2_2.html}</td>
</tr>
<tr>
    <td class="grouplabel" colspan="2">{$form.years_lived_id_2.label}</td>
    <td class="fieldlabel" colspan="2">{$form.years_lived_id_2.html}</td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">{$form.description.label}</td>
    <td colspan="2" class="fieldlabel">{$form.description.html}</td>    
</tr>
<tr id ="foster_child_show">
    <td colspan="2" class="grouplabel">{$form.foster_child.label}</td>
    <td colspan="2" class="fieldlabel">{$form.foster_child.html}</td>    
</tr>



</table>

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{literal}
    <script type="text/javascript">
    var field = new Array(3);  
	field[0] = "first_name_";
	field[1] = "last_name_";
	field[2] = "relationship_id_";
    
   	function copyNames(cbName, index) 
    {
        if (document.getElementsByName(cbName)[0].checked) {
		    for (var j=0; j<field.length; j++) {
                var currentElement = document.getElementById(field[j]+"2_"+index);
                var previousElement = document.getElementById(field[j]+"1_"+index);
                
                currentElement.value = previousElement.value;
		    }
        } else {
		    for (var j=0; j<field.length; j++) {
                var currentElement = document.getElementById(field[j]+"2_"+index);
			    currentElement.value = '';
		    }
        }
    }
    
    function show_foster(trigger_element_name,target_element_name) {
        var index_1 = document.getElementById(trigger_element_name + "_1").selectedIndex;
        var index_2 = document.getElementById(trigger_element_name + "_2").selectedIndex;
        if ( ( ( index_1 > 2 ) && ( index_2 > 2 ) ) ) {
            show(target_element_name, '');
        } else if ( ( ( index_1 == 0 ) && ( index_2 == 0 ) ) ) {
            hide(target_element_name);
        } else if ( ( ( index_1 == 0 ) && ( index_2 > 2 ) ) ) {
            show(target_element_name, '');
        } else if ( ( ( index_2 == 0 ) && ( index_1 > 2 ) ) ) {
            show(target_element_name, '');
        } else {
            hide(target_element_name);
        }
    }

    show_foster('relationship_id_1','foster_child_show');
    </script>
{/literal}
