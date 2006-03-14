{* Quest Pre-application: High School Information section *}

{* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>

{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}

<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>	    	
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.title}</td>
</tr>
<tr>
<td>We realize our applicants come from a diverse group of secondary schools. Please tell us about your particular school by answering the following questions.</td>
</tr>
<tr>
    <td class="grouplabel">{$form.organization_name.label}<span class="marker">*</span></td>
    <td>                   {$form.organization_name.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.custom_1.label}</td>
    <td>                   {$form.custom_1.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.date_of_entry.label}<span class="marker">*</span></td>
    <td>                   {$form.date_of_entry.html}</td>
</tr>
<tr>
    <td class="grouplabel" rowspan="4">{ts}School Address{/ts} <span class="marker">*</span></td>
    <td> {$form.location.1.address.street_address.html}<br />
         {ts}Number and Street (including apartment number){/ts}</td>
</tr>
<tr>
    <td>{$form.location.1.address.city.html}<br/></td>
</tr>
<tr>
    <td>{$form.location.1.address.postal_code.html} - {$form.location.1.address.postal_code_suffix.html}<br />
        {ts}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/ts} <span class="marker">*</span></td>
</tr>
<tr>
    <td>{$form.location.1.address.country_id.html}</td>
</tr>
<tr>
    <td>{$form.location.1.phone.1.phone.label}</td>
    <td>{$form.location.1.phone.1.phone.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.custom_2.label}<span class="marker">*</span></td>
    <td>{$form.custom_2.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.custom_3.label}<span class="marker">*</span></td>
    <td> {$form.custom_3.html}</td>
</tr>
<tr>
    <td> If you attended another high school prior to the one above, click to add another</td>

</table>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>

