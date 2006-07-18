{* Quest Pre-application: Scholarship Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
     <td class="grouplabel"> {$form.internet_access_id.label} </td>
     <td class="fieldlabel">{$form.internet_access_id.html}</td>
</tr> 
<tr id="internet_access_other">
     <td class="grouplabel">&nbsp;</td>
     <td class="fieldlabel">{$form.internet_access_other.html}<br />
        {ts}{edit}Describe your primary internet access method.{/edit}{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_home_computer.label}</td>
    <td class="fieldlabel"> {$form.is_home_computer.html} </td>
</tr> 
<tr id="is_home_internet">
    <td class="grouplabel"> {$form.is_home_internet.label} </td>
    <td class="fieldlabel"> {$form.is_home_internet.html} </td>
</tr> 
<tr>
    <td class="grouplabel"> {$form.fed_lunch_id.label}</td>
    <td class="fieldlabel"> {$form.fed_lunch_id.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_take_SAT_ACT.label}</td>
    <td class="fieldlabel"> {$form.is_take_SAT_ACT.html}</td>
</tr>
<tr id="study_method_id">
    <td class="grouplabel"> {$form.study_method_id.label}</td>
    <td class="fieldlabel"> {$form.study_method_id.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.financial_aid_applicant.label}</td>
    <td class="fieldlabel"> {$form.financial_aid_applicant.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.register_standarized_tests.label}</td>
    <td class="fieldlabel"> {$form.register_standarized_tests.html} </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.displacement.label}</td>
    <td class="fieldlabel"> {$form.displacement.html} </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.heard_about_qb_id.label}</td>
    <td class="fieldlabel"> 
        <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
        {foreach from=$form.heard_about_qb_id item=type key=key}
        {assign var="countEI" value=`$countEI+1`}
        {if $countEI gt 9 }         
        <tr><td class="fieldlabel"> {$form.heard_about_qb_id.$key.html}
        {assign var=heard_about_name value="heard_about_qb_name_"|cat:$key}  
        {if $form.$heard_about_name.html}
            {if $key eq 3}
            ( Website: {$form.$heard_about_name.html} )
            {else}
            ( Name: {$form.$heard_about_name.html} )
            {/if}
        {/if}
        </td></tr>
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
     <tr>
          <td>Last Name</td>
          <td>First Name</td>
          <td>School</td>
          <td>Year of Graduation</td> 
          <td>Email</td>
          <td>Telephone</td>     

     </tr>    
     {section name=rowLoop start=1 loop=4}
          {assign var=i value=$smarty.section.rowLoop.index}
          
           {assign var=last_name value="referral_student_last_name_"|cat:$i}  
            {assign var=first_name value="referral_student_first_name_"|cat:$i}
            {assign var=school value="referral_student_school_"|cat:$i}
            {assign var=year value="referral_student_year_"|cat:$i}            
            {assign var=email value="referral_student_email_"|cat:$i}
            {assign var=phone value="referral_student_phone_"|cat:$i}
            
            <tr>    
            <td>{$form.$last_name.html|crmReplace:class:six}</td>      
            <td>{$form.$first_name.html|crmReplace:class:six}</td>
            <td>{$form.$school.html}</td>
            <td>{$form.$year.html}</td>
            <td>{$form.$email.html}</td>
            <td>{$form.$phone.html|crmReplace:class:six}</td>
           </tr>
     {/section} 

    </table>
    </td>
