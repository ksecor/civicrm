{* Quest Pre-application: Personal Information section *}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</td>
</tr>
<tr>
    <td rowspan=4 valign=top class="grouplabel" width="30%">
        <label for="first_name">{ts}Legal Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html}<br />
        {$form.first_name.label}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.middle_name.html}<br />
        {$form.middle_name.label}</td>
</tr> 
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
        {$form.last_name.label}</td>
</tr> 
<tr>
    <td class="fieldlabel">
        {$form.suffix_id.html}<br />
        {$form.suffix_id.label}</td>
</tr> 
<tr>
    <td class="grouplabel">
        {$form.nick_name.label}</td>
    <td class="fieldlabel">
        {$form.nick_name.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.gender_id.label}</td>
    <td class="fieldlabel">
        {$form.gender_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.location.1.email.1.email.label}</td>
    <td class="fieldlabel">
        {$form.location.1.email.1.email.html}</td>
</tr>
<tr>
    <td class="grouplabel" rowspan="6">
        {ts}Permanent Address{/ts} <span class="marker">*</span></td>
    <td class="fieldlabel">
        {$form.location.1.address.street_address.html}<br />
        {ts}Number and Street (including apartment number){/ts}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.supplemental_address_1.html}<br />
        {$form.location.1.address.supplemental_address_1.label}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.city.html}<br />
        {$form.location.1.address.city.label}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.state_province_id.html}<br />
        {ts}State (required only for USA, Canada, and Mexico) {/ts}<span class="marker">*</span>
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.postal_code.html} - {$form.location.1.address.postal_code_suffix.html}<br />
        {ts}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/ts} <span class="marker">*</span>
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.country_id.html}<br />
        {$form.location.1.address.country_id.label}
        </td>
</tr>
<tr>
    <td class="grouplabel">
        {ts}Permanent Telephone{/ts} <span class="marker">*</span></td>
    <td class="fieldlabel">
        {$form.location.1.phone.1.phone_type.html}{$form.location.1.phone.1.phone.html}<br />
        {ts}Area Code and Number. Include extension, if applicable. Include country code, if not US or Canada.{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel" rowspan="7">
        {ts}Mailing Address{/ts} <span class="marker">*</span></td>
    <td class="fieldlabel">
        <input type="checkbox" name="copy_address" value="1" /> {ts}Check if same as Permanent Address{/ts}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.street_address.html}<br />
        {ts}Number and Street (including apartment number){/ts}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.supplemental_address_1.html}<br />
        {$form.location.2.address.supplemental_address_1.label}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.city.html}<br />
        {$form.location.2.address.city.label}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.state_province_id.html}<br />
        {ts}State (required only for USA, Canada, and Mexico) <span class="marker">*</span>{/ts}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.postal_code.html} - {$form.location.2.address.postal_code_suffix.html}<br />
        {ts}USA Zip Code (Zip Plus 4 if available) OR International Postal Code <span class="marker">*</span>{/ts}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.country_id.html}<br />
        {$form.location.2.address.country_id.label}
        </td>
</tr>
<tr>
    <td class="grouplabel">
        {ts}Mailing Telephone{/ts}<span class="marker">*</span></td>
    <td class="fieldlabel">
        {$form.location.2.phone.1.phone_type.html}{$form.location.2.phone.1.phone.html}<br />
        {ts}Area Code and Number. Include extension, if applicable. Include country code, if not US or Canada.{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {ts}Alternate Telephone <span class="marker">*</span></td>
    <td class="fieldlabel">
        {$form.location.2.phone.2.phone_type.html}{$form.location.2.phone.2.phone.html}<br />
        {ts}Area Code and Number. Include extension, if applicable. Include country code, if not US or Canada.{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.citizenship_status_id.label}</td>
    <td class="fieldlabel">
        {$form.citizenship_status_id.html}<br />
        {ts}<strong>If you were born in the U.S., you are most likely a U.S. citizen, not a permanent resident.</strong>{/ts}<br />
        {ts}The information collected by Quest in response to this question will not be forwarded to any government agency and will be used solely for the purpose of your application to Quest. Please note that Quest does not require students to be citizens of the United States.{/ts}<br />
        <a href="javascript:popUp('http://questscholars.stanford.edu/appFAQans.htm#q6')">{ts}Please click here for more information{/ts}</a>.{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.citizenship_country_id.label}</td>
    <td class="fieldlabel">
        {$form.citizenship_country_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.ethnicity_id_1.label}
        {ts}Quest Scholars seeks to enroll a diverse student body. Please select a response from the following list. Completion of this information is appreciated, but not required.{/ts}
    </td>
    <td class="fieldlabel">
        {$form.ethnicity_id_1.html}
        <div id="ethnicity_id_2[show]">{$ethnicity_id_2.show}{ts}add another Race/Ethnicity{/ts}</div>
        <div id="ethnicity_id_1">{$form.ethnicity_id_2.html}</div>
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.birth_date.label}</td>
    <td class="fieldlabel">
        {$form.birth_date.html}
        <div class="description"> 
            {include file="CRM/common/calendar/desc.tpl"}
        </div>
        {include file="CRM/common/calendar/body.tpl" dateVar=birth_date startDate=1905 endDate=currentYear}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.home_area_id.label}</td>
    <td class="fieldlabel">
        {$form.home_area_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.growup_country_id.label}</td>
    <td class="fieldlabel">
        {$form.growup_country_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.years_in_us.label}</td>
    <td class="fieldlabel">
        {$form.years_in_us.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.number_siblings.label}</td>
    <td class="fieldlabel">
        {$form.number_siblings.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.nationality_country_id.label}</td>
    <td class="fieldlabel">
        {$form.nationality_country_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.first_language.label}</td>
    <td class="fieldlabel">
        {$form.first_language.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.primary_language.label}</td>
    <td class="fieldlabel">
        {$form.primary_language.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.high_school_grad_year.label}</td>
    <td class="fieldlabel">
        {$form.high_school_grad_year.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.parent_grad_college_id.label}</td>
    <td class="fieldlabel">
        {$form.parent_grad_college_id.html}</td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
