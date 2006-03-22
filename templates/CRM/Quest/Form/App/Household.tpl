{* Quest Pre-application: Household Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=3 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>
<tr>
  <td colspan=3>
<div id="help">     
    {ts}In this section, our goal is to better understand your living situation over the past five years. Please answer the following regarding your current, primary household. If you live in two separate homes on a regular basis, please list the one where you spend most of your time.{/ts}
</div>
  </td>
</tr>
<tr>
    <td colspan=2 class="grouplabel">{$form.member_count_1.label}</td>
    <td>{$form.member_count_1.html}</td>
</tr>
<tr>
    <td colspan=3>
    {ts}Please list the primary caregiver(s) (parents, legal guardians, etc.) in this household{/ts}: *
</td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}Family Member{/ts}</label></td>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
</tr>
<tr>
    <td>{$form.relationship_id_1_1.html}</td>
    <td class="grouplabel">{$form.first_name_1_1.html}</td>
    <td>{$form.last_name_1_1.html}</td>
</tr>
<tr>
    <td>{$form.relationship_id_1_2.html}</td>
    <td class="grouplabel">{$form.first_name_1_2.html}</td>
    <td>{$form.last_name_1_2.html}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel">{$form.years_lived_id_1.label}</td>
    <td>{$form.years_lived_id_1.html}</td>
</tr>
</table>

<div id="help">     
    {ts}If you lived at the above residence for less than five years, please answer the questions below regarding your previous (primary) household.{/ts}
</div>  

<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=3 class="grouplabel">{$form.member_count_2.label}</td>
    <td>{$form.member_count_2.html}</td>
</tr>
<tr>
    <td colspan=4>
    {ts}Please list the primary caregiver(s) (parents, legal guardians, etc.) in this household{/ts}: *
    </td>
</tr>
    <td class="grouplabel"><label>{ts}Family Member{/ts}</label></td>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Same person as in current household above{/ts}</label></td>
</tr>
<tr>
    <td>{$form.relationship_id_2_1.html}</td>
    <td>{$form.first_name_2_1.html}</td>
    <td>{$form.last_name_2_1.html}</td>
    <td>{$form.same_2_1.html}</td>
</tr>
<tr>
    <td>{$form.relationship_id_2_2.html}</td>
    <td class="grouplabel">{$form.first_name_2_2.html}</td>
    <td>{$form.last_name_2_2.html}</td>
    <td>{$form.same_2_2.html}</td>
</tr>
<tr>
    <td colspan=3>{$form.years_lived_id_2.label}</td>
    <td>{$form.years_lived_id_2.html}</td>
</tr>

</table>
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
    <tr>
    <td>{$form.household_note.label}</td>
    <td>{$form.household_note.html}</td>    
    </tr>
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

