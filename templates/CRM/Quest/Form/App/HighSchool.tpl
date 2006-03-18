{* Quest Pre-application: High School Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>
<tr>
    <td>{ts}We realize our applicants come from a diverse group of secondary schools. Please tell us about your particular school by answering the following questions.{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.organization_name.label}</td>
    <td>                   {$form.organization_name.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.custom_1.label}</td>
    <td>                   {$form.custom_1.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.date_of_entry.label}</td>
    <td>                   {$form.date_of_entry.html}</td>
</tr>
<tr>
    <td class="grouplabel" rowspan="5">{ts}School Address{/ts}</td>
    <td> {$form.location.1.address.street_address.html}<br />
         {ts}Number and Street (including apartment number){/ts}</td>
</tr>
<tr>
    <td>{$form.location.1.address.city.html}<br/></td>
</tr>
<tr>
    <td>{$form.location.1.address.state_province_id.html}<br/></td>
</tr>
<tr>
    <td>{$form.location.1.address.postal_code.html} - {$form.location.1.address.postal_code_suffix.html}<br />
        {ts}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/ts}</td>
</tr>
<tr>
    <td>{$form.location.1.address.country_id.html}</td>
</tr>
<tr>
    <td>{$form.location.1.phone.1.phone.label}</td>
    <td>{$form.location.1.phone.1.phone.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.custom_2.label}</td>
    <td>{$form.custom_2.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.custom_3.label}</td>
    <td> {$form.custom_3.html}</td>
</tr>
<tr>
    <td> If you attended another high school prior to the one above, click to add another</td>

</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

