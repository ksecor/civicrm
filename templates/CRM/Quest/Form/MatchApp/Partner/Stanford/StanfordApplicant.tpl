{* Quest College Match: Partner: Columbia: Applicant Info section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    	<td class="grouplabel" rowspan="4" width="33%"><label>{ts}Place of birth{/ts}</label></td>
</tr>
<tr>
	<td class="fieldlabel">{$form.location.1.address.city.html}<br>{ts}{edit}City{/edit}{/ts}</td>
      </tr>
      <tr>
    	<td class="fieldlabel">{$form.location.1.address.state_province_id.html}<br>{ts}{edit}State or Province{/edit}{/ts}</td>
      </tr>
      <tr>
    	<td class="fieldlabel">{$form.location.1.address.country_id.html}<br>{ts}{edit}Country{/edit}{/ts}</td>
      </tr>
<tr>
    <td class="grouplabel"> {$form.is_enrolled_full_time.label}</td>
    <td class="fieldlabel"> {$form.is_enrolled_full_time.html}</td>
</tr>

<tr>
    <td class="grouplabel"> {$form.area_of_major_1.label}</td>
    <td> 
       <table>
        <tr><td class="grouplabel optionlist"><span class="italic-text">1. </span>{$form.area_of_major_1.html}</td></tr>
        <tr><td class="grouplabel optionlist"><span class="italic-text">2. </span>{$form.area_of_major_2.html}</td></tr>
        <tr><td class="grouplabel optionlist"><span class="italic-text">3. </span>{$form.area_of_major_3.html}</td></tr>
       </table>
    </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_parent_employed.label}</td>
    <td class="fieldlabel"> {$form.is_parent_employed.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_sibling_applying.label}</td>
    <td class="fieldlabel"> {$form.is_sibling_applying.html}</td>
</tr>
{if $totalSibligs > 0}
<tr id="tr_sibling_application_status">
    <td>
     {ts}Who?{/ts}
    </td>
    <td>
     <table>
     {section name=rowLoop start=1 loop=$totalSibligs+1}
     
     {assign var=i value=$smarty.section.rowLoop.index}
     {assign var=sibling_id value="sibling_id_"|cat:$i}
     {assign var=sibling_application_status value="sibling_application_status_"|cat:$i}
        
       <tr><td>{$form.$sibling_id.html}</td><td>{$form.$sibling_application_status.html}</td></tr>
     {/section}
     </table>    
    </td>
</tr>
{/if}


</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_sibling_applying"
    trigger_value       ="1"
    target_element_id   ="tr_sibling_application_status" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
