{* Quest Pre-application: Parent/Guardian Detail  section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
{if $form.is_contact_with_student }
<tr>
    <td class="grouplabel">{$form.is_contact_with_student.label}</td>
    <td class="fieldlabel">{$form.is_contact_with_student.html}</td>
</tr>
{/if}
<tr>
    <td rowspan=2 valign=top class="grouplabel" width="30%">
        <label>{ts}Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html} <br />
        {edit}{$form.first_name.label}{/edit} <span class="marker">*</span></td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
        {edit}{$form.last_name.label}{/edit} <span class="marker">*</span></td>
</tr> 
<tr>
    <td class="grouplabel">{$form.marital_status_id.label}</td>
    <td class="fieldlabel">{$form.marital_status_id.html}</td>
</tr>
<tr id="separated-year">
    <td class="grouplabel">{$form.separated_year.label}</td>
    <td class="fieldlabel">{$form.separated_year.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.is_deceased.label} <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.is_deceased.html}</td>
</tr>
<tr id="deceased_year_date">
    <td class="grouplabel">{$form.deceased_year_date.label}</td>
    <td class="fieldlabel">{$form.deceased_year_date.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.birth_date.label} <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.birth_date.html}
{edit}
     <div class="description"> 
        {include file="CRM/common/calendar/desc.tpl"}
     </div>
        {include file="CRM/common/calendar/body.tpl" dateVar=birth_date startDate=1905 endDate=currentYear}
{/edit}
    </td>
</tr>
<tr>
    <td class="grouplabel">{$form.citizenship_status.label}</td>
    <td class="fieldlabel">{$form.citizenship_status.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.birth_place.label}</td>
    <td class="fieldlabel">{$form.birth_place.html}<br />
    <label>{ts}City or State{/ts}</label></td>
</tr>
<tr>
    <td class="grouplabel">{$form.citizenship_country_id.label}</td>
    <td class="fieldlabel">{$form.citizenship_country_id.html}</td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}How long have you lived with this person?{/ts}</label></td>
    <td>
        <table border="0">
          <tr><td class="grouplabel" colspan=2>{$form.all_life.label} {$form.all_life.html}</td></tr>
          <tr id="lived_with_from_age"><td class="grouplabel"><label>{$form.lived_with_from_age.label}</label></td><td width="80%">{$form.lived_with_from_age.html}</td></tr>
          <tr id="lived_with_to_age"><td class="grouplabel"><label>{$form.lived_with_to_age.label}</label></td><td  width="80%">{$form.lived_with_to_age.html}</td></tr>
        </table>
    </td>
</tr>


<tr>
     
    <td class="grouplabel" rowspan="7">
        <label>{ts}Permanent Address{/ts} <span class="marker">*</span></td>
    <td class=fieldlabel">
       {edit}
        <input type="checkbox" name="copy_address" value="1" onClick="copyAddress()"/> {ts}Same as my Permanent address{/ts}
       {/edit}
     </td>
</tr>
<tr> 
    <td class="fieldlabel">
        {$form.location.1.address.street_address.html}<br />
        {edit}{ts}Number and Street (including apartment number){/ts}{/edit}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.supplemental_address_1.html}<br />
        {edit}{$form.location.1.address.supplemental_address_1.label}{/edit}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.city.html}<br />
        {edit}{$form.location.1.address.city.label}{/edit} <span class="marker">*</span>
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.state_province_id.html}<br />
        {edit}{ts}State (required only for USA, Canada, and Mexico) {/ts}<span class="marker">*</span>{/edit}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.postal_code.html} - {$form.location.1.address.postal_code_suffix.html}<br />
        {edit}{ts}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/ts} <span class="marker">*</span>{/edit}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.country_id.html}<br />
        {edit}{$form.location.1.address.country_id.label}{/edit} <span class="marker">*</span>
        </td>
</tr>
<tr>
    <td class="grouplabel" rowspan="2">
        <label>{ts}Permanent Telephone{/ts} <span class="marker">*</span></td>
    <td class=fieldlabel">
        {edit}
           <input type="checkbox" name="copy_phone" value="1" onClick="copyPhone()"/> {ts}Same as my Permanent telephone{/ts}<br/>
	   {/edit}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.phone.1.phone.html}<br />
        {ts}{edit}Area Code and Number. Include extension, if applicable. Include country code, if not US or Canada.{/edit}{/ts}
    </td>
</tr>


<tr>
    <td class="grouplabel">{$form.industry_id.label} <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.industry_id.html}</td>
</tr>
<tr id="job_organization">
    <td class="grouplabel">{$form.job_organization.label}</td>
    <td class="fieldlabel">{$form.job_organization.html}</td>
</tr>
<tr id="job_occupation">
    <td class="grouplabel">{$form.job_occupation.label}</td>
    <td class="fieldlabel">{$form.job_occupation.html}</td>