</tr>
<tr>
    <td class="grouplabel" colspan=2> 
    Please enter contact information for 3 teachers or counselors in your local area who you think would be helpful in identifying students like you who would qualify for and benefit from particiaption in QuestBridge. (Please ask these teachers or counselors for permission to include their contact information. We will use this information in future years to help us identify students to apply to QuestBridge)
     <table cellpadding=0 cellspacing=1 border=2 width="90%" class="app">
      <tr>
          <td>Last Name</td>
          <td>First Name</td>
          <td>School</td>
          <td>Position</td> 
          <td>Email</td>
          <td>Telephone</td>     
      </tr> 
        {section name=rowLoop start=1 loop=4}
          {assign var=i value=$smarty.section.rowLoop.index}   
            {assign var=last_name value="referral_educator_last_name_"|cat:$i}  
            {assign var=first_name value="referral_educator_first_name_"|cat:$i}
            {assign var=school value="referral_educator_school_"|cat:$i}
            {assign var=position value="referral_educator_position_id_"|cat:$i}            
            {assign var=email value="referral_educator_email_"|cat:$i}
            {assign var=phone value="referral_educator_phone_"|cat:$i}
            <tr>    
            <td>{$form.$last_name.html|crmReplace:class:six}</td>      
            <td>{$form.$first_name.html|crmReplace:class:six}</td>
            <td>{$form.$school.html}</td>
            <td>{$form.$position.html}</td>
            <td>{$form.$email.html}</td>
            <td>{$form.$phone.html|crmReplace:class:six}</td>
           </tr>
        {/section} 

    </table>
    </td>
  </tr>
    <tr>
    <td colspan=2 class="grouplabel">
     {ts}For any of your relatives who are alumni/ae at any of our partner colleges/universities, please list their names, relationship to you and years of graduation, if known:{/ts}
    <table cellpadding=0 cellspacing=1 border=2 width="90%" class="app">
       <tr>
          <td>Partner Institution</td>
          <td>Last Name</td>
          <td>First Name</td>
          <td>Class year</td> 
          <td>Relationship</td>  

        </tr> 
           {section name=rowLoop start=1 loop=7}
             {assign var=i value=$smarty.section.rowLoop.index}
             <tr>
             {assign var=partner_institution value="alumni_partner_institution_id_"|cat:$i}  
             {assign var=last_name value="alumni_last_name_"|cat:$i}  
             {assign var=first_name value="alumni_first_name_"|cat:$i}  
             {assign var=class_year value="alumni_class_year_"|cat:$i}  
             {assign var=relationship value="alumni_relationship_"|cat:$i}  
   
             <td class="fieldlabel">{$form.$partner_institution.html}</td>  
             <td class="fieldlabel">{$form.$last_name.html}</td>
             <td class="fieldlabel">{$form.$first_name.html}</td>
             <td class="fieldlabel">{$form.$class_year.html}</td> 
             <td class="fieldlabel">{$form.$relationship.html}</td>  
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
       <tr>
          <td>Partner Institution</td>
          <td>Last Name</td>
          <td>First Name</td>
          <td>Department</td> 
          <td>Relationship</td>  

        </tr> 
          {section name=rowLoop start=1 loop=7}
             {assign var=i value=$smarty.section.rowLoop.index}
             <tr>
             {assign var=partner_institution value="employee_partner_institution_id_"|cat:$i}  
             {assign var=last_name value="employee_last_name_"|cat:$i}  
             {assign var=first_name value="employee_first_name_"|cat:$i}  
             {assign var=department value="employee_department_"|cat:$i}  
             {assign var=relationship value="employee_relationship_"|cat:$i}  
   
             <td class="fieldlabel">{$form.$partner_institution.html}</td>  
             <td class="fieldlabel">{$form.$last_name.html}</td>
             <td class="fieldlabel">{$form.$first_name.html}</td>
             <td class="fieldlabel">{$form.$department.html}</td> 
             <td class="fieldlabel">{$form.$relationship.html}</td>  
             </tr>
          {/section} 
       
        
    </table>  

        
    </td>        
    </tr>
 <tr>
    <td class="grouplabel">
        {$form.parent_grad_college_id.label}</td>
    <td class="fieldlabel">
        {$form.parent_grad_college_id.html}</td>
 </tr>
<tr>
    <td class="grouplabel">
        {$form.is_dismissed.label}</td>
    <td class="fieldlabel">
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

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}


{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_home_computer"
    trigger_value       ="1"
    target_element_id   ="is_home_internet" 
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_take_SAT_ACT"
    trigger_value       ="1"
    target_element_id   ="study_method_id" 
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}


{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_dismissed"
    trigger_value       ="1"
    target_element_id   ="explain_dismissed" 
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_convicted"
    trigger_value       ="1"
    target_element_id   ="explain_convicted" 
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="internet_access_id"
    trigger_value       ="23"
    target_element_id   ="internet_access_other" 
    target_element_type =""
    field_type          ="select"
    invert              = 0
}
