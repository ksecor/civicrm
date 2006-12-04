{* Quest Pre-application: Scholarship Information section *}

{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
     <td class="grouplabel" colspan="2"> <b>Income Information</b> </td>
</tr> 
<tr>
    <td class="grouplabel"> {$form.fed_lunch_id.label}</td>
    <td class="fieldlabel"> {$form.fed_lunch_id.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.financial_aid_applicant.label}</td>
    <td class="fieldlabel"> {$form.financial_aid_applicant.html}</td>
</tr>
</table>
<br/>
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
     <td class="grouplabel" colspan="2"> <b>Family Information</b> </td>
</tr> 
<tr>
    <td class="grouplabel">
        {$form.parent_grad_college_id.label}</td>
    <td class="fieldlabel">
        {$form.parent_grad_college_id.html}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel">
     {ts}For any of your relatives who are alumni/ae at any of our partner colleges/universities, please list their names, relationship to you and years of graduation, if known:{/ts}
    <table cellpadding=0 cellspacing=1 border=2 width="90%" class="app">
       <tr class="bold-label vertical-center-text">
          <td>Partner Institution</td>
          <td>First Name</td>
          <td>Last Name</td>
          <td>Class year</td> 
          <td>Relationship</td>  

        </tr> 
           {section name=rowLoop start=1 loop=6}
             {assign var=i value=$smarty.section.rowLoop.index}
             <tr>
             {assign var=partner_institution value="alumni_partner_institution_id_"|cat:$i}  
             {assign var=first_name value="alumni_first_name_"|cat:$i}  
             {assign var=last_name value="alumni_last_name_"|cat:$i}  
             {assign var=class_year value="alumni_class_year_"|cat:$i}  
             {assign var=relationship value="alumni_relationship_"|cat:$i}  
   
             <td class="fieldlabel">{$form.$partner_institution.html}</td>  
             <td class="fieldlabel">{$form.$first_name.html|crmReplace:class:eight}</td>
             <td class="fieldlabel">{$form.$last_name.html|crmReplace:class:eight}</td>
             <td class="fieldlabel">{$form.$class_year.html}</td> 
             <td class="fieldlabel">{$form.$relationship.html|crmReplace:class:eight}</td>  
             </tr>
          {/section} 
       
        
    </table>  
    </td>        
</tr>
<tr>
    <td colspan=2 class="grouplabel">
    {ts}Please list any of your family members or relatives who are presently employed at any of our partner colleges/universities.{/ts}
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
      <table cellpadding=0 cellspacing=2 border=1 width="90%" class="app">
       <tr class="bold-label vertical-center-text">
          <td>Partner Institution</td>
          <td>First Name</td>
          <td>Last Name</td>
          <td>Department</td> 
          <td>Relationship</td>  

        </tr> 
          {section name=rowLoop start=1 loop=6}
             {assign var=i value=$smarty.section.rowLoop.index}
             <tr>
             {assign var=partner_institution value="employee_partner_institution_id_"|cat:$i}  
             {assign var=first_name value="employee_first_name_"|cat:$i}  
             {assign var=last_name value="employee_last_name_"|cat:$i}  
             {assign var=department value="employee_department_"|cat:$i}  
             {assign var=relationship value="employee_relationship_"|cat:$i}  
   
             <td class="fieldlabel">{$form.$partner_institution.html}</td>  
             <td class="fieldlabel">{$form.$first_name.html|crmReplace:class:eight}</td>
             <td class="fieldlabel">{$form.$last_name.html|crmReplace:class:eight}</td>
             <td class="fieldlabel">{$form.$department.html|crmReplace:class:eight}</td> 
             <td class="fieldlabel">{$form.$relationship.html|crmReplace:class:eight}</td>  
             </tr>
          {/section} 
       
        
    </table>  
    </td>        
</tr>
</table>
<br/>



<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
     <td class="grouplabel" colspan="2"> <b>Legal and Disciplinary Information</b> </td>
</tr> 
<tr>
    <td class="grouplabel" width="80%">
        {$form.is_dismissed.label}</td>
    <td class="fieldlabel" width="20%">
        {$form.is_dismissed.html}</td>
</tr>
<tr id = "explain_dismissed">
    <td  class="grouplabel">
        {$form.explain_dismissed.label}</td>
    <td class="fieldlabel">
        {$form.explain_dismissed.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.is_convicted.label}</td>
    <td class="fieldlabel">
        {$form.is_convicted.html}</td>
</tr>
<tr id = "explain_convicted">
    <td class="grouplabel">
        {$form.explain_convicted.label}</td>
    <td class="fieldlabel">
        {$form.explain_convicted.html}</td>
</tr>
</table>
<br/>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
     <td class="grouplabel" colspan="2"> <b>Other Information</b> </td>
</tr> 
<tr>
     <td class="grouplabel" width="30%"> {$form.is_health_insurance.label} </td>
     <td class="fieldlabel" width="70%">{$form.is_health_insurance.html}</td>
</tr> 
<tr>
    <td class="grouplabel"> {$form.displacement.label}</td>
    <td class="fieldlabel"> {$form.displacement.html} </td>
</tr>
</table>
<br/>


<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
     <td class="grouplabel" colspan="2"> 
          <b>Survey Information</b><br/>
          The following questions are not required in order to complete your application. However, we would appreciate any information you may be able to provide.
     </td>
</tr> 
<tr>
    <td class="grouplabel"> {$form.heard_about_qb_id.label}</td>
    <td class="fieldlabel"> 
        <table cellpadding=0 cellspacing=1 border=1 width="90%" >
        {foreach from=$form.heard_about_qb_id item=type key=key}
        {assign var="countEI" value=`$countEI+1`}
        {if $countEI gt 9 }
	   {if $key is odd}		
              <tr>
 	   {/if}
		<td class="optionlist"> {$form.heard_about_qb_id.$key.html}
	        {assign var=heard_about_name value="heard_about_qb_name_"|cat:$key}  
	        {if $form.$heard_about_name.html}
	            {assign var=element_id value="heard_about_name_"|cat:$key }
	            {if $key eq 3}
	            <span id={$element_id}>( Website: {$form.$heard_about_name.html} )
	            {else}
	            <span id={$element_id}>( Please specify: {$form.$heard_about_name.html} )</span>
	            {/if}
	        {/if}
	        </td>
	   {if $key is even}		
              </tr>
 	   {/if}
        {/if}
        {/foreach}
        </table>
    </td>
</tr>

<tr>
    <td class="grouplabel" colspan=2> 
     We have found that fellow students are a valuable source of information in identifying qualified
applicants for the following year's application cycle. Please enter contact information for current juniors that you think would be strong applicants for next year.
    <table cellpadding=0 cellspacing=1 border=2 width="90%" class="app">
     <tr class="bold-label tr-vertical-center-text">
          <td>First Name</td>
          <td>Last Name</td>
          <td>School</td>
          <td>Year of Graduation</td> 
          <td>Email</td>
          <td>Telephone</td>     

     </tr>    
     {section name=rowLoop start=1 loop=4}
          {assign var=i value=$smarty.section.rowLoop.index}
          
            {assign var=first_name value="referral_student_first_name_"|cat:$i}
            {assign var=last_name value="referral_student_last_name_"|cat:$i}  
            {assign var=school value="referral_student_school_"|cat:$i}
            {assign var=year value="referral_student_year_"|cat:$i}            
            {assign var=email value="referral_student_email_"|cat:$i}
            {assign var=phone value="referral_student_phone_"|cat:$i}
            
            <tr>    
            <td>{$form.$first_name.html|crmReplace:class:eight}</td>
            <td>{$form.$last_name.html|crmReplace:class:eight}</td>      
            <td>{$form.$school.html|crmReplace:class:eight}</td>
            <td>{$form.$year.html}</td>
            <td>{$form.$email.html|crmReplace:class:eight}</td>
            <td>{$form.$phone.html|crmReplace:class:eight}</td>
           </tr>
     {/section} 

    </table>
    </td>
</tr>
<tr>
    <td class="grouplabel" colspan=2> 
    Please enter contact information for 3 teachers or counselors in your local area who you think would be helpful in identifying students like you who would qualify for and benefit from particiaption in QuestBridge. (Please ask these teachers or counselors for permission to include their contact information. We will use this information in future years to help us identify students to apply to QuestBridge)
     <table cellpadding=0 cellspacing=1 border=2 width="90%" class="app">
      <tr class="bold-label">
          <td>First Name</td>
          <td>Last Name</td>
          <td>School</td>
          <td>Position</td> 
          <td>Email</td>
          <td>Telephone</td>     
      </tr> 
        {section name=rowLoop start=1 loop=4}
          {assign var=i value=$smarty.section.rowLoop.index}   
            {assign var=first_name value="referral_educator_first_name_"|cat:$i}
            {assign var=last_name value="referral_educator_last_name_"|cat:$i}  
            {assign var=school value="referral_educator_school_"|cat:$i}
            {assign var=position value="referral_educator_position_id_"|cat:$i}            
            {assign var=email value="referral_educator_email_"|cat:$i}
            {assign var=phone value="referral_educator_phone_"|cat:$i}
            <tr>    
            <td>{$form.$first_name.html|crmReplace:class:eight}</td>
            <td>{$form.$last_name.html|crmReplace:class:eight}</td>      
            <td>{$form.$school.html|crmReplace:class:eight}</td>
            <td>{$form.$position.html}</td>
            <td>{$form.$email.html|crmReplace:class:eight}</td>
            <td>{$form.$phone.html|crmReplace:class:eight}</td>
           </tr>
        {/section} 

    </table>
    </td>
</tr>
</table>
    

{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="end"}

{edit}
{literal}
<script type="text/javascript">
    show_element("heard_about_qb_id");
    function show_element(trigger_element_id)
    {
        var element = document.getElementsByName(trigger_element_id);
        for ( i=0; i<9; i++) {
            if (i==0 || i==1 || i==7) {
                continue;
            }
            if (element[i].checked) {
                show("heard_about_name_" + element[i].value);
            } else {
                hide("heard_about_name_" + element[i].value);
            }
        }
        
    }
    
</script>
{/literal}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_dismissed"
    trigger_value       ="1"
    target_element_id   ="explain_dismissed" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_convicted"
    trigger_value       ="1"
    target_element_id   ="explain_convicted" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{/edit}