</tr>
<tr id="job_current_years">
    <td class="grouplabel">{$form.job_current_years.label}</td>
    <td class="fieldlabel">{$form.job_current_years.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.highest_school_level_id.label} <span class="marker">*</span></td>
    <td class="fieldlabel">{$form.highest_school_level_id.html}</td>
</tr>
<tr id="college_name">
    <td class="grouplabel">{$form.college_name.label}</td>
    <td class="fieldlabel">{$form.college_name.html}</td>
</tr>
<tr id="college_country">
    <td class="grouplabel">{$form.college_country_id.label}</td>
    <td class="fieldlabel">{$form.college_country_id.html}</td>
</tr>
<tr id="college_grad_year">
    <td class="grouplabel">{$form.college_grad_year.label}</td>
    <td class="fieldlabel">{$form.college_grad_year.html}</td>
</tr>
<tr id="college_major">
    <td class="grouplabel">{$form.college_major.label}</td>
    <td class="fieldlabel">{$form.college_major.html}</td>
</tr>
<tr id="prof_school_name">
    <td class="grouplabel">{$form.prof_school_name.label}</td>
    <td class="fieldlabel">{$form.prof_school_name.html}</td>
</tr>
<tr id="prof_school_degree">
    <td class="grouplabel">{$form.prof_school_degree_id.label}</td>
    <td class="fieldlabel">{$form.prof_school_degree_id.html}</td>
</tr>
<tr id="prof_grad_year">
    <td class="grouplabel">{$form.prof_grad_year.label}</td>
    <td class="fieldlabel">{$form.prof_grad_year.html}</td>
</tr>

<tr>
    <td class="grouplabel">{$form.description.label}</td>
    <td class="fieldlabel">{$form.description.html}</td>
</tr>
</table>

{* Include Javascript to show/hide fields based on value of other fields. *}
{* Marital status field *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="marital_status_id"
    trigger_value       ="43,44"
    target_element_id   ="separated-year" 
    target_element_type =""
    field_type          ="select"
    invert              = 0
}
{* Deceased field *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_deceased"
    trigger_value       ="1"
    target_element_id   ="deceased_year_date" 
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
{* How long lived with *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="all_life"
    trigger_value       ="1"
    target_element_id   ="lived_with_from_age|lived_with_to_age" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 1
}
{* Industry field *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="industry_id"
    trigger_value       ="47"
    target_element_id   ="job_organization|job_occupation|job_current_years" 
    target_element_type =""
    field_type          ="select"
    invert              = 1
}

{* Highest school completed field. College values, then grad school value. *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="highest_school_level_id"
    trigger_value       ="118|119|120|121|122|302"
    target_element_id   ="college_name|college_country|college_grad_year|college_major" 
    target_element_type =""
    field_type          ="select"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="highest_school_level_id"
    trigger_value       ="122|302"
    target_element_id   ="prof_school_name|prof_school_degree|prof_grad_year" 
    target_element_type =""
    field_type          ="select"
    invert              = 0
}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}



{literal}
    <script type="text/javascript">
        var field = new Array(7);
        var  original = new Array(7);
        
    
	    field[0] = "street_address";
	    field[1] = "supplemental_address_1";
	    field[2] = "city";
	    field[3] = "postal_code";
	    field[4] = "postal_code_suffix";
	    field[5] = "state_province_id";
	    field[6] = "country_id";

        for (i = 0; i < field.length; i++) {
 		  original [i] = document.getElementById("location_1_address_"+field[i]).value ;
	    }
        var origanlPhone = document.getElementById("location_1_phone_1_phone").value;      
        var fieldValue = new Array(7);

        fieldValue[0] = "{/literal}{$studentLoaction.address.street_address}{literal}";
        fieldValue[1] = "{/literal}{$studentLoaction.address.supplemental_address_1}{literal}";
        fieldValue[2] = "{/literal}{$studentLoaction.address.city}{literal}";
        fieldValue[3] = "{/literal}{$studentLoaction.address.postal_code}{literal}";
        fieldValue[4] = "{/literal}{$studentLoaction.address.postal_code_suffix}{literal}";                
        fieldValue[5] = "{/literal}{$studentLoaction.address.state_province_id}{literal}";
        fieldValue[6] = "{/literal}{$studentLoaction.address.country_id}{literal}";
        var phone     = "{/literal}{$studentLoaction.phone.1.phone}{literal}";        
 
    	function copyAddress() {
          if (document.getElementsByName("copy_address")[0].checked) {
      	  	    for (i = 0; i < field.length; i++) {
 		   	    document.getElementById("location_1_address_"+field[i]).value = fieldValue[i];
                }
	      } else {
	  	        for (i = 0; i < field.length; i++) {
 		           document.getElementById("location_1_address_"+field[i]).value = original [i];
	   	         }
	      }
	    }
      
        function copyPhone() {
            if (document.getElementsByName("copy_phone")[0].checked) {
 	   	       document.getElementById("location_1_phone_1_phone").value = phone;
			} else {
 	   	       document.getElementById("location_1_phone_1_phone").value = origanlPhone;
	        }		
	    }    

  
    </script>
{/literal}
