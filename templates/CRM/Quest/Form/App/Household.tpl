{* Quest Pre-application: Household Information section *}

{* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>

{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
  <div id="help">     
In this section, our goal is to better understand your living situation over the past five years. Please answer the following regarding your current, primary household. If you live in two separate homes on a regular basis, please list the one where you spend most of your time
  </div>          
<div class="crm-submit-buttons">
     {$form.buttons.html}
</div>	    	
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.title}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sibling_count.label}</td>
    <td>{$form.sibling_count.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.member_count_1.label}</td>
    <td>{$form.member_count_1.html}</td>
</tr>
<tr>
<td>
Please list the primary caregiver(s) (parents, legal guardians, etc.) in this household: *
</td>
</tr>
<tr>
<td>Family Member</td><td>First Name</td><td>Last Name</td>
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
    <td>{$form.years_lived_1.label}</td>
    <td>{$form.years_lived_1.html}</td>
</tr>
</table>

<div id="help">     
If you lived at the above residence for less than five years, please answer the questions below regarding your previous (primary) household.
</div>  

<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td class="grouplabel">{$form.member_count_2.label}</td>
    <td>{$form.member_count_2.html}</td>
</tr>
<tr>
<td>
Please list the primary caregiver(s) (parents, legal guardians, etc.) in this household: *
</td>
</tr>
<td>Family Member</td><td>First Name</td><td>Last Name</td><td>Same person as in current household above<tr>
<tr>
    <td>{$form.relationship_id_2_1.html}</td>
    <td class="grouplabel">{$form.first_name_2_1.html}</td>
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
    <td>{$form.years_lived_2.label}</td>
    <td>{$form.years_lived_2.html}</td>
</tr>

</table>
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
    <tr>
    <td>{$form.household_note.label}</td>
    <td>{$form.household_note.html}</td>    
    </tr>
</table>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>

